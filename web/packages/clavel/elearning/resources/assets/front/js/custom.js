$(".nav-pills").attr("id", "mainMenu");

$(".nav-pills").children("LI").each(function() {
    $(this).addClass("dropdown-full-color dropdown-quaternary");
});

$(document).ready(function() {


    resizeWindowResolveFooter();
    $(window).resize(function() {
        resizeWindowResolveFooter();
    });

    $(".caret").remove();

});

function resizeWindowResolveFooter() {
    var heightDoc = $(window).height();
    var heightWrapper = $(".body").height();
    var heightFooter = $("#footer").height();
    var heightMain = $(".main").height();

    if(heightDoc>(heightWrapper-heightFooter)) {
        $(".main").css("min-height", heightMain + (heightDoc - heightWrapper));
    }
}
