<?php

/**
 * Class ZigZag_Base_Model_Observer_Saveorder
 */
class ZigZag_Base_Model_Observer_Saveorder
{
    public function setDeliveryTime($observer)
    {
        $event = $observer->getEvent();
        $deliveryDate = Mage::app()->getFrontController()->getRequest()->getParam('zigzag-availability');

        $quote = $event->getQuote();
        if ($deliveryDate && $deliveryDate != -1) {
            $data = explode('_', $deliveryDate);
            $quote->setZigzagDeliveryFrom(date('Y-m-d H:i:s', strtotime($data[0] . ' ' . $data[1])));
            $quote->setZigzagDeliveryTo(date('Y-m-d H:i:s', strtotime($data[0] . ' ' . $data[2])));
        } else {
            $quote->setZigzagDeliveryFrom(null);
            $quote->setZigzagDeliveryTo(null);
        }
    }
}