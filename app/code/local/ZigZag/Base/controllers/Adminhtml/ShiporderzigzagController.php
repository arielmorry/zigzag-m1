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
            }
        } else {
            Mage::getSingleton('core/session')->addError('ZigZag Module: Order Not Found');
        }

        $this->_redirectReferer();
    }
}
