<?php

/**
 * Class ZigZag_Base_Model_Observer_Shipmentcreated
 */
class ZigZag_Base_Model_Observer_Shipmentcreated
{
    public function setDeliveryTime($observer){
        $order = $observer->getEvent()->getOrder();

        /** @var string $trackingNumber */
        $trackingNumber = $observer->getEvent()->getTrackingNumber();

        if ($order->getZigzagDeliveryFrom()) {
            $result = Mage::getModel('zigzagbase/service_ws_shipmentdelivery')->set($order, $trackingNumber);
            if ($result) {
                $requestedDelivery = date('Y-m-d H:i', strtotime($order->getZigzagDeliveryFrom())) . '-' . date('H:i', strtotime($order->getZigzagDeliveryTo()));
                $order->addStatusHistoryComment('Delivery Date Sent To ZigZag Successfully ('. $requestedDelivery .')');
                $order->save();
            }
        }
    }
}