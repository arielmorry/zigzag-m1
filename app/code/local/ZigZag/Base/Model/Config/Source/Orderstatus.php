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
        $allow = [
            Mage_Sales_Model_Order::STATE_NEW,
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
            Mage_Sales_Model_Order::STATE_PROCESSING,
            Mage_Paypal_Model_Info::PAYMENTSTATUS_PENDING
        ];

        $options = Mage::getModel('sales/order_status')->getCollection()->toOptionArray();
        foreach ($options as $k => $option) {
            if (!in_array($option['value'], $allow)) {
                unset($options[$k]);
            }
        }

        return $options;
    }
}