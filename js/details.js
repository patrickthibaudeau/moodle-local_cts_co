$(document).ready(function () {

    let events = JSON.parse($('#timeline').val());
    $('#my-timeline').roadmap(events, {
        eventsPerSlide: 8,
        slide: 1,
        prevArrow: '<i class="fa fa-angle-left"></i>',
        nextArrow: '<i class="fa fa-angle-right"></i>',
        onBuild: function() {
            console.log('onBuild event')
        }
    });
});