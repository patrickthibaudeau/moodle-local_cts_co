$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;

    let request_table = $('#cts_co_my_request_table').DataTable({
        dom: 'Blfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/cts_co/ajax/dashboard.php",
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            { "data": "timecreated" },
            { "data": "for_user" },
            { "data": "halo_ticket_id" },
            { "data": "jira_issue_key" },
            { "data": "status" },
        ],
        'columnDefs': [ {
            "searchable": false,
            "targets": [0]
        }],
        buttons: [
            'excelHtml5',
        ],
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 10,
        stateSave: false,
    });

    // Add some top spacing
    $('.dataTables_length').css('margin-top', '.5rem');

    $('.buttons-html5').addClass('btn-outline-primary');
    $('.buttons-html5').removeClass('btn-secondary');

    // Click on row
    $('#cts_co_my_request_table').on('click', 'tbody tr', function (){
        var row = request_table.row($(this)).data();
        let spinner =  $('<div></div>').attr('class', 'loading');
        spinner.appendTo('body');
        window.location.href = wwwroot + "/local/cts_co/details.php?id=" + row.id;   //full row of array data
    });
});