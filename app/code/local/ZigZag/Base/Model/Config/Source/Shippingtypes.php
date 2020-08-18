<?php

class ZigZag_Base_Model_Config_Source_Shippingtypes
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = Mage::helper('zigzagbase')->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_FULL_PATH);
        return $options ? json_decode($options, true) : [];
    }
}