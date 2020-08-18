<?php

/**
 * Class ZigZag_Base_Model_Observer_Mass
 */
class ZigZag_Base_Model_Observer_Mass
{
    /** @var Mage_Adminhtml_Block_Widget_Grid_Massaction $_block */
    protected $_block;

    /**
     * @param $observer
     */
    public function addMassAction($observer)
    {
        $this->_block = $observer->getEvent()->getBlock();
        if (get_class($this->_block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' && $this->_block->getRequest()->getControllerName() == 'sales_order') {
            $this->addMassPrintLabels();
        }
    }

    /**
     *
     */
    protected function addMassPrintLabels()
    {
        $this->_block->addItem('zigzag_mass_print_labels', array(
            'label' => Mage::helper('sales')->__('Print ZigZag Labels'),
            'url' => $this->_block->getUrl('*/printzigzaglabel/mass')
        ));
    }
}