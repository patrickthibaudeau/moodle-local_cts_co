$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    let events = JSON.parse($('#timeline').val());
    let numberOfItems = $("#number-of-items").val();
    // Number of events per slide should never be more than 9
    if (numberOfItems > 9) {
        numberOfItems = 9;
    }
    $('#my-timeline').roadmap(events, {
        eventsPerSlide: numberOfItems,
        slide: 1,
        prevArrow: '<i class="fa fa-angle-left"></i>',
        nextArrow: '<i class="fa fa-angle-right"></i>',
        onBuild: function() {
            console.log('onBuild event')
        }
    });

    $('#get_ticket_details').off();
    $('#get_ticket_details').on('click', function () {
        let ticket_id = $('#ticket_id').val();
        window.location.href = wwwroot + "/local/cts_co/status.php?id=" + ticket_id;   //full row of array data
    });
});