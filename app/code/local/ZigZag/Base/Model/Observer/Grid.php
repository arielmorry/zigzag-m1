<?php

class ZigZag_Base_Model_Observer_Grid
{
    /**
     * @param $observer
     * @return ZigZag_Base_Model_Observer_Grid
     */
    public function addTrackingToSalesCollection($observer)
    {
        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection = $observer->getOrderGridCollection();

        $separator = '<br>';
        $action = Mage::app()->getRequest()->getActionName();
        if ($action == 'exportCsv' || $action == 'exportExcel') {
            $separator = '|';
        }

        /** @var Zend_Db_Select $select */
        $select = $collection->getSelect();
        $select->joinLeft(
            array(
                'shipment_track' => $collection->getTable('sales/shipment_track'),
            ),
            'shipment_track.order_id = main_table.entity_id AND shipment_track.carrier_code LIKE "zigzag%"',
            array('track' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT shipment_track.track_number SEPARATOR "'. $separator .'")'))
        );
        $select->group('main_table.entity_id');

        return $this;
    }
}