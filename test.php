<?php

require_once('../../config.php');

use local_cts_co\haloitsm;
use local_cts_co\jira;

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
$JIRA = new jira();


print_object($JIRA->get_issue('YULRN-339'));
//$user = $HALO->get_user_by_username('jelder');
//print_object($user);


//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
