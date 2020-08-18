<?php
class ZigZag_Base_Model_Config_Comment_Shippingtypes extends Mage_Core_Model_Config_Data
{
    public function getCommentText(Mage_Core_Model_Config_Element $element, $currentValue)
    {
        $shippingTypes = Mage::helper('zigzagbase')->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_PATH);

        return ($shippingTypes) ? Mage::helper('shipping')->__('This list is controlled by ZigZag (Save configuration to refresh)')
            : Mage::helper('shipping')->__('Please provide username, password and save configuration in order to populate this list. If credentials are set and this list is still empty, please contact ZigZag customer support.');
    }
}