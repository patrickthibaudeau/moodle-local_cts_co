$(document).ready(function () {
    wwwroot = M.cfg.wwwroot;
    $('#id_halo_ticket_id').on('change', function(){
        $('#id_halo_ticket_id').removeClass('halo_ticket_not_found');
        $('#id_halo_ticket_id').removeClass('halo_ticket_found');
        $('#id_halo_ticket_id').addClass('blinking');
        $.ajax({
            url: wwwroot + '/local/cts_co/ajax/check_halo_ticket.php?ticket=' + $(this).val(),
            cache: false,
            success: function(result){
                if (result == 1) {
                    $('#id_halo_ticket_id').removeClass('blinking');
                    $('#id_halo_ticket_id').removeClass('halo_ticket_not_found');
                    $('#id_halo_ticket_id').addClass('halo_ticket_found');
                } else {
                    $('#id_halo_ticket_id').val('');
                    $('#id_halo_ticket_id').removeClass('blinking');
                    $('#id_halo_ticket_id').removeClass('halo_ticket_found');
                    $('#id_halo_ticket_id').addClass('halo_ticket_not_found');
                    alert('Please enter a valid ticket number or leave empty to create a new ticket');
                }
            }
        });
    });
});