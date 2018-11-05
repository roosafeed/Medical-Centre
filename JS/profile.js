$(".button").click(function () {
    var bId = String(this.id),
        divClass = bId.replace("toggle-", ""),
        pos = 0,
        showTime = 800;

    $("#profile-functions .function").hide();
    $("#profile-functions #" + divClass).show(showTime);
    pos = $("#profile-functions #" + divClass).offset().top;
    $("HTML, BODY").animate({ scrollTop: pos }, 1000);
});

$(".profile-function-close").click(function () {
    $(".function").hide(500);
});

$("#up-bio-form, #ch-pass-form, #ch-pass-form").submit(function (e) {
    var data = $(this).serialize();
    e.preventDefault();
    reHTML(data, '/profile_handle.php');
});