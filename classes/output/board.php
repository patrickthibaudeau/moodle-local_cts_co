<?php
/**
 * *************************************************************************
 * *                           YULearn ELMS                               **
 * *************************************************************************
 * @package     local                                                     **
 * @subpackage  yulearn                                                   **
 * @name        YULearn ELMS                                              **
 * @copyright   UIT - Innovation lab & EAAS                               **
 * @link                                                                  **
 * @author      Patrick Thibaudeau                                        **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

namespace local_cts_co\output;

use local_cts_co\requests;
use local_cts_co\status;

class board implements \renderable, \templatable
{

    /**
     * @var int
     */
    private $id;

    public function __construct()
    {

    }

    /**
     *
     * @param \renderer_base $output
     * @return type
     * @global \moodle_database $DB
     * @global type $USER
     * @global type $CFG
     */
    public function export_for_template(\renderer_base $output)
    {
        global $USER, $CFG, $DB;

        $REQUESTS= new requests();

        $new = $REQUESTS->get_based_on_status(status::STEP_NEW);
        $quote_process = $REQUESTS->get_based_on_status(status::STEP_QUOTE);
        $pending_approval = $REQUESTS->get_based_on_status(status::STEP_PENDING);
        $with_vendor = $REQUESTS->get_based_on_status(status::STEP_WITH_VENDOR);
        $order_received = $REQUESTS->get_based_on_status(status::STEP_ORDER_RECIEVED);
        $order_inventory_preperation = $REQUESTS->get_based_on_status(status::STEP_INVENTORY_PREPERATION);
        $order_deployment = $REQUESTS->get_based_on_status(status::STEP_DEPLOYMENT);
        $order_completed = $REQUESTS->get_based_on_status(status::STEP_COMPLETED);

        $data = [
            'new' => $new,
            'quote_process' => $quote_process,
            'pending_approval' => $pending_approval,
            'with_vendor' => $with_vendor,
            'order_received' => $order_received,
            'order_inventory_preperation' => $order_inventory_preperation,
            'order_deployment' => $order_deployment,
            'order_completed' => $order_completed,
        ];

        print_object($new);

        return $data;
    }

}
