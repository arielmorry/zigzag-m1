<?php

class ZigZag_Base_Block_Adminhtml_Order_Label extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_orders = [];

    protected function _construct()
    {
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        $orderIds = Mage::app()->getRequest()->getParam('order_ids');

        if ($orderId) {
            $this->_orders[] = Mage::getModel('sales/order')->load($orderId);
        } elseif ($orderIds) {
            foreach ($orderIds as $orderId) {
                $this->_orders[] = Mage::getModel('sales/order')->load($orderId);
            }
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getStoreInfo($order)
    {
        return array(
            'name'    => Mage::getStoreConfig(
                Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME,
                $order->getStoreId()
            ),
            'address' => Mage::getStoreConfig(
                'general/store_information/address',
                $order->getStoreId()
            ),
            'phone'   => Mage::getStoreConfig(
                Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE,
                $order->getStoreId()
            ),
        );
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     * @throws Zend_Barcode_Exception
     */
    public function getBarcodeBase64($order)
    {
        $renderer = Zend_Barcode::factory(
            'code128',
            'image',
            [
                'barHeight'     => 80,
                'barThickWidth' => 6,
                'barThinWidth'  => 2,
                'text'          => $this->getTrackingNumber($order)
            ]
        );

        ob_start();
        $renderer->render();
        $imageData = ob_get_clean();
        return 'data:image/png;base64,' . base64_encode($imageData);

    }

    /**
     * @return array
     * @throws Exception
     */
    public function getOrders()
    {
        return $this->_orders;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return mixed
     * @throws Exception
     */
    public function getShipmentType($order)
    {
        return Mage::helper('zigzagbase')->getShipmentCodeByCarrierCode(
            $order->getShippingMethod(true)->getCarrierCode()
        );
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     * @throws Exception
     */
    public function getTrackingNumber($order)
    {
        $trackNumber = false;
        if ($order->getTracksCollection()->count()) {
            $trackNumber = $order->getTracksCollection()->getFirstItem()->getTrackNumber();
        }
        return $trackNumber;
    }
}