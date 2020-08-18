<?php

/**
 * Class ZigZag_Base_Model_Observer_Label
 */
class ZigZag_Base_Model_Observer_Label
{
    /** @var Mage_Adminhtml_Block_Widget_Container $_block */
    protected $_block;

    /**
     * @param $observer
     * @return $this
     */
    public function addButtons($observer)
    {
        $this->_block = Mage::app()->getLayout()->getBlock('sales_order_edit');
        if (!$this->_block) {
            return $this;
        }

        $order = Mage::registry('current_order');
        $this->addZigZag($order);
        $this->addShippingLabel($order);

        return $this;
    }

    /**
     * @param $order
     */
    protected function addZigZag($order)
    {
        $url = Mage::helper("adminhtml")->getUrl(
            '*/shiporderzigzag/index',
            array('order_id' => $order->getId())
        );
        $this->_block->addButton('order_send_to_zigzag', array(
            'label' => Mage::helper('sales')->__('Send Order To ZigZag'),
            'onclick'   => 'setLocation(\'' . $url . '\')',
            'class' => 'print-label'
        ));
    }

    /**
     * @param $order
     */
    protected function addShippingLabel($order)
    {
        $carrierCode = $order->getShippingMethod(true)->getCarrierCode();
        if (strpos($carrierCode, 'zigzag') !== false && $order->hasShipments()) {
            $url = Mage::helper("adminhtml")->getUrl(
                '*/printzigzaglabel/index',
                array('order_id' => $order->getId())
            );
            $this->_block->addButton('order_print_zigzag_label', array(
                'label' => Mage::helper('sales')->__('Print ZigZag Shipping Label'),
                'onclick' => "window.open('$url','popUpWindow','height=700,width=700,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');",
                'class' => 'print-label'
            ));
        }
    }
}