<?php

class Pasinter_BrowseBy_Block_Product_List extends Mage_Catalog_Block_Product_List
{
   
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            //$this->_productCollection = Mage::getModel('catalog/product')->getCollection();
            //echo $this->_productCollection->getSelectSql();exit;
        }
        //echo parent::_getProductCollection()->getSelectSql();exit;
        if (is_null($this->_productCollection)) {
            $attribute = Mage::registry('current_attribute');
            $attribute_code = $attribute->getData('attribute_code');
            $value = Mage::registry('current_attribute_value');
            $storeId = Mage::app()->getStore()->getStoreId();
            $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
            
            // Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
            $this->_productCollection = Mage::getModel('catalog/product')->getCollection();
            
            $this->_productCollection
                //->addUrlRewrite()
                ->addPriceData()
                ->addAttributeToSelect('status')
                ->addAttributeToFilter('small_image',array('notnull'=>'','neq'=>'no_selection'))

                // visible only
                  
                ->addAttributeToFilter('visibility', array(
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                ))
                  
                // in stock only   
                
                ->joinField('stock_status','cataloginventory/stock_status','stock_status',
                  'product_id=entity_id', array(
                  'stock_status' => Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK, 'website_id' => $websiteId
               ))
              
               ->addStoreFilter()
               
               ->addIdFilter($this->getFilteredIds())

               
            ;
           
            
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            

        }

        return $this->_productCollection;
    }
    
    protected function getFilteredIds()
    {
        $attribute = Mage::registry('current_attribute');
        $attribute_code = $attribute->getData('attribute_code');
        $value = Mage::registry('current_attribute_value');
        
        $query = "
            SELECT e.entity_id
            FROM catalog_product_entity e
            LEFT JOIN catalog_product_relation cpr ON cpr.parent_id = e.entity_id
            LEFT JOIN `catalog_product_entity_int` AS `_table_manufacturer` ON (_table_manufacturer.entity_id = e.entity_id) AND (_table_manufacturer.attribute_id='66') 



            WHERE 
            _table_manufacturer.value = $value
                OR


            (
                    SELECT COUNT(le.entity_id) FROM catalog_product_entity le
                    INNER JOIN catalog_product_relation lcpr ON lcpr.child_id = le.entity_id
                    INNER JOIN `catalog_product_entity_int` AS `_table_manufacturer_linked` 
                            ON (_table_manufacturer_linked.entity_id = le.entity_id) AND (_table_manufacturer_linked.attribute_id='66') AND (_table_manufacturer_linked.value = $value) 
                    WHERE lcpr.parent_id = e.entity_id
            ) > 0


            GROUP BY e.entity_id
        ";
        
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        //echo $query;
        $result = $write->query($query);
        
        
        $ids = array();
        
        
        
        foreach($result->fetchAll() as $row) {
            $ids[] = $row['entity_id'];
        }
        
        //var_dump($ids);
        
        return $ids;
    }
    
    
    
    protected function getFilterByIdsExpression()
    {
        $attribute = Mage::registry('current_attribute');
        $attribute_code = $attribute->getData('attribute_code');
        $value = Mage::registry('current_attribute_value');
            
        $query = "
            SELECT e.entity_id
            FROM catalog_product_entity e
            LEFT JOIN catalog_product_link cpl ON cpl.product_id = e.entity_id AND cpl.link_type_id = 1
            LEFT JOIN `catalog_product_entity_int` AS `_table_manufacturer` ON (_table_manufacturer.entity_id = e.entity_id) AND (_table_manufacturer.attribute_id='66') AND (_table_manufacturer.value=$value) 
            
            WHERE 

            IF (cpl.linked_product_id IS NULL, 
                    _table_manufacturer.value = $value, 

                    (
                            SELECT COUNT(le.entity_id) FROM catalog_product_entity le
                            INNER JOIN catalog_product_link lcpl ON lcpl.linked_product_id = le.entity_id AND lcpl.link_type_id = 1
                            INNER JOIN `catalog_product_entity_int` AS `_table_manufacturer_linked` ON (_table_manufacturer_linked.entity_id = le.entity_id) 
                                                    AND (_table_manufacturer_linked.attribute_id= 66 ) AND (_table_manufacturer_linked.value=$value) 
                            WHERE lcpl.product_id = e.entity_id
                            -- WHERE 
                    ) > 0 OR _table_manufacturer.value = $value
            )

        GROUP BY e.entity_id
        ";
        
        return $query;
    }
    
    
     
    
    public function getAvailableOrders()
    {
        //$orders = parent::getAvailableOrders();
        
        $orders = array(
            'name'  => 'Name',
            'price' => 'Price'
        );
        
        return $orders;
    }

}