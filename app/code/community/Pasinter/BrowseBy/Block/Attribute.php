<?php

class Pasinter_BrowseBy_Block_Attribute extends Pasinter_BrowseBy_Block_Attribute_Abstract
{

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Block/Mage_Core_Block_Abstract#_prepareLayout()
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $label = $this->getCurrentAttributeLabel();
        $value = $this->getCurrentAttributeText();

        $title = $label . ' - ' . $value;

        if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbBlock->addCrumb('home', array(
                'label' => Mage::helper('browseby')->__('Home'),
                'title' => Mage::helper('browseby')->__('Go to Home Page'),
                'link' => Mage::getBaseUrl()
            ));
            $breadcrumbBlock->addCrumb('attribute', array(
                'label' => $label,
                'title' => $label
            ));
            $breadcrumbBlock->addCrumb('value', array(
                'label' => $value,
                'title' => $value
            ));
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            if ($title) {
                $headBlock->setTitle($title);
            }
        }

        return $this;
    }

    /**
     * Returns child html of product list
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    /**
     * Retrieves a CMS static block if it exists.
     * The CMS Block ID should be in the format:
     * attr-<attribute-code>-<attribute-value> - no spaces, all lowercase chars
     *
     * @return string CMS Block HTML
     */
    public function getCmsBlockHtml()
    {
        if (!$this->getData('cms_block_html')) {
            $blockid = $this->getCmsBlockId();
            $html = $this->getLayout()->createBlock('cms/block')->setBlockId($blockid)->toHtml();
            $this->setData('cms_block_html', $html);
        }
        return $this->getData('cms_block_html');
    }

    /**
     * Gets the CMS block ID, based on attribute code and attribute text
     *
     * @return <type>
     */
    public function getCmsBlockId()
    {
        if (!$this->getData('cms_block_id')) {
            $replacementValues = array(" ", "#", "!", "&", ".");
            $blockid = $this->getCurrentAttribute()->getAttributeCode() . ' ' . $this->getCurrentAttributeText();
            $blockid = strtolower($this->_normalizeString($blockid));
            $blockid = 'attr-' . str_replace($replacementValues, "-", $blockid);
            $this->setData('cms_block_id', $blockid);
        }
        return $this->getData('cms_block_id');
    }

    private function _normalizeString($input)
    {
        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r',
        );

        return strtr($input, $table);
    }

}
