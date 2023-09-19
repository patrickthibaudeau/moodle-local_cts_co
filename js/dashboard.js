$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;

    $('#get_ticket_details').on('click', function () {
        let ticket_id = $('#ticket_id').val();
        window.location.href = wwwroot + "/local/cts_co/status.php?id=" + ticket_id;   //full row of array data
    });
});