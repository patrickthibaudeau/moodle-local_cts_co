<?php

namespace local_cts_co\task;

use local_cts_co\status;

class update_status extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('update_status', 'local_cts_co');
    }

    /**
     * Run forum cron.
     */
    public function execute() {
        global $DB;
        $STATUS = new status();
        // Get all requests that are not completed
        $sql = "SELECT id,jira_issue_key FROM {cts_co_request} WHERE latest_status !='Completed'";
        $results = $DB->get_recordset_sql($sql);
        foreach($results as $r) {
            $STATUS->update_status($r->id, $r->jira_issue_key);
        }
    }

    public function get_run_if_component_disabled() {
        return true;
    }

}
