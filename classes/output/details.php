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

use local_cts_co\request;
use local_cts_co\status;
use local_cts_co\jira;

class details implements \renderable, \templatable
{

    /**
     * @var int
     */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
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

        $REQUEST = new request($this->id);
        $STATUS = new status();
        $JIRA = new jira();
        // Update status for this record
        $STATUS->update_status($this->id, $REQUEST->get_jira_issue_key());

        $results = $REQUEST->get_request('ASC');
        $issue = $JIRA->get_issue($REQUEST->get_jira_issue_key());
        // Get user information
        $for_user = $DB->get_record('user', ['id' => $results->request->userid]);
        $by_user = $DB->get_record('user', ['id' => $results->request->usermodified]);

        // Get timeline
        $i = 0;
        $timeline = [];
        foreach ($results->statuses as $status) {
            $timeline[$i]['date'] = strftime(get_string('strftimedatetime'), $status->timecreated);
            $timeline[$i]['content'] = $status->status;
            $i++;
        }

        switch ($i) {
            case 1:
                $new_request_completed = false;
                $quote_process_started = false;
                $quote_process_not_started = true;
                $quote_process_completed = false;
                $order_process_started = false;
                $order_process_not_started = false;
                $order_process_completed = false;
                $receiving_process_started = false;
                $receiving_process_not_started = false;
                $receiving_process_completed = false;
                $order_complete_process_started = false;
                $order_complete_process_not_started = false;
                $order_complete_process_completed = false;
                $inventory_process_started = false;
                $inventory_process_not_started = false;
                $inventory_process_completed = false;
                $imaging_process_started = false;
                $imaging_process_not_started = false;
                $imaging_process_completed = false;
                $setup_process_started = false;
                $setup_process_not_started = false;
                $setup_process_completed = false;
                $pickup_process_started = false;
                $pickup_process_not_started = false;
                $pickup_process_completed = false;
                $deployment_process_started = false;
                $deployment_process_not_started = false;
                $deployment_process_completed = false;
                break;
            case 2:
                $new_request_completed = true;
                $quote_process_started = true;
                $quote_process_not_started = true;
                $quote_process_completed = false;
                $order_process_started = false;
                $order_process_not_started = true;
                $order_process_completed = false;
                $receiving_process_started = false;
                $receiving_process_not_started = true;
                $receiving_process_completed = false;
                $order_complete_process_started = false;
                $order_complete_process_not_started = true;
                $order_complete_process_completed = false;
                $inventory_process_started = false;
                $inventory_process_not_started = true;
                $inventory_process_completed = false;
                $imaging_process_started = false;
                $imaging_process_not_started = true;
                $imaging_process_completed = false;
                $setup_process_started = false;
                $setup_process_not_started = true;
                $setup_process_completed = false;
                $pickup_process_started = false;
                $pickup_process_not_started = true;
                $pickup_process_completed = false;
                $deployment_process_started = false;
                $deployment_process_not_started = true;
                $deployment_process_completed = false;
                break;
            case 3:
                $new_request_completed = true;
                $quote_process_started = true;
                $quote_process_not_started = false;
                $quote_process_completed = false;
                $order_process_started = false;
                $order_process_not_started = true;
                $order_process_completed = false;
                $receiving_process_started = false;
                $receiving_process_not_started = true;
                $receiving_process_completed = false;
                $order_complete_process_started = false;
                $order_complete_process_not_started = true;
                $order_complete_process_completed = false;
                $inventory_process_started = false;
                $inventory_process_not_started = true;
                $inventory_process_completed = false;
                $imaging_process_started = false;
                $imaging_process_not_started = true;
                $imaging_process_completed = false;
                $setup_process_started = false;
                $setup_process_not_started = true;
                $setup_process_completed = false;
                $pickup_process_started = false;
                $pickup_process_not_started = true;
                $pickup_process_completed = false;
                $deployment_process_started = false;
                $deployment_process_not_started = true;
                $deployment_process_completed = false;
                break;
        }

        $data = [
            'summary' => $results->request->summary,
            'description' => $results->request->description,
            'halo_ticket_id' => $results->request->halo_ticket_id,
            'timecreated' => strftime(get_string('strftimedatetime'), $results->request->timecreated),
            'for_user' => fullname($for_user),
            'by_user' => fullname($by_user),
            'timeline' => json_encode($timeline),
            'halo_url' => $CFG->halo_url,
            'issue' => $issue
        ];

        return $data;
    }

}
