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

class dashboard implements \renderable, \templatable {

    public function __construct() {
        
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

        $context = \context_system::instance();
        // Can user access Jira
        $can_access_jira = false;
        $jira_board = '';
        $jira_url = '';
        if (has_capability('local/cts_co:access_jira', $context)) {
            $can_access_jira = true;
            $jira_board = $CFG->jira_board;
            $jira_url = $CFG->jira_url;
        }

        $data = [
            'can_access_jira' => $can_access_jira,
            'jira_board' => $jira_board,
            'jira_url' => $jira_url,
        ];
        return $data;
    }

}
