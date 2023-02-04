<?php

namespace local_cts_co;

class request
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var \stdClass
     */
    private $record;


    /**
     * If id available, load record
     * @param $id
     * @throws \dml_exception
     */
    public function __construct($id = 0)
    {
        global $DB;
        $this->id = $id;
        $this->table = 'cts_co_request';

        if ($this->id) {
            $this->record = $DB->get_record($this->table, ['id' => $this->id]);
        } else {
            $this->record = new \stdClass();
        }
    }

    /**
     * @param $data \stdClass
     * @return int
     */
    public function insert_record($data)
    {
        global $DB;

        if (!isset($data->timecreated)) {
            $timecreated = time();
        } else {
            $timecreated = $data->timecreated;
        }
        $timemodified = $timecreated;

        $data->timecreated = $timecreated;
        $data->timemodified = $timemodified;
        $data->latest_status = 'New request';

        if ($id = $DB->insert_record($this->table, $data)) {
            // Create status record.
            $STATUS = new status();

            $status_data = new \stdClass();
            $status_data->request_id = $id;
            $status_data->status = 'New request';
            $status_data->timecreated = $timecreated;

            $STATUS->insert_record($status_data);

            return $id;
        }

        return false;
    }

    /**
     * Returns record data and statuses associated to this request.
     * @return \stdClass
     */
    public function get_request($order_direction = 'DESC')
    {
        $STATUS = new status();
        $request = new \stdClass();

        $request->request = $this->record;
        $request->statuses = $STATUS->get_statuses($this->id, $order_direction);

        return $request;
    }

    public function get_dashboard_data($start = 0, $end = 25,
                                       $term = '', $order_column = 'u.timecreated', $order_direction = 'DESC')
    {
        global $USER, $DB;

        $sql = "Select
                    r.id,
                    r.userid,
                    r.halo_ticket_id,
                    r.jira_issue_key,
                    r.summary,
                    r.usermodified,
                    r.latest_status as status,
                    r.timecreated,
                    r.timemodified,
                    u.firstname,
                    u.lastname,
                    u.email
                From
                    {cts_co_request} r Inner Join
                    {user} u On u.id = r.userid
                Where
                    (r.userid = $USER->id Or
                    r.usermodified = $USER->id)";

        if ($term) {
            $sql .= " AND (u.firstname LIKE '%$term%' OR ";
            $sql .= "u.lastname LIKE '%$term%' OR ";
            $sql .= "latest_status LIKE '%$term%')";
        }

        switch ($order_column) {
            case 'for_user':
                $sql .= " ORDER BY u.firstname $order_direction, u.lastname $order_direction";
                break;
            case 'timecreated':
                $sql .= " ORDER BY r.timecreated $order_direction";
                break;
            case 'status':
                $sql .= " ORDER BY r.latest_status $order_direction";
                break;
            default:
                $sql .= " ORDER BY r.timecreated $order_direction";
                break;
        }

        $total_found = count($DB->get_records_sql($sql));

        $sql .= " LIMIT $start, $end";

        $requests = $DB->get_recordset_sql($sql);

        // Do some formating
        $results = [];
        $i = 0;
        foreach ($requests as $r) {
            $results[$i] = new \stdClass();
            $results[$i]->id = $r->id;
            $time_created = strftime(get_string('strftimedatetime'), $r->timecreated);
            $results[$i]->halo_ticket_id = 'SR-' . $r->halo_ticket_id;
            $results[$i]->jira_issue_key = $r->jira_issue_key;
            $results[$i]->timecreated = $time_created;
            $results[$i]->status = $r->status;
            $results[$i]->for_user = $r->firstname . ' ' . $r->lastname;
            $i++;
        }

        $data = new \stdClass();
        $data->total_found = $total_found;
        $data->total_displayed = count($results);
        $data->results = $results;

        return $data;
    }


    /**
     * Returns JIRA issue key
     * @return string
     */
    public function get_jira_issue_key()
    {
        return $this->record->jira_issue_key;
    }

    /**
     * Returns HALO ticket id
     * @return string
     */
    public function get_halo_ticket_id()
    {
        return $this->record->halo_ticket_id;
    }

    /**
     * @param $step int
     * @return void
     */
    public function current_status($step)
    {
        $new_request_stage = false;
        $quote_process_stage = false;
        $order_submitted_stage = false;
        $with_supplier_stage = false;
        $order_received_stage = false;
        $inventory_preperation_stage = false;
        $deployment_stage = false;
        $request_completed_stage = false;
        switch ($step) {
            case 1:
                $new_request = 'info';
                $quote_process = 'secondary';
                $order_submitted = 'secondary';
                $with_supplier = 'secondary';
                $order_received = 'secondary';
                $inventory_preperation = 'secondary';
                $deployment = 'secondary';
                $request_completed = 'secondary';
                $new_request_stage = true;
                break;
            case 2:
                $new_request = 'success';
                $quote_process = 'info';
                $order_submitted = 'secondary';
                $with_supplier = 'secondary';
                $order_received = 'secondary';
                $inventory_preperation = 'secondary';
                $deployment = 'secondary';
                $request_completed = 'secondary';
                $quote_process_stage = true;
                break;

            case 3:
                $new_request = 'success';
                $quote_process = 'success';
                $order_submitted = 'info';
                $with_supplier = 'secondary';
                $order_received = 'secondary';
                $inventory_preperation = 'secondary';
                $deployment = 'secondary';
                $request_completed = 'secondary';
                $order_submitted_stage = true;
                break;
            case 4:
                $new_request = 'success';
                $quote_process = 'success';
                $order_submitted = 'success';
                $with_supplier = 'info';
                $order_received = 'secondary';
                $inventory_preperation = 'secondary';
                $deployment = 'secondary';
                $request_completed = 'secondary';
                $with_supplier_stage = true;
                break;
            case 5:
                $new_request = 'success';
                $quote_process = 'success';
                $order_submitted = 'success';
                $with_supplier = 'success';
                $order_received = 'info';
                $inventory_preperation = 'secondary';
                $deployment = 'secondary';
                $request_completed = 'secondary';
                $order_received_stage = true;
                break;
            case 6:
                $new_request = 'success';
                $quote_process = 'success';
                $order_submitted = 'success';
                $with_supplier = 'success';
                $order_received = 'success';
                $inventory_preperation = 'info';
                $deployment = 'secondary';
                $request_completed = 'secondary';
                $inventory_preperation_stage = true;
                break;
            case 7:
                $new_request = 'success';
                $quote_process = 'success';
                $order_submitted = 'success';
                $with_supplier = 'success';
                $order_received = 'success';
                $inventory_preperation = 'success';
                $deployment = 'info';
                $request_completed = 'secondary';
                $deployment_stage = true;
                break;
            case 8:
                $new_request = 'success';
                $quote_process = 'success';
                $order_submitted = 'success';
                $with_supplier = 'success';
                $order_received = 'success';
                $inventory_preperation = 'success';
                $deployment = 'success';
                $request_completed = 'success';
                $request_completed_stage = true;
                break;
        }

        $results = new \stdClass();
        $results->new_request = $new_request;
        $results->quote_process = $quote_process;
        $results->order_submitted = $order_submitted;
        $results->with_supplier = $with_supplier;
        $results->order_received = $order_received;
        $results->inventory_preperation = $inventory_preperation;
        $results->deployment = $deployment;
        $results->request_completed = $request_completed;
        $results->new_request_stage = $new_request_stage;
        $results->quote_process_stage = $quote_process_stage;
        $results->order_submitted_stage = $order_submitted_stage;
        $results->with_supplier_stage = $with_supplier_stage;
        $results->order_received_stage = $order_received_stage;
        $results->inventory_preperation_stage = $inventory_preperation_stage;
        $results->deployment_stage = $deployment_stage;
        $results->request_completed_stage = $request_completed_stage;

        return $results;

    }
}