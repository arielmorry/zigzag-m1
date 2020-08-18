<?php

/**
 * Class ZigZag_Regular_Model_Observer_Salesorderplaceafter
 */
class ZigZag_Regular_Model_Observer_Salesorderplaceafter
{
    /**
     * @param $observer
     * @return $this
     */
    public function shipOrder($observer){
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        $model = Mage::getModel('zigzagregular/carrier_regular');
        if (Mage::helper('zigzagbase/shipping')->isCarrierInOrder($order, $model->getCarrierCode())) {
            $result = Mage::getModel('zigzagbase/service_ws_insertshipment')->insert($order, $model);
            if ($result) {
                Mage::helper('zigzagbase/shipping')->shipOrder($order, $result);
            }
        }

        return $this;
    }
}