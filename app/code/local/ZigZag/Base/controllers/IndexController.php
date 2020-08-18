<?php

class ZigZag_Base_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_title($this->__('ZigZag Shipping Label'));
        $this->loadLayout();
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('zigzagbase/adminhtml_order_label')->setTemplate('zigzagbase/label.phtml')->toHtml());
    }
}