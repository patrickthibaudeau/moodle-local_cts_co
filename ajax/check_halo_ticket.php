<?php
use local_cts_co\haloitsm;

include_once('../../../config.php');

global $DB, $USER;

$ticket_id = optional_param('ticket', 0, PARAM_INT);

$context = context_system::instance();

if ($ticket_id) {
    // First check to see if the ticket already exists as a request
    if (!$found = $DB->get_record('cts_co_request', ['halo_ticket_id' => $ticket_id])) {
        $HALO = new haloitsm();
        $ticket = $HALO->get_ticket($ticket_id);
        if ($ticket) {
            echo json_encode($ticket);
        } else {
            echo 0;
        }
    } else {
        echo -1;
    }

} else {
    echo 0;
}

