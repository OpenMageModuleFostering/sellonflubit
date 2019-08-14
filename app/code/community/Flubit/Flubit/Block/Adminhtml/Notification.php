<?php

/**
 * Class Flubit Block Admin Flubitbackend
 * 
 * @package Flubit
 * @category Flubit_Block
 * @author Flubit team
 */
class Flubit_Flubit_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template {

    /**
     * Method to check if a scheduled cron is executed 
     * 
     * @return  String
     */
    function checkScheduledCronExecution() {
        $return_message = '';
        try {
            $cronTimedOut = false;
            $time_now = date('Y-m-d H:i:s');
            $threashhold_time = '+15 min';

            //Mage::log('Time Now' . $time_now, NULL , 'CronTest.log' );

            $crons = array(
                0 => 'flubit_products_cron',
                1 => 'flubit_orders_fetch_cron',
                2 => 'flubit_orders_dispatch_cron',
                3 => 'flubit_orders_cancel_cron',
                4 => 'flubit_orders_refund_cron'
            );
            //Products, Orders Fetch, Orders Dispatch, Orders Cancel, Orders Refund.
            $crons_Redable = array(
                'flubit_products_cron' => 'Products Push',
                'flubit_orders_fetch_cron' => 'Fetching Orders',
                'flubit_orders_dispatch_cron' => 'Dispatch Orders',
                'flubit_orders_cancel_cron' => 'Cancel Orders',
                'flubit_orders_refund_cron' => 'Refund Orders'
            );

            $cron_stopped = array();
            foreach ($crons as $cron) {
                $cronCollection = Mage::getModel('cron/schedule')->getCollection()
                        ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_SUCCESS)
                        ->addFieldToFilter('job_code', $cron)
                        ->addOrder('executed_at', 'DESC')
                        ->load();
                $cronNotInitalized = FALSE;
                $cronCollection->getSelect()->limit(1);
                $cron_row = $cronCollection->getData();

                if (count($cron_row)) {
                    $lastExecutedTime = $cron_row[0]['executed_at'];

                    $tot_time_now = strtotime($time_now);
                    $tot_executed_time = strtotime($threashhold_time . $lastExecutedTime);



                    if ($tot_time_now < $tot_executed_time) { /* do Something */

                    } else {

                        $cron_stopped[] = $crons_Redable[$cron];
                        $cronTimedOut = true;
                    }
                } else {
                    $cron_stopped[] = $crons_Redable[$cron];
                    $cronNotInitalized = true;
                }
            }

            if ($cronTimedOut) {
                //Cron Execution has stopped! The following Cron jobs have been affected: 
                $return_message = 'Alert: Cron/Scheduled Tasks is either not setup or running.';
            }
            if ($cronNotInitalized) {
                $return_message = 'Alert: Cron/Scheduled Tasks is either not setup or running.';
            }
        } catch (Exception $e) {
            Mage::log('Cron Collection Fetch Exception :' . $e, NULL, 'CronTest.log');
        }
        return $return_message;
    }

}