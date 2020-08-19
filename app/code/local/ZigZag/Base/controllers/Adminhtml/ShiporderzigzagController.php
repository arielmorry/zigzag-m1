<?php

class ZigZag_Base_Adminhtml_ShiporderzigzagController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order) {
            $result = Mage::getModel('zigzagbase/service_ws_insertshipment')->insert($order);
            if ($result) {
                Mage::helper('zigzagbase/shipping')->shipOrder($order, $result);
                Mage::getSingleton('core/session')->addSuccess('ZigZag Module: Shipment Created Successfully. Tracking Number ' . $result);
            } else {
                Mage::getSingleton('core/session')->addError('ZigZag Module: Error occurred. Please Check Error Log');
            }
        } else {
            Mage::getSingleton('core/session')->addError('ZigZag Module: Order Not Found');
        }

        $this->_redirectReferer();
    }

    public function massAction()
    {
        $errorOrders = [];
        $successOrders = [];

        $orderIds = Mage::app()->getRequest()->getParam('order_ids');
        foreach ($orderIds as $orderId) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($orderId);

            if ($order) {
                $result = Mage::getModel('zigzagbase/service_ws_insertshipment')->insert($order);
                if ($result) {
                    Mage::helper('zigzagbase/shipping')->shipOrder($order, $result);
                    $successOrders[] = 'Incremented Id: ' . $order->getIncrementId() . ' Tracking Number: ' .  $result;
                } else {
                    $errorOrders[] = 'Incremented Id: ' . $order->getIncrementId() . ' Please Check Error Log';
                }
            } else {
                $errorOrders[] = 'Order Not Found (no id)';
            }
        }

        if ($errorOrders) {
            Mage::getSingleton('core/session')->addError('ZigZag Module Errors:<br>' . implode('<br>', $errorOrders));
        }

        if ($successOrders) {
            Mage::getSingleton('core/session')->addSuccess('ZigZag Module Success:<br>' . implode('<br>', $successOrders));
        }

        $this->_redirectReferer();
    }
}
