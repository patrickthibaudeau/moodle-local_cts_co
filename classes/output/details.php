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

        $data = [
            'summary' => $results->request->summary,
            'description' => $results->request->description,
            'halo_ticket_id' => $results->request->halo_ticket_id,
            'timecreated' => strftime(get_string('strftimedatetime'), $results->request->timecreated),
            'for_user' => fullname($for_user),
            'by_user' => fullname($by_user),
            'timeline' => json_encode($timeline),
            'halo_url' => $CFG->halo_url,
            'issue' => $issue,
            'agent' => $agent->name,
            'new_request_process' => $process_class->new_request,
            'quote_process' => $process_class->quote_process,
            'order_process' => $process_class->order_process,
            'receiving_process' => $process_class->receiving_process,
            'order_complete' => $process_class->order_complete,
            'inventory_process' => $process_class->inventory_process,
            'imaging_process' => $process_class->imaging_process,
            'setup_process' => $process_class->setup_process,
            'pickup_process' => $process_class->pickup_process,
            'deployment_process' => $process_class->deployment_process,
            'deployment_process_completed' => $process_class->deployment_process_completed,
        ];

        return $data;
    }

}
