<?php

/**
 * Class ZigZag_Reverse_Model_Observer_Salesorderplaceafter
 */
class ZigZag_Reverse_Model_Observer_Salesorderplaceafter
{
    /**
     * @param $observer
     * @return $this
     */
    public function shipOrder($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        $model = Mage::getModel('zigzagreverse/carrier_reverse');
        if (Mage::helper('zigzagbase/shipping')->isCarrierInOrder($order, $model->getCarrierCode())) {
            $result = Mage::getModel('zigzagbase/service_ws_insertshipment')->insert($order, $model);
            if ($result) {
                Mage::helper('zigzagbase/shipping')->shipOrder($order, $result);
            }
        }

        return $this;
    }
}