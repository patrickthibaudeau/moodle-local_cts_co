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
    public function __construct($id = 0) {
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
    public function insert_record($data) {
        global $DB;

        $timecreated = time();
        $timemodified = $timecreated;

        $data->timecreated = $timecreated;
        $data->timemodified = $timemodified;

        if ($id = $DB->insert_record($this->table, $data)) {
            // Create status record.
            $STATUS = new status();

            $status_data = new \stdClass();
            $status_data->request_id = $id;
            $status_data->status = 'New';

            $STATUS->insert_record($status_data);

            return $id;
        }

        return false;
    }

    /**
     * Returns record data and statuses associated to this request.
     * @return \stdClass
     */
    public function get_request() {
        $STATUS = new status();
        $request = new \stdClass();

        $request->request = $this->record;
        $request->statuses = $STATUS->get_statuses($this->id);

        return $request;
    }
}