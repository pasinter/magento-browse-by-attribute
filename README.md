# magento-browse-by-attribute
Magento Browse by Attribute Extension

Attributes Dropdown List
_______
To display a dropdown with manufacturers, add the following xml:
```xml
<block type="browseby/attribute_navigation" name="browseby.manufacturer" template="browseby/attribute/dropdown.phtml">
    <action method="setBrowseAttribute"><attribute>manufacturer</attribute></action>
    <action method="setLabel"><label>Brands</label></action>
</block>
```