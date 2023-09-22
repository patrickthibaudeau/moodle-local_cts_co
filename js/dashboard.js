$(document).ready(function () {

    $('#get_ticket_details').on('click', function () {
        search_ticket();
    });

    $("input").on("keypress", function(event) {
        if (event.which === 13) {
            search_ticket();
        }
    });
});

function search_ticket() {
    let wwwroot = M.cfg.wwwroot;
    let ticket_id = $('#ticket_id').val();
    window.location.href = wwwroot + "/local/cts_co/status.php?id=" + ticket_id;   //full row of array data
}