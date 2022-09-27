<?php

require_once('../../config.php');

use local_cts_co\haloitsm;
use local_cts_co\jira;
use local_cts_co\status;

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
$STATUS = new status();


//print_object($JIRA->get_issue('CTSCO-8'));
print_object($HALO->get_user_by_username('jelder'));
//print_object($HALO->get_ticket_type(29));
//$new_ticket = $HALO->create_ticket('aalaily', 'New computer request', "I need a new computer<br>MacBook Pro 14");



//$jira_description = str_replace('<br>', "\n", $new_ticket->details);
//$jira_description .= "\n\nHalo Ticket ID: " . $new_ticket->id;
// Remove HTML tags
//$jira_description = strip_tags($jira_description);

//$new_jira_issue = $JIRA->create_issue($new_ticket->summary, $jira_description);
//print_object($new_jira_issue);

//$STATUS->update_status(3, 'CTSCO-7');

//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
