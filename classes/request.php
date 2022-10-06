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

        $timecreated = time();
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
    public function current_status($step) {
        switch ($step) {
            case 1:
                $new_request= 'info';
                $quote_process = 'secondary';
                $order_process = 'secondary';
                $receiving_process = 'secondary';
                $order_complete = 'secondary';
                $inventory_process = 'secondary';
                $imaging_process = 'secondary';
                $setup_process = 'secondary';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 2:
                $new_request= 'success';
                $quote_process = 'info';
                $order_process = 'secondary';
                $receiving_process = 'secondary';
                $order_complete = 'secondary';
                $inventory_process = 'secondary';
                $imaging_process = 'secondary';
                $setup_process = 'secondary';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 3:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'info';
                $receiving_process = 'secondary';
                $order_complete = 'secondary';
                $inventory_process = 'secondary';
                $imaging_process = 'secondary';
                $setup_process = 'secondary';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 4:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'info';
                $order_complete = 'secondary';
                $inventory_process = 'secondary';
                $imaging_process = 'secondary';
                $setup_process = 'secondary';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 5:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'info';
                $inventory_process = 'secondary';
                $imaging_process = 'secondary';
                $setup_process = 'secondary';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 5:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'success';
                $inventory_process = 'info';
                $imaging_process = 'secondary';
                $setup_process = 'secondary';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 5:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'success';
                $inventory_process = 'success';
                $imaging_process = 'info';
                $setup_process = 'secondary';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 6:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'success';
                $inventory_process = 'success';
                $imaging_process = 'success';
                $setup_process = 'info';
                $pickup_process = 'secondary';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 7:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'success';
                $inventory_process = 'success';
                $imaging_process = 'success';
                $setup_process = 'success';
                $pickup_process = 'info';
                $deployment_process= 'secondary';
                $deployment_process_completed = 'secondary';
                break;
            case 8:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'success';
                $inventory_process = 'success';
                $imaging_process = 'success';
                $setup_process = 'success';
                $pickup_process = 'success';
                $deployment_process= 'info';
                $deployment_process_completed = 'secondary';
                break;
            case 9:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'success';
                $inventory_process = 'success';
                $imaging_process = 'success';
                $setup_process = 'success';
                $pickup_process = 'success';
                $deployment_process= 'success';
                $deployment_process_completed = 'info';
                break;
            case 10:
                $new_request= 'success';
                $quote_process = 'success';
                $order_process = 'success';
                $receiving_process = 'success';
                $order_complete = 'success';
                $inventory_process = 'success';
                $imaging_process = 'success';
                $setup_process = 'success';
                $pickup_process = 'success';
                $deployment_process= 'success';
                $deployment_process_completed = 'success';
                break;
        }
        
        $results = new \stdClass();
        $results->new_request = $new_request;
        $results->quote_process = $quote_process;
        $results->order_process = $order_process;
        $results->receiving_process = $receiving_process;
        $results->order_complete = $order_complete;
        $results->inventory_process = $inventory_process;
        $results->imaging_process = $imaging_process;
        $results->setup_process = $setup_process;
        $results->pickup_process = $pickup_process;
        $results->deployment_process = $deployment_process;
        $results->deployment_process_completed = $deployment_process_completed;

        return $results;

    }
}