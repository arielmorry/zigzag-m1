<?php

class ZigZag_Base_Model_Config_Source_Orderstatus
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        $ignore = [
            Mage_Sales_Model_Order::STATE_CANCELED,
            Mage_Sales_Model_Order::STATE_CLOSED,
            Mage_Sales_Model_Order::STATE_COMPLETE,
            Mage_Sales_Model_Order::STATUS_FRAUD,
            Mage_Sales_Model_Order::STATE_HOLDED,
            Mage_Paypal_Model_Info::ORDER_STATUS_CANCELED_REVERSAL,
            Mage_Paypal_Model_Info::ORDER_STATUS_REVERSED,
        ];

        $options = Mage::getModel('sales/order_status')->getCollection()->toOptionArray();
        foreach ($options as $k => $option) {
            if (in_array($option['value'], $ignore)) {
                unset($options[$k]);
            }
        }

        return $options;
    }
}