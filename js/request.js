$(document).ready(function () {
    wwwroot = M.cfg.wwwroot;
    // Check to see if ticket exists in HALO
    $('#id_halo_ticket_id').on('change', function () {
        $('#id_halo_ticket_id').removeClass('halo_ticket_not_found');
        $('#id_halo_ticket_id').removeClass('halo_ticket_found');
        $('#id_halo_ticket_id').removeClass('halo_ticket_already_requested');
        $('#id_halo_ticket_id').addClass('blinking');

        $.ajax({
            url: wwwroot + '/local/cts_co/ajax/check_halo_ticket.php?ticket=' + $(this).val(),
            cache: false,
            success: function (result) {

                switch (result) {
                    case '0':
                        $('#id_halo_ticket_id').val('');
                        $('#id_halo_ticket_id').removeClass('blinking');
                        $('#id_halo_ticket_id').removeClass('halo_ticket_found');
                        $('#id_halo_ticket_id').addClass('halo_ticket_not_found');
                        // alert('Please enter a valid ticket number or leave empty to create a new ticket');
                        $('#cts-alert').modal('show');
                        break;
                    case '-1':
                        console.log(result);
                        $('#id_halo_ticket_id').val('');
                        $('#id_halo_ticket_id').removeClass('blinking');
                        $('#id_halo_ticket_id').removeClass('halo_ticket_found');
                        $('#id_halo_ticket_id').removeClass('halo_ticket_not_found');
                        $('#id_halo_ticket_id').addClass('halo_ticket_already_requested');
                        // alert('Please enter a valid ticket number or leave empty to create a new ticket');
                        $('#cts-exists').modal('show');
                        break;
                    default:
                        // Convert results to object
                        let ticket = JSON.parse(result);
                        // Add details to content
                        $('#id_description_editoreditable').html(ticket.details);
                        $('#id_halo_ticket_id').removeClass('blinking');
                        $('#id_halo_ticket_id').removeClass('halo_ticket_not_found');
                        $('#id_halo_ticket_id').addClass('halo_ticket_found');
                        break;
                }
            }
        });
    });

    // Full page spinner on save
    $('#id_submitbutton').on('click', function () {
        let spinner =  $('<div></div>').attr('class', 'loading');
        spinner.appendTo('body');
        $(this).hide();
    });
});