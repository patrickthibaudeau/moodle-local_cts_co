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

use local_cts_co\haloitsm;
use local_cts_co\request;
use local_cts_co\status;
use local_cts_co\jira;

class details implements \renderable, \templatable
{

    /**
     * @var int
     */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param \renderer_base $output
     * @return type
     * @global \moodle_database $DB
     * @global type $USER
     * @global type $CFG
     */
    public function export_for_template(\renderer_base $output)
    {
        global $USER, $CFG, $DB;



        // Is user an agent
        $context = \context_system::instance();
        $HALO = new haloitsm();

        $timeline_data = $HALO->get_timeline($this->id);
        if ($timeline_data->number_of_items == 0) {
            $timeline = false;
        } else {
            $timeline = json_encode($timeline_data->timeline);
        }
        $data = [
            'ticket_id' => $this->id,
            'timeline' => $timeline,
            'number_of_items' => $timeline_data->number_of_items,
        ];

        return $data;
    }

}
