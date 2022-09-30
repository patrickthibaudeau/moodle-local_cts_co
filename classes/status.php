<?php

namespace local_cts_co;

use core\notification;

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
    public function __construct()
    {
        global $DB;
        $this->table = 'cts_co_status';
    }

    /**
     * @param $data \stdClass
     * @return int
     */
    public function insert_record($data)
    {
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
    public function get_statuses($request_id, $order_direction)
    {
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
        $HALO = new haloitsm();
        $issue = $JIRA->get_issue($jira_issue_key);
        // If issue status is not the same as result status, add new status record
        if (isset($issue)) {
            if ($issue->status != $result->status) {
                $REQUEST = new request($request_id);
                $params = new \stdClass();
                $params->request_id = $request_id;
                $params->status = $issue->status;
                $params->timecreated = $issue->updated;

                $new_status_id = $DB->insert_record('cts_co_status', $params);
                //Update latest status in request record;
                $request_params = new \stdClass();
                $request_params->id = $request_id;
                $request_params->latest_status = $issue->status;
                $request_params->timemodified = $params->timecreated;

                $DB->update_record('cts_co_request', $request_params);

                $note = 'Your computer request status has been updated to ' . $issue->status;
                // create HALO action on ticekt
                $HALO->add_action(
                    $issue->agent,
                    $REQUEST->get_halo_ticket_id(),
                    $note);

                return $new_status_id;
            }
        } else {
            notification::error('This record is not aproperly ssocitaed with ticket');
        }
        return false;
    }

}