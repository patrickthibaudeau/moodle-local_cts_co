<?php

require_once('../../config.php');
require_once('classes/class.html2text.inc');
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

$html = '<p dir="ltr" style="text-align: left;">Hello,</p>
<p dir="ltr" style="text-align: left;">This is what I want to do</p>
<p dir="ltr" style="text-align: left;">Get thsi stuff<br></p>
<p dir="ltr" style="text-align: left;"><br></p>';

$H2T = new html2text($html);
$text = $H2T->get_text();
echo '<textarea>' . $text . '</textarea>';

//print_object($JIRA->get_issue('CTSCO-2'));
//print_object($HALO->add_action('thibaud', '0110043' , 'This is a note'));
print_object($HALO->get_users('thibaud'));
//$new_ticket = $HALO->create_ticket('thibaud', 'New computer request', "I need a new computer<br>MacBook Pro 14");

//print_object($new_ticket);

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
