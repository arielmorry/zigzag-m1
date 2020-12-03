<?php

class ZigZag_Base_AvailabilityController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $address = Mage::app()->getRequest()->getParam('target');
        $countryId = Mage::app()->getRequest()->getParam('country_id');
        $country = Mage::getModel('directory/country')->loadByCode($countryId)->getName();

        $result = Mage::getModel('zigzagbase/service_ws_shipmentavailability')->get($address . ' ' . $country);

        if ($result) {
            $this->loadLayout();

            $block = $this->getLayout()->createBlock('zigzagbase/availability')
                ->setNameInLayout('zigzag_availability')
                ->setData('availability', $result)
                ->setTemplate('zigzagbase/availability.phtml');

            echo $block->toHtml();
        }

        exit;
    }
}