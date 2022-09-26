<?php

namespace local_cts_co;

class status
{
    /**
     * @var string
     */
    protected $table;

    /**
     * If id available, load record
     * @param $id
     * @throws \dml_exception
     */
    public function __construct() {
        global $DB;
        $this->table = 'cts_co_status';
    }

    /**
     * @param $data \stdClass
     * @return int
     */
    public function insert_record($data) {
        global $DB;

        $timecreated = time();

        $data->timecreated = $timecreated;

        $id = $DB->insert_record($this->table, $data);

        return $id;
    }

    /**
     * Returns statuses for a request in chronological order latest to oldest
     * @param $request_id
     * @return void
     */
    public function get_statuses($request_id, $order_direction) {
        global $DB;

        $results = $DB->get_records($this->table, ['request_id' => $request_id], 'timecreated ' . $order_direction);
        $results = array_values($results);

        return $results;
    }

    public function update_status($request_id, $jira_issue_key)
    {
        global $DB;
        // Get the last status
        $sql = "SELECT * FROM {cts_co_status} WHERE request_id=? ORDER BY timecreated DESC Limit 1";
        $result = $DB->get_record_sql($sql, [$request_id]);
        // Get JIRA issue
        $JIRA = new jira();
        $issue = $JIRA->get_issue($jira_issue_key);
        // If issue status is not the same as result status, add new status record
        if ($issue->status != $result->status) {
            $params = new \stdClass();
            $params->request_id = $request_id;
            $params->status = $issue->status;
            $params->timecreated = time();

            $new_status_id = $DB->insert_record('cts_co_status', $params);
            //Update latest status in request record;
            $request_params = new \stdClass();
            $request_params->id = $request_id;
            $request_params->latest_status = $issue->status;
            $request_params->timemodified = $params->timecreated;

            $DB->update_record('cts_co_request', $request_params);

            return $new_status_id;
        }

        return false;
    }

}