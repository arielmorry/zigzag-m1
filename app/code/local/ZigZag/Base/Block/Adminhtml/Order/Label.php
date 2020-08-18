<?php

class ZigZag_Base_Block_Adminhtml_Order_Label extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    protected function _construct()
    {
        $orderId      = Mage::app()->getRequest()->getParam('order_id');
        $this->_order = Mage::getModel('sales/order')->load($orderId);
    }

    /**
     * @return array
     */
    public function getStoreInfo()
    {
        return array(
            'name'    => Mage::getStoreConfig(
                Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME,
                $this->_order->getStoreId()
            ),
            'address' => Mage::getStoreConfig(
                'general/store_information/address',
                $this->_order->getStoreId()
            ),
            'phone'   => Mage::getStoreConfig(
                Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE,
                $this->_order->getStoreId()
            ),
        );
    }

    /**
     * @return string
     * @throws Zend_Barcode_Exception
     */
    public function getBarcodeBase64()
    {
        $renderer = Zend_Barcode::factory(
            'code128',
            'image',
            [
                'barHeight'     => 80,
                'barThickWidth' => 6,
                'barThinWidth'  => 2,
                'text'          => $this->getTrackingNumber()
            ]
        );

        ob_start();
        $renderer->render();
        $imageData = ob_get_clean();
        return 'data:image/png;base64,' . base64_encode($imageData);

    }

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
        }
        return $this->_order;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getShipmentType()
    {
        return Mage::helper('zigzagbase')->getShipmentCodeByCarrierCode(
            $this->getOrder()->getShippingMethod(true)->getCarrierCode()
        );
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function getTrackingNumber()
    {
        $trackNumber = false;
        if ($this->getOrder()->getTracksCollection()->count()) {
            $trackNumber = $this->getOrder()->getTracksCollection()->getFirstItem()->getTrackNumber();
        }
        return $trackNumber;
    }
}