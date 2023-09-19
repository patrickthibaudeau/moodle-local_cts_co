<?php

require_once('../../config.php');
require_once('classes/class.html2text.inc');

use local_cts_co\haloitsm;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$ticket_id = required_param('ticketid', PARAM_INT);

\local_cts_co\base::page($CFG->wwwroot . '/local/cto_co/actions.php', 'Actions', 'Actions', $context);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$HALO = new haloitsm();

$actions = $HALO->get_actions($ticket_id);

$actions_reversed = array_reverse($actions->actions);
$timeline = array();
$i = 0;
foreach ($actions_reversed as $action) {
    if ($action->old_status != $action->new_status) {
        $timeline[$i]['date'] = date('l F d, Y h:i A', $HALO->convert_halo_date_to_timestamp($action->actionarrivaldate));
        $timeline[$i]['content'] = $action->new_status_name;
        $timeline[$i]['timestamp'] = $HALO->convert_halo_date_to_timestamp($action->actionarrivaldate);
        $i++;
    }
}
$end_key = count($timeline) - 1;
$time_taken = $timeline[$end_key]['timestamp'] - $timeline[0]['timestamp'];

print_object($HALO->convert_seconds_to_days($time_taken));
print_object($timeline);


//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>