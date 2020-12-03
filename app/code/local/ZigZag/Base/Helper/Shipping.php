<?php

class ZigZag_Base_Helper_Shipping extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Sales_Model_Order $order
     * @param int $trackingNumber
     * @throws Exception
     */
    public function shipOrder($order, $trackingNumber = 0)
    {
        // Save order to repository so we can retrieve full info
        $order->save();
        $carrier = $order->getShippingCarrier();
        $createNewShipment = !$order->hasShipments();

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        if (!$createNewShipment) {
            $shipment = $order->getShipmentsCollection()->getFirstItem();
        } else {
            if (!$order->canShip()) {
                $msg = "Error while creating shipment for ZigZag. Order Can't be shipped\nOrder Number: {$order->getIncrementId()}";
                Mage::helper('zigzagbase')->log('error', $msg, null, true);
                return;
            }
            $shipment = Mage::getModel('sales/convert_order')->toShipment($order);
        }


        try {
            if ($createNewShipment) {
                foreach ($order->getAllItems() as $orderItem) {
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }

                    $qtyShipped = $orderItem->getQtyToShip();
                    $shipmentItem = Mage::getModel('sales/convert_order')->itemToShipmentItem($orderItem)->setQty($qtyShipped);

                    $shipment->addItem($shipmentItem);
                }

                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);


                // Save Order and Shipment
                $shipment->save();
                $shipment->getOrder()->save();

                // Add Notification and Tracking
                $title = $order->getShippingDescription();
                if (defined(get_class($carrier) . '::ZIGZAG_SHIPPING_NAME_PATH')) {
                    $title = Mage::helper('zigzagbase')->getConfig($carrier::ZIGZAG_SHIPPING_NAME_PATH);
                }
            }

            $track = Mage::getModel('sales/order_shipment_track')
                ->setNumber(
                    $trackingNumber
                )->setCarrierCode(
                    $carrier->getCarrierCode()
                )->setTitle(
                    $title
                );
            $shipment->addTrack($track);
            $shipment->sendEmail();

            // Save Shipment again
            $shipment->save();
        } catch (Exception $e) {
            Mage::helper('zigzagbase')->log('error', $order->getId());
            $msg = "Error while saving shipment for ZigZag\nError Code: {$e->getCode()}\nError Message: {$e->getMessage()}\nOrder Number: {$order->getIncrementId()}";
            Mage::helper('zigzagbase')->log('error', $msg, null, true);
        }
    }

    /**
     * @param $order
     * @param $carrierCode
     * @return bool
     */
    public function isCarrierInOrder($order, $carrierCode)
    {
        $orderCarrierCode = $order->getShippingMethod(true)->getCarrierCode();
        return $carrierCode == $orderCarrierCode;
    }

    /**
     * @param $trackingNumber
     * @param $code
     * @param $title
     * @return mixed
     */
    public function getTrackingInfo($trackingNumber, $code, $title)
    {
        $status = '';

        $statuses = Mage::getModel('zigzagbase/service_ws_shipmentstatus')->get($trackingNumber);
        if ($statuses) {
            $latestStatusData = end($statuses);
            $status = property_exists($latestStatusData, 'TEOR_STATUSCODE') ? $latestStatusData->TEOR_STATUSCODE : '';
        }

        $tracking = Mage::getModel('shipping/tracking_result_status');
        $tracking->setData([
            'carrier'       => $code,
            'carrier_title' => $title,
            'tracking'      => $trackingNumber,
            'track_summary' => $status
        ]);
        return $tracking;
    }
}