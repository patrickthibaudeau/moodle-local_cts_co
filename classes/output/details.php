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

use local_cts_co\haloitsm;
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
        $HALO = new haloitsm();
        // Is user an agent
        $context = \context_system::instance();
        $is_agent = false;
        if (has_capability('local/cts_co:access_jira', $context)) {
            $is_agent = true;
        }
        // Update status for this record
        $STATUS->update_status($this->id, $REQUEST->get_jira_issue_key());

        $results = $REQUEST->get_request('ASC');
        $issue = $JIRA->get_issue($REQUEST->get_jira_issue_key());

        $ticket = $HALO->get_ticket($results->request->halo_ticket_id);
        $agent = $HALO->get_agent_by_id($ticket->agent_id);
        // Get user information
        $for_user = $DB->get_record('user', ['id' => $results->request->userid]);
        $by_user = $DB->get_record('user', ['id' => $results->request->usermodified]);

        // Get timeline
        $i = 0;
        $timeline = [];
        foreach ($results->statuses as $status) {
            $timeline[$i]['date'] = strftime(get_string('strftimedatetime'), $status->timecreated);
            $timeline[$i]['content'] = $status->status . ' ' . $status->jira_comment;
            $i++;
        }

        $process_class = $REQUEST->current_status(count($results->statuses));

        if (!$results->request->timecreated) {
            $timecreated = strftime(get_string('strftimedatetime'), $ticket->dateoccurred);
        } else {
            $timecreated = strftime(get_string('strftimedatetime'), $results->request->timecreated);
        }
        $data = [
            'summary' => $results->request->summary,
            'description' => $results->request->description,
            'halo_ticket_id' => $results->request->halo_ticket_id,
            'timecreated' => $timecreated,
            'for_user' => fullname($for_user),
            'by_user' => fullname($by_user),
            'timeline' => json_encode($timeline),
            'halo_url' => $CFG->halo_url,
            'issue' => $issue,
            'agent' => $agent->name,
            'new_request' => $process_class->new_request,
            'quote_process' => $process_class->quote_process,
            'order_submitted' => $process_class->order_submitted,
            'with_supplier' => $process_class->with_supplier,
            'order_received' => $process_class->order_received,
            'inventory_preperation' => $process_class->inventory_preperation,
            'deployment' => $process_class->deployment,
            'request_completed' => $process_class->request_completed,
            'new_request_stage' => $process_class->new_request_stage,
            'quote_process_stage' => $process_class->quote_process_stage,
            'order_submitted_stage' => $process_class->order_submitted_stage,
            'with_supplier_stage' => $process_class->with_supplier_stage,
            'order_received_stage' => $process_class->order_received_stage,
            'inventory_preperation_stage' => $process_class->inventory_preperation_stage,
            'deployment_stage' => $process_class->deployment_stage,
            'request_completed_stage' => $process_class->request_completed_stage,
            'is_agent' => $is_agent
        ];

        return $data;
    }

}
