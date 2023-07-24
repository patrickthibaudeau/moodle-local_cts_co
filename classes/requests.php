<?php

namespace local_cts_co;

use local_cts_co\status;

class requests
{
    /**
     * @var string
     */
    protected $table;


    /**
     * Return requests based on status. Used with Kanban Board
     * @param $status
     * @param $show_all
     * @return array
     * @throws \dml_exception
     */
    public function get_based_on_status($status, $show_all = false)
    {
        global $DB;
        $sql = "SELECT * FROM {cts_co_request} WHERE status_code =$status ";
        if ($status == status::STEP_COMPLETED && !$show_all) {
            $sql .= " AND (timecreated BETWEEN UNIX_TIMESTAMP(CURDATE()) AND UNIX_TIMESTAMP(CURDATE() + INTERVAL 1 DAY))";

        }
        $sql .= " ORDER BY timecreated ASC";
        $results = $DB->get_records_sql($sql);
        // Reset array
        $results = array_values($results);
        return $results;
    }
}