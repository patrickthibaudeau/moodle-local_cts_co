$(document).ready(function () {

    let events = JSON.parse($('#timeline').val());
    $('#my-timeline').roadmap(events, {
        eventsPerSlide: 6,
        slide: 1,
        prevArrow: '<i class="material-icons">keyboard_arrow_left</i>',
        nextArrow: '<i class="material-icons">keyboard_arrow_right</i>',
        onBuild: function() {
            console.log('onBuild event')
        }
    });
});