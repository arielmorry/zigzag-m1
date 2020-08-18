<?php

class ZigZag_Base_Adminhtml_PrintzigzaglabelController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('ZigZag Shipping Label'));
        $this->loadLayout();
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('zigzagbase/adminhtml_order_label')->setTemplate('zigzagbase/label.phtml')->toHtml());
    }

    public function massAction()
    {
        $this->_title($this->__('ZigZag Shipping Label'));
        $this->loadLayout();
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('zigzagbase/adminhtml_order_label')->setTemplate('zigzagbase/label.phtml')->toHtml());
    }
}