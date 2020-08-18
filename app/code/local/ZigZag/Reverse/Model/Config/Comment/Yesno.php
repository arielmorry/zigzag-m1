<?php

class ZigZag_Reverse_Model_Comment_Source_Yesno extends Mage_Core_Model_Config_Data
{
    /**
     * @param Mage_Core_Model_Config_Element $element
     * @param $currentValue
     * @return string
     */
    public function getCommentText(Mage_Core_Model_Config_Element $element, $currentValue)
    {
        $isEnabled = Mage::helper('zigzagbase')->isShippingTypeEnabledByCarrier(ZigZag_Reverse_Model_Carrier_Reverse::ZIGZAG_SHIPPING_TYPE_CODE);
        if (!$isEnabled) {
            return Mage::helper('adminhtml')->__('Shipping method <b>Disabled</b> by ZigZag.');
        }
    }
}