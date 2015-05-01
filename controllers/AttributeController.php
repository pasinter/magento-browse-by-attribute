<?php

class Pasinter_BrowseBy_AttributeController extends Mage_Core_Controller_Front_Action
{

    /**
     * Browse action
     */
    public function browseAction()
    {
        $attrCode = $this->getRequest()->getParam('code');
        $attrValue = $this->getRequest()->getParam('value');

        $attribute = Mage::getResourceModel('catalog/product')
                ->getAttribute($attrCode);

        /**
         * If attribute is found in parameters, add attribute
         * and value to registry, else return 404 not found page.
         */
        if ($attribute) {
            Mage::register('current_attribute', $attribute);
            Mage::register('current_attribute_value', $attrValue);

            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        } else {
            $this->_forward('noroute');
        }
    }

}
