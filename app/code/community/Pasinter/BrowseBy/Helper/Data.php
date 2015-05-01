<?php

class Pasinter_BrowseBy_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Get URL to browse product by attribute value
     *
     * @param string|Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string|array $value
     * @return string
     */
    public function getBrowseUrl($attribute, $value)
    {
        if (is_string($attribute)) {
            $attribute = Mage::getResourceModel('catalog/product')->getAttribute($attribute);
        }

        switch ($attribute->getFrontendInput()) {
            case 'select':
            case 'multiselect':
                $optionText = $attribute->getSource()->getOptionText($value);
                $slug = preg_replace('/[^a-z0-9]/', '-', strtolower($optionText));
                $value = $value . '/' . $slug;
                break;
            case 'price':
                if (is_array($value) && isset($value['from']) && isset($value['to'])) {
                    $value = $value['from'] . ',' . $value['to'];
                }
                break;
            default:
                Mage::throwException('Only select, multiselect or price attributes are accepted.');
        }

        return Mage::getBaseUrl() . $this->getUrlPrefix() . '/' . $attribute->getAttributeCode() . '/' . $value;
    }

    /**
     * Get prefix for all BrowseBy URLs
     *
     * @return string
     */
    public function getUrlPrefix()
    {
        return Mage::getStoreConfig('catalog/browse_by/url_prefix');
    }

    public function getProductIdsWithManufacturer()
    {
        $value = Mage::registry('current_attribute_value');

        $query = "
            SELECT e.entity_id
            FROM catalog_product_entity e
            LEFT JOIN catalog_product_relation cpr ON cpr.parent_id = e.entity_id
            LEFT JOIN `catalog_product_entity_int` AS `_table_manufacturer` ON (_table_manufacturer.entity_id = e.entity_id) AND (_table_manufacturer.attribute_id='66') 



            WHERE 
            _table_manufacturer.value IS NOT NULL
                OR


            (
                    SELECT COUNT(le.entity_id) FROM catalog_product_entity le
                    INNER JOIN catalog_product_relation lcpr ON lcpr.child_id = le.entity_id
                    INNER JOIN `catalog_product_entity_int` AS `_table_manufacturer_linked` 
                            ON (_table_manufacturer_linked.entity_id = le.entity_id) AND (_table_manufacturer_linked.attribute_id='66') AND (_table_manufacturer_linked.value IS NOT NULL) 
                    WHERE lcpr.parent_id = e.entity_id
            ) > 0


            GROUP BY e.entity_id
        ";

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        //echo $query;
        $result = $write->query($query);


        $ids = array();

        foreach ($result->fetchAll() as $row) {
            $ids[] = $row['entity_id'];
        }

        return $ids;
    }

}
