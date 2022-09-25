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


print_object($JIRA->get_issue('CTSCO-2'));
//$user = $HALO->get_user_by_username('jelder');
//print_object($HALO->get_ticket_type(29));
print_object($HALO->create_ticket('thibaud', 'New computer', 'I need a new computer<br>MacBook Pro 14'));


//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
