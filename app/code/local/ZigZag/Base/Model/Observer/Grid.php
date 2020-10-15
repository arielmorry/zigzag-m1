<?php

class ZigZag_Base_Model_Observer_Grid
{
    /**
     * @param $observer
     * @return ZigZag_Base_Model_Observer_Grid
     */
    public function addTrackingToSalesCollection($observer)
    {
        $separator = '<br>';
        $action    = Mage::app()->getRequest()->getActionName();
        if ($action == 'exportCsv' || $action == 'exportExcel') {
            $separator = '|';
        }

        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection = $observer->getOrderGridCollection();
        $collection->addFilterToMap('created_at', 'main_table.created_at');

        /** @var Zend_Db_Select $select */
        $select = $collection->getSelect();

        $select->joinLeft(
            array(
                'shipment_track' => $collection->getTable('sales/shipment_track'),
            ),
            'shipment_track.order_id = main_table.entity_id',
            array('track' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT shipment_track.track_number SEPARATOR "' . $separator . '")'))
        );
        $select->group('main_table.entity_id');

        if ($where = $select->getPart(Zend_Db_Select::WHERE)) {
            foreach ($where as $key => $condition) {
                if (strpos($condition, '`created_at`')) {
                    $where[$key]   = str_replace('`created_at`', new Zend_Db_Expr('`main_table`.`created_at`'), $condition);;
                }
            }

            $select->reset(Zend_Db_Select::WHERE);
            $select->setPart('where', $where);
        }

        return $this;
    }
}