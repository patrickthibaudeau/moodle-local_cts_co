<?php

require_once('../../config.php');

use local_cts_co\haloitsm;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

 \local_cts_co\base::page($CFG->wwwroot . '/local/cto_co/test.php', 'Test', 'Test', $context);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$HALO = new haloitsm();
print_object($HALO->get_users());
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
