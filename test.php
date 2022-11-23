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

//$html = '<p dir="ltr" style="text-align: left;">Hello,</p>
//<p dir="ltr" style="text-align: left;">This is what I want to do</p>
//<p dir="ltr" style="text-align: left;">Get thsi stuff<br></p>
//<p dir="ltr" style="text-align: left;"><br></p>';
//
//$H2T = new html2text($html);
//$text = $H2T->get_text();
//echo '<textarea>' . $text . '</textarea>';
$issue = $JIRA->get_issue('CTSCO-19');
//print_object($issue);
$params = new stdClass();
foreach ($issue->comments as $comment) {
 if (!$found = $DB->get_record('cts_co_status', ['jira_comment_id' =>$comment->id])) {
  $params->jira_comment_id = $comment->id;
  $params->jira_comment = $comment->body;
  // only add 1 comment per status
  break;
 }
}
print_object($params);
//print_object(strtotime('2022-11-23T03:22:32.1991177Z'));
//$ticket = $HALO->get_ticket(0110310);
//print_object($ticket);

//print_object($JIRA->update_agent_from_halo(110052,'CTSCO-3'));
//print_object($JIRA->get_assignee_id('CTSCO-3', 'aalaily'));
//print_object($HALO->add_action('aalaily', '110310' , 'This is a note too'));
//print_object($HALO->get_user_by_username('aalaily'));
//$new_ticket = $HALO->create_ticket('rsambhi', 'New computer request', "I need a new computer<br>MacBook Pro 14");
//
//print_object($new_ticket);

//$jira_description = str_replace('<br>', "\n", $new_ticket->details);
//$jira_description .= "\n\nHalo Ticket ID: " . $new_ticket->id;
// Remove HTML tags
//$jira_description = strip_tags($jira_description);

//$new_jira_issue = $JIRA->create_issue($new_ticket->summary . ' - SR-' . $new_ticket->id, $jira_description);
//print_object($new_jira_issue);

//$STATUS->update_status(3, 'CTSCO-7');

//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
