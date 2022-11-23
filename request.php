<?php

require_once("../../config.php");

require_once($CFG->dirroot . "/local/cts_co/classes/forms/request_form.php");
require_once($CFG->dirroot . "/local/cts_co/classes/class.html2text.inc");

use local_cts_co\base;
use local_cts_co\haloitsm;
use local_cts_co\jira;
use local_cts_co\request;

global $CFG, $OUTPUT, $USER, $PAGE, $DB, $SITE;

$id = optional_param('id', 0, PARAM_INT);

$context = CONTEXT_SYSTEM::instance();

require_login(1, false);

$formdata = new stdClass();
$formdata->halo_ticket_id = '';
$formdata->description_editor['text'] = '';
$formdata->userid = [$USER->id => fullname($USER) . '(' . $USER->email . ')'];
$formdata->summary = get_string('summary_title', 'local_cts_co');

$mform = new \local_cts_co\request_form(null, array('formdata' => $formdata));
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/cts_co/index.php');
} else if ($data = $mform->get_data()) {
    // Set objects for HALO and JIRA
    $HALO = new haloitsm();
    $JIRA = new jira();
    $REQUEST = new request();
    // Make sure a userid is set
    if (!$data->userid) {
        $data->userid = $USER->id;
    }

    $description = $data->description_editor['text'];

    // Create HALO ticket
    if (!$data->halo_ticket_id) {
        $new_ticket = $HALO->create_ticket($USER->username, $data->summary, $description);
    } else {
        $new_ticket = $HALO->get_ticket($data->halo_ticket_id);
    }

    if (is_object($new_ticket)) {
        // Remove HTML tags for JIRA
        $H2T = new html2text($description);
        $jira_description = $H2T->get_text();
        // Add HALO Ticket ID to JIRA description
        $jira_description .= "\n\nHalo Ticket ID: " . $new_ticket->id;
        // Create JIRA issue
        $new_jira_issue = $JIRA->create_issue($data->summary . ' - SR-' . $new_ticket->id, $jira_description);

        // Create request record
        $params = new stdClass();
        $params->userid = $data->userid;
        $params->summary = $data->summary . ' - SR-' . $new_ticket->id;
        $params->description = $description;
        $params->halo_ticket_id = $new_ticket->id;
        $params->jira_issue_id = $new_jira_issue->id;
        $params->jira_issue_key = $new_jira_issue->key;
        $params->jira_issue_url = $new_jira_issue->self;
        $params->usermodified = $USER->id;
        $params->timecreated = strtotime($new_ticket->dateoccurred);

        $REQUEST->insert_record($params);

        redirect($CFG->wwwroot . '/local/cts_co/index.php');
    } else {
        \core\notification::error('Could not find user in HALO');
    }


} else {
    $mform->set_data($mform);
}

$PAGE->requires->js('/local/cts_co/js/request.js', true);
$PAGE->requires->css('/local/cts_co/css/request.css');

base::page(
    '/local/cts_co/request.php',
    get_string('request_form', 'local_cts_co'),
    '',
    $context,
    'standard'
);


echo $OUTPUT->header();
//**********************
//*** DISPLAY HEADER ***
//

$mform->display();
base::getAlertModal(
    'cts-alert',
    get_string('halo_ticket_not_found_title', 'local_cts_co'),
    get_string('halo_ticket_not_found', 'local_cts_co')
);

//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();
?>