<?php

class Pasinter_BrowseBy_Block_Attribute_Navigation extends Pasinter_BrowseBy_Block_Attribute_Abstract
{

    /**
     * Layer Filter
     *
     * @var Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected $_filter;
    protected $_label;

    /**
     * Called from layout action. Sets attribue for browse options
     *
     * @param string $attribute
     */
    public function setBrowseAttribute($attribute)
    {
        if (is_string($attribute)) {
            $attribute = Mage::getResourceModel('catalog/product')
                    ->getAttribute($attribute);
        }
        $this->setData('browse_attribute', $attribute);
    }

    /**
     * Get attribute label
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->_label) {
            return $this->_label;
        }
        return $this->getBrowseAttribute()->getFrontendLabel();
    }

    public function setLabel($label)
    {
        $this->_label = $label;
    }

    /**
     * Get navigation items
     *
     * @return array
     */
    public function getItems()
    {

        $attribute = $this->getBrowseAttribute();

        $product = Mage::getModel('catalog/product');

        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($product->getResource()->getTypeId())
                ->addFieldToFilter('attribute_code', $attribute->getData('attribute_code'))
                ->load(false)
        ;

        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());

        $items = $attribute->getSource()->getAllOptions(false);

        $attributeIdsWithProducts = $this->getItemsIdsWithProducts();
        $active = array();
        foreach ($items as $item) {
            if (in_array($item['value'], $attributeIdsWithProducts)) {
                $active[] = $item;
            }
        }

        usort($active, array($this, 'cmpItemsByLabel'));

        return $active;
    }

    protected function getItemsIdsWithProducts()
    {
        $attribute = $this->getBrowseAttribute();
        $attribute_code = $attribute->getData('attribute_code');

        $products = Mage::getModel('catalog/product')->getCollection();
        $helper = $this->helper('browseby');

        $products
                ->addAttributeToSelect($attribute_code)
                ->addAttributeToFilter($attribute_code, array('neq' => 'blaaaah'))
                ->addStoreFilter()
                // active only
                ->addAttributeToFilter(
                        'status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                )

                // in stock only   
                ->joinField('stock_status', 'cataloginventory/stock_status', 'stock_status', 'product_id=entity_id', array(
                    'stock_status' => Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK
                ))
        //->addFieldToFilter('store_id', Mage::app()->getStore()->getId())
        ;

        $query = "SELECT DISTINCT(`$attribute_code`) FROM (" . $products->getSelectSql(true) . ") p";

        //echo $query;exit;
        //echo $products->getSelectSql(true);exit;


        $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);

        $ids = array();

        foreach ($data as $row) {
            $ids[] = $row[$attribute_code];
        }

        return $ids;
    }

    public function cmpItemsByLabel($a, $b)
    {
        if ($a['label'] == $b['label']) {
            return 0;
        }

        return ($a['label'] < $b['label']) ? -1 : 1;
    }

    /**
     * Checks for current value of attribute
     *
     * @param string $value
     * @return boolean
     */
    public function isValueActive($value)
    {
        $currentAttribute = $this->getCurrentAttribute();
        $browseAttribute = $this->getBrowseAttribute();

        if ($currentAttribute && $browseAttribute && ($currentAttribute->getAttributeCode() == $this->getBrowseAttribute()->getAttributeCode()) && $this->getCurrentAttributeValue() == $value) {
            return true;
        }
        return false;
    }

    /**
     * Get URL to browse products by attribute value
     *
     * @param string $value
     * @return string
     */
    public function getBrowseUrl($value)
    {
        return $this->helper('browseby')->getBrowseUrl($this->getBrowseAttribute(), $value);
    }

}
