<?php

require_once('../../config.php');

use local_cts_co\request;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$id = required_param('id', PARAM_INT);

 \local_cts_co\base::page($CFG->wwwroot . '/local/cto_co/index.php', get_string('pluginname', 'local_cts_co'), '', $context);

 // Load JS
$PAGE->requires->css('/local/cts_co/css/jquery.roadmap.min.css');
$PAGE->requires->js('/local/cts_co/js/jquery.roadmap.min.js', true);
$PAGE->requires->js('/local/cts_co/js/details.js', true);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$output = $PAGE->get_renderer('local_cts_co');
$details = new \local_cts_co\output\details($id);
echo $output->render_details($details);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
