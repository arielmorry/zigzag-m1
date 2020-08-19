<?php

/**
 * Class ZigZag_Base_Model_Observer_Mass
 */
class ZigZag_Base_Model_Observer_Mass
{
    /**
     * @param $observer
     * @return ZigZag_Base_Model_Observer_Mass
     */
    public function addMassAction($observer)
    {
        /** @var Mage_Adminhtml_Block_Widget_Grid_Massaction $block */
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' && $block->getRequest()->getControllerName() == 'sales_order') {
            $this->addMassPrintLabels($block);
            $this->addMassZigZagSubmission($block);
        }

        return $this;
    }

    /**
     * @param $block
     */
    protected function addMassPrintLabels($block)
    {
        /** @var Mage_Adminhtml_Block_Widget_Grid_Massaction $block */
        $block->addItem('zigzag_mass_print_labels', array(
            'label' => Mage::helper('sales')->__('Print ZigZag Shipping Labels'),
            'url' => $block->getUrl('*/printzigzaglabel/index')
        ));
    }

    /**
     * @param $block
     */
    protected function addMassZigZagSubmission($block)
    {
        /** @var Mage_Adminhtml_Block_Widget_Grid_Massaction $block */
        $block->addItem('zigzag_mass_submission', array(
            'label' => Mage::helper('sales')->__('Send Orders To ZigZag'),
            'url' => $block->getUrl('*/shiporderzigzag/mass'),
            'confirm' => Mage::helper('sales')->__('Order Submission to ZigZag Can Take up to 10 sec per order. Please avoid large submissions to prevent timeout errors in your browser')
        ));
    }
}