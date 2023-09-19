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

print_object($actions_reversed);

//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>