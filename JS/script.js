function modalShow(data)
{
    /*
    *data - Text data to be shown in the modal
    *Opens the modal window
    *Displays the 'data' in the modal window
    */
    $("#modal-body").html(data);
    $("#modal").show(200);

    return true;
}

$("#modal-close").click(function () {
    //Click event listener for the close button of the modal window
    $("#modal").hide(200);

    //POS page:
    //Triggers form reset on the POS page
    $("#form-pos").trigger("reset");
});


function reHTML(data, page){
    /*
    *data - The data to be sent to page
    *page - The relative link or the url of the page to which the data is to be sent
    *Function does not return anything
    *The request to the page expects an HTML response 
    *Handles the loading ring
    */
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