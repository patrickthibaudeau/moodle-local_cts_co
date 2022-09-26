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
                    moodle.mdl_cts_co_request r Inner Join
                    moodle.mdl_user u On u.id = r.userid
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
            $time_created = strftime(get_string('strftimedatetime'),$r->timecreated);
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
}