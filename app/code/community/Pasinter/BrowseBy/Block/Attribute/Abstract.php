<?php

abstract class Pasinter_BrowseBy_Block_Attribute_Abstract extends Mage_Core_Block_Template
{

    /**
     * Get current attribute
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getCurrentAttribute()
    {
        return Mage::registry('current_attribute');
    }

    /**
     * Get current attribute value
     *
     * @return mixed
     */
    public function getCurrentAttributeValue()
    {
        return Mage::registry('current_attribute_value');
    }

    /**
     * Get current attribute label
     *
     * @return string
     */
    public function getCurrentAttributeLabel()
    {
        return $this->getCurrentAttribute()->getFrontendLabel();
    }

    /**
     * Get current attribute text value
     *
     * @return string
     */
    public function getCurrentAttributeText()
    {
        $attribute = $this->getCurrentAttribute();
        $value = $this->getCurrentAttributeValue();

        if ($attribute->getFrontendInput() == 'price') {
            list($index, $range) = explode(',', $value);
            $store = Mage::app()->getStore();
            $fromPrice = $store->formatPrice(($index - 1) * $range, false);
            $toPrice = $store->formatPrice($index * $range, false);
            return Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice);
        } else {
            return $attribute->getSource()->getOptionText($value);
        }
    }

}
