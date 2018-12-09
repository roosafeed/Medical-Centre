//global vars
//Last sync times
var sync_med = 0,
    sync_stock = 0,
    sync_exp = 0;

var sync_wait = 600;    //seconds (10 minutes)

//Request address
var pharm_url = "/pharm_handle.php";

function reJSON(data)
{
        $("#loading").show();
        $.ajax({
            method: 'POST',
            data: data,
            url: pharm_url,
            dataType: 'JSON',
            async: false
        }).always(function () {
            $("#loading").hide();
        }).done(function (data, status, xhr) {
            alert(JSON.stringify(data));////////////
            return JSON.stringify(data);
        }).fail(function (xhr, status, errotThrown) {
            modalShow("Error: " + status + "\n" + errorThrown);
            return false;
        });
}

function fillJSON(dataType, result_cont){
    //dataType - Data to be retrieved (med/stock/exp)
    //result_cont - Container ID for the result to be loaded in
    //ToDo  : Pagination to handle large data
    var ret;
    var sendData = "type=" + dataType,
        output = "",
        cont_id = "#cont-" + result_cont,
        selector = cont_id + " table tbody";

    if(((Math.floor(new Date().getTime() / 1000) - sync_med > sync_wait) && dataType == "med") ||
    ((Math.floor(new Date().getTime() / 1000) - sync_exp > sync_wait) && dataType == "exp") ||
    ((Math.floor(new Date().getTime() / 1000) - sync_stock > sync_wait) && dataType == "stock"))
    {

        $("#loading").show();
        $.ajax({
            method: 'POST',
            data: sendData,
            url: pharm_url
        }).done(function (data, status, xhr) {
            //alert(JSON.stringify(data));////////////   
            if (data == "") {
                modalShow("Empty. No data to be fetched.");
            }
            else if (data == "Unauthorized") {
                modalShow("Error: Unauthorized access.");
            }
            else {
                var retObj = JSON.parse(data);  //returned object
                //Populate the respective tables with data.
                switch (dataType) {
                    case "med":
                        sync_med = Math.floor(new Date().getTime() / 1000);
                        for (var x in retObj) {
                            output += "<tr>";
                            output += "<td>" + retObj[x].name + "</td>";
                            output += "<td>" + retObj[x].man + "</td>";
                            output += "</tr>";
                        }
                        break;
                    case "stock":
                        sync_stock = Math.floor(new Date().getTime() / 1000);
                        for (var x in retObj) {
                            output += "<tr>";
                            output += "<td>" + retObj[x].name + "</td>";
                            output += "<td>" + retObj[x].man + "</td>";
                            output += "<td>" + retObj[x].arr + "</td>";
                            output += "<td>" + retObj[x].exp + "</td>";
                            output += "<td>" + retObj[x].num + "</td>";
                            output += "<td>" + retObj[x].tot + "</td>";
                            output += "<td>" + retObj[x].worth + "</td>";
                            output += "</tr>";
                        }
                        break;
                    case "exp":
                        sync_exp = Math.floor(new Date().getTime() / 1000);
                        for (var x in retObj) {
                            output += "<tr>";
                            output += "<td>" + retObj[x].name + "</td>";
                            output += "<td>" + retObj[x].man + "</td>";
                            output += "<td>" + retObj[x].arr + "</td>";
                            output += "<td>" + retObj[x].exp + "</td>";
                            output += "</tr>";
                        }
                        break;
                    default:
                        return false;   //invalid data requested
                }

                $(selector).html(output);
            }
        }).fail(function (xhr, status, errorThrown) {
            modalShow("Error: " + status + "\n" + errorThrown);
            return false;
        }).always(function () {
            $("#loading").hide();
        });


        /*
        ret = reJSON(sendData);
        if(ret != false)
        {
            //Create a flex table with the JSON data
            //Create the table rows
            //Update the last sync time
            alert(ret); //////////
            var retObj = JSON.parse(ret);            
            switch(data){
                case "med":
                    sync_med = Math.floor(new Date().getTime() / 1000);
                    for (var x in retObj) {
                        output += "<tr>";
                        output += "<td>" + retObj[x].name + "</td>";
                        output += "<td>" + retObj[x].man + "</td>";
                        output += "</tr>";
                    }
                    break;
                case "stock":
                    sync_stock = Math.floor(new Date().getTime() / 1000);
                    for (var x in retObj) {
                        output += "<tr>";
                        output += "<td>" + retObj[x].name + "</td>";
                        output += "<td>" + retObj[x].man + "</td>";
                        output += "<td>" + retObj[x].arr + "</td>";
                        output += "<td>" + retObj[x].exp + "</td>";
                        output += "<td>" + retObj[x].num + "</td>";
                        output += "<td>" + retObj[x].tot + "</td>";
                        output += "<td>" + retObj[x].worth + "</td>";
                        output += "</tr>";
                    }
                    break;
                case "exp":
                    sync_exp = Math.floor(new Date().getTime() / 1000);
                    for (var x in retObj) {
                        output += "<tr>";
                        output += "<td>" + retObj[x].name + "</td>";
                        output += "<td>" + retObj[x].man + "</td>";
                        output += "<td>" + retObj[x].arr + "</td>";
                        output += "<td>" + retObj[x].exp + "</td>";
                        output += "</tr>";
                    }
                    break;
                default:
                    return false;   //invalid data requested
            }

            $(selector).html(output);
            
        }
        else{
            return false;   //reJSON returned false
        }*/
    }
    return true;
}

$(function () {
    //Check to see if the location contains a hash
    //Call the respective function
    var hash = window.location.hash.substr(1);
    if(hash != "" && hash != "home")
    {
        $(".pharm-functions #" + hash).show(800);
        pos = $(".pharm-functions #" + hash).offset().top;
        $("HTML, BODY").animate({ scrollTop: pos }, 1000);
        fillJSON(hash.replace("list-", ""), hash);
    }    
});

$(".button").click(function () {
    var bId = String(this.id),
        divClass = bId.replace("toggle-", ""),
        pos = 0,
        showTime = 800,
        data = divClass.replace("list-", "");

    $(".pharm-functions .function").hide();
    $(".pharm-functions #" + divClass).show(showTime);
    pos = $(".pharm-functions #" + divClass).offset().top;
    $("HTML, BODY").animate({ scrollTop: pos }, 1000);
    window.location.hash = divClass;
    fillJSON(data, divClass);
});

$(".admin-function-close").click(function () {
    $(".function").hide(500);
    window.location.hash = "home";
});


