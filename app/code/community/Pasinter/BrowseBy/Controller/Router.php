<?php

class Pasinter_BrowseBy_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{

    /**
     * Matches the request URL. If matched, sets request
     * parameters for correct module/controller/action.
     *
     * @param Zend_Controller_Request_Http $request
     * @return boolean
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        $urlPrefix = Mage::helper('browseby')->getUrlPrefix();

        if (!$urlPrefix) {
            return false;
        }

        $identifier = trim($request->getPathInfo(), '/');
        $parts = explode('/', $identifier);

        if (count($parts) < 3 || $parts[0] != $urlPrefix) {
            return false;
        }

        $request->setModuleName('browseby')
                ->setControllerName('attribute')
                ->setActionName('browse')
                ->setParams(array(
                    'code' => $parts[1],
                    'value' => $parts[2])
        );
        
//        Mage::register('current_attribute', $parts[1]);
//        Mage::register('current_attribute_value', $attrValue);
            
        $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier
        );

        return true;
    }

}
