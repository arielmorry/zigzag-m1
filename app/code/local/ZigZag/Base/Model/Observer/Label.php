<?php

/**
 * Class ZigZag_Base_Model_Observer_Label
 */
class ZigZag_Base_Model_Observer_Label
{
    /**
     * @param $observer
     * @return $this
     */
    public function addLabel($observer) {
        /** @var Mage_Adminhtml_Block_Widget_Container $block */
        $block = Mage::app()->getLayout()->getBlock('sales_order_edit');
        if (!$block){
            return $this;
        }

        $order = Mage::registry('current_order');
        $carrierCode = $order->getShippingMethod(true)->getCarrierCode();
//        if (strpos($carrierCode, 'zigzag') !== false) {
            $url = Mage::helper("adminhtml")->getUrl(
                'zigzag/index/index',
                array('order_id' => $order->getId())
            );
            $block->addButton('order_print_zigzag_label', array(
                'label' => Mage::helper('sales')->__('Print ZigZag Shipping Label'),
                'onclick' => "window.open('$url','popUpWindow','height=700,width=700,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');",
                'class' => 'print-label'
            ));
            $block->updateButton('order_ship', 'label', Mage::helper('sales')->__('Ship and Send To ZigZag'));
//        }
        return $this;
    }
}