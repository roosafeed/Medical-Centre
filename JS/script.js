function modalShow(data)
{
    $("#modal-body").html(data);
    $("#modal").show(200);
    return true;
}



$("#modal-close").click(function () {
    $("#modal").hide(200);
});

function reHTML(data, page){
    $("input").attr("disabled", "disabled");
    $("option").attr("disabled", "disabled");
    $("textarea").attr("disabled", "disabled");
    $("#loading").show();

    $.ajax({
        method: 'POST',
        data: data,
        url: page,
        dataType: 'HTML'
    }).done(function (data, status, xhr) {
        //alert(data);
        modalShow(data);
    }).fail(function (xhr, status, errorThrown) {
        //alert("Error: " + status + "\n" + errorThrown);
        modalShow("Error: " + status + "\n" + errorThrown);
    }).always(function () {
        $("input").removeAttr("disabled");
        $("option").removeAttr("disabled");
        $("textarea").removeAttr("disabled");
        $("#loading").hide();
    });
}