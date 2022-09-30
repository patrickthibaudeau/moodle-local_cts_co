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

class details implements \renderable, \templatable {

    /**
     * @var int
     */
    private $id;

    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * 
     * @global type $USER
     * @global type $CFG
     * @global \moodle_database $DB
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {
        global $USER, $CFG, $DB;

        $REQUEST = new request($this->id);
        $STATUS = new status();
        // Update status for this record
        $STATUS->update_status($this->id, $REQUEST->get_jira_issue_key());

        $results = $REQUEST->get_request('ASC');

        // Get user information
        $for_user = $DB->get_record('user', ['id' => $results->request->userid]);
        $by_user = $DB->get_record('user', ['id' => $results->request->usermodified]);

        // Get timeline
        $i = 0;
        $timeline = [];
        foreach ($results->statuses as $status) {
            $timeline[$i]['date'] = strftime(get_string('strftimedatetime'),$status->timecreated);
            $timeline[$i]['content'] = $status->status;
            $i++;
        }

        $data = [
            'summary' => $results->request->summary,
            'description' => $results->request->description,
            'halo_ticket_id' => $results->request->halo_ticket_id,
            'timecreated' => strftime(get_string('strftimedatetime'),$results->request->timecreated),
            'for_user' => fullname($for_user),
            'by_user' => fullname($by_user),
            'timeline' => json_encode($timeline),
            'halo_url' => $CFG->halo_url
        ];

        return $data;
    }

}
