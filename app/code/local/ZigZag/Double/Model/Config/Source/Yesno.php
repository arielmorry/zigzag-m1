<?php

class ZigZag_Double_Model_Config_Source_Yesno
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $isEnabled = Mage::helper('zigzagbase')->isShippingTypeEnabledByCarrier(ZigZag_Double_Model_Carrier_Double::ZIGZAG_SHIPPING_TYPE_CODE);
        $options = [
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('No')]
        ];

        if ($isEnabled) {
            $options[] =  ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Yes')];
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $isEnabled = Mage::helper('zigzagbase')->isShippingTypeEnabledByCarrier(ZigZag_Double_Model_Carrier_Double::ZIGZAG_SHIPPING_TYPE_CODE);
        $options = [
            0 => Mage::helper('adminhtml')->__('No')
        ];

        if ($isEnabled) {
            $options[1] =  Mage::helper('adminhtml')->__('Yes');
        }
        return $options;
    }
}