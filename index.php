<?php

require_once('../../config.php');

use local_cts_co\request;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, true);
$context = context_system::instance();

 \local_cts_co\base::page($CFG->wwwroot . '/local/cto_co/index.php', get_string('pluginname', 'local_cts_co'), '', $context);

 // Load JS
$PAGE->requires->css('/local/cts_co/css/request.css');
$PAGE->requires->js('/local/cts_co/js/dashboard.js', true);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$output = $PAGE->get_renderer('local_cts_co');
$dashboard = new \local_cts_co\output\dashboard();
echo $output->render_dashboard($dashboard);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
