<?php

class ZigZag_Base_Block_Adminhtml_Config_Shippingtypes extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setDisabled('disabled');
        return $element->getElementHtml();
    }
}