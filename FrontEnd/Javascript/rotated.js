$(document).ready(function () {

    wait(10);
    $("#resume").addClass("resume-container");
    $("#resume").removeClass("resume-container-preload");

})


function wait(ms) {
    var start = new Date().getTime();
    var end = start;
    while (end < start + ms) {
        end = new Date().getTime();
    }
}