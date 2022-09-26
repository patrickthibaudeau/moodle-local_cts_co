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
}