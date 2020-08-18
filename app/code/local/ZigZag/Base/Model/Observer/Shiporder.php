<?php

/**
 * Class ZigZag_Base_Model_Observer_Shiporder
 */
class ZigZag_Base_Model_Observer_Shiporder
{
    /**
     * @param $observer
     * @return $this|void
     * @throws Exception
     */
    public function shipOrder($observer){
        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($shipment->getOrder()->getId());

        $carrierCode = $order->getShippingMethod(true)->getCarrierCode();
        $modelName = $carrierCode . '/carrier_' . str_replace('zigzag', '', $carrierCode);
        $model = Mage::getModel($modelName);

        if ($model && Mage::helper('zigzagbase/shipping')->isCarrierInOrder($order, $model->getCarrierCode())) {
            $result = Mage::getModel('zigzagbase/service_ws_insertshipment')->insert($order, $model, false);
            if ($result) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($result)
                    ->setCarrierCode($carrierCode)
                    ->setTitle(Mage::helper('zigzagbase')->getConfig($model::ZIGZAG_SHIPPING_NAME_PATH));
                $shipment->addTrack($track);
                $shipment->sendEmail();
            }
        }

        return $this;
    }
}
