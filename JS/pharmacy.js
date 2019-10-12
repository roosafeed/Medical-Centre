//global vars
//Last sync times
var sync_med = 0,
    sync_stock = 0,
    sync_exp = 0,
    sync_trans = 0;

var sync_wait = 600,    //seconds (10 minutes)
    sync_auto_trans = 10;

//Request address
var pharm_url = "/pharm_handle.php";
var stockObj = "[]",
    transObj = "[]",
    stock_min_id = 0,
    stock_max_id = 0,
    stock_sum_worth = 0,
    trans_min_id = 0,
    trans_max_id = 0;

var trans_Interval_var; //setInterval variable

function fillJSON(dataType, result_cont){
    //dataType - Data to be retrieved (med/stock/exp)
    //result_cont - Container ID for the result to be loaded in
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
            //ToDo: handle db query error 
            if (data == "" || data == "[]") {
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
                        output = populate_med(retObj);
                        break;
                    case "stock":
                        stockObj = retObj;
                        output = populate_stock(retObj);
                        break;
                    case "exp":
                        output = populate_exp(retObj);
                        break;
                    default:
                        return false;   //invalid data requested
                }

                $(selector).html(output);

                $(".stock-mod-form").submit(function (e) {
                    //handle the form submit in a separate function
                    //pass the event and the callee form to the function
                    mod_formSubmitHandle(e, this);
                });
            }
        }).fail(function (xhr, status, errorThrown) {
            modalShow("Error: " + status + "\n" + errorThrown);
            return false;
        }).always(function () {
            $("#loading").hide();
        });

    }
    return true;
}

$(function () {
    //Check to see if the location contains a hash
    //Call the respective function
    var hash = window.location.hash.substr(1);
    if (hash != "" && hash != "home") {
        $(".pharm-functions #" + hash).show(800);
        pos = $(".pharm-functions #" + hash).offset().top;
        $("HTML, BODY").animate({ scrollTop: pos }, 1000);
        if (hash == "list-trans") {
            trans_Interval_var = setInterval(function () {
                loadRecords();
            }, sync_auto_trans * 1000);
            loadRecords();
        }
        else {
            fillJSON(hash.replace("list-", ""), hash);
        }
        
    }
});

$(".button").click(function () {
    //clear the setInterval on loadRecords() if it is set on page load
    clearInterval(trans_Interval_var);

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
    if (divClass == "list-trans") {
        trans_Interval_var = setInterval(function () {
            //$("div#cont-list-trans p#trans-status").text("Refreshing...");
            loadRecords();
        }, sync_auto_trans * 1000);
        loadRecords();
    }
    else {
        fillJSON(data, divClass);
    }

});

$(".admin-function-close").click(function () {
    $(".function").hide(500);
    window.location.hash = "home";
});

//Load next batch of stocks on #stock-more button click
//Add to the stock table 
//If no more data display message in #stock-msg
$("#stock-more").click(function () {
    $("#loading").show();
    $("input").attr("disabled", "disabled");

    $.ajax({
        method: 'POST',
        url: pharm_url,
        data: "more=stock&min-id=" + stock_min_id + "&max-id=" + stock_max_id
    }).done(function (data) {
        if (data == "" || data == "[]") {
            $("#stock-msg").text("No more data");
        }
        else if (data == "Unauthorized") {
            modalShow("Error: Unauthorized access.");
        }
        else {
            var retObj = JSON.parse(data),
                output = "";
            output = populate_stock(retObj);

            $("#cont-list-stock table tbody").html($("#cont-list-stock table tbody").html() + output);

            for (var x in retObj) {
                stockObj.push(retObj[x]);
            }
        }



    }).fail(function (xhr, status, errorThrown) {
        modalShow("Error: " + status + "\n" + errorThrown);
    }).always(function () {
        $("#loading").hide();
        $("input").removeAttr("disabled");
    });
});

//stock medicine search autocomplete
$("#stock-search-name").autocomplete({
    source: function (request, response) {
        $.getJSON("/med_search.php", { term: request.term, type: 'new-med' },
            response);
    },
    minLength: 3
}).data("ui-autocomplete")._renderItem = function (ul, item) {
    return $("<li>")
    .append("<div>" + item.value + "<br>" + item.desc + "</div></li>")
    .appendTo(ul);
};


//stock seach form handler
$("#stock-search-form").submit(function (e) {
    e.preventDefault();
    var search_term = $(this).children("input[name=stock-search-name]").val(),
        sendTerm = "stock-search-term=" + search_term;


    $("#loading").show();
    $("input").attr("disabled", "disabled");

    $.ajax({
        method: 'POST',
        url: pharm_url,
        data: sendTerm
    }).done(function (data, status, xhr) {
        console.log("'" + data + "'");
        if (data == "" || data == "[]") {
            modalShow("Empty. No data to be fetched.");
        }
        else if (data == "Unauthorized") {
            modalShow("Error: Unauthorized access.");
        }
        else {
            stock_sum_worth = 0;
            var retObj = JSON.parse(data);
            var output = populate_stock(retObj);
            $("#cont-list-stock table tbody").html(output);
            $("#cont-list-stock table tfoot tr#stock-more-tr").hide();
            $(".stock-mod-form").submit(function (e) {
                //handle the form submit in a separate function
                //pass the event and the callee form to the function
                mod_formSubmitHandle(e, this);
            });
        }
    }).fail(function (xhr, status, errorThrown) {
        modalShow(errorThrown + "\n" + status)
    }).always(function () {
        $("#loading").hide();
        $("input").removeAttr("disabled");
    });
});

//stock search close button handler
//restore stock list table values from stockObj
$("#stock-search-close").click(function () {
    stock_sum_worth = 0;
    $("#stock-search-name").val("");
    var output = populate_stock(stockObj);
    $("#cont-list-stock table tbody").html(output);
    $("#cont-list-stock table tfoot tr#stock-more-tr").show();
    $(".stock-mod-form").submit(function (e) {
        //handle the form submit in a separate function
        //pass the event and the callee form to the function
        mod_formSubmitHandle(e, this);
    });
});


function mod_formSubmitHandle (e, obj) {
    //Handle form submit to decrease stock number
    //Modify stock amount
    var data = $(obj).serialize(),
        num = Number($(obj).children("input[name=stock-mod-num]").val()),
        id = $(obj).children("input[name=stock-mod-id]").val(),
        numInput = $(obj).children("input[name=stock-mod-num]");

    e.preventDefault();
    //check if the number is greater than actual stock number
    for (var x in stockObj) {
        if (stockObj[x].id == id) {
            console.log(num + " -> " + stockObj[x].num);
            //convert to number before checking
            //otherwise may result false on some cases
            if (Number(stockObj[x].num) >= num) {

                var numCell = $("#stock-num-" + stockObj[x].id),
                    worthCell = $("#stock-worth-" + stockObj[x].id),
                    init_num = Number(stockObj[x].num),
                    init_tot = stockObj[x].tot,
                    init_x = x;

                //ajax call to server
                $("#loading").show();
                $("input").attr("disabled", "disabled");
                $.ajax({
                    method: 'POST',
                    url: pharm_url,
                    data: data
                }).done(function (data, status, xhr) {
                    if (data == "200") {
                        //200 will be returned on successfull modification
                        numInput.val("");
                        numCell.text((init_num - num) + "/" + init_tot);
                        stockObj[init_x].num = init_num - num;
                        stock_sum_worth -= stockObj[init_x].worth;
                        stockObj[init_x].worth = Math.round(((Number(stockObj[init_x].price) / Number(stockObj[init_x].tot)) * Number(stockObj[init_x].num)) * 100) / 100;
                        worthCell.text(stockObj[init_x].worth);
                        stock_sum_worth += stockObj[init_x].worth;
                        stock_sum_worth = Math.round(stock_sum_worth * 100) / 100;
                        $("#stock-worth-sum").text(stock_sum_worth);
                    }
                    if (data == "Unauthorized") {
                        modalShow("Error: Unauthorized access.");
                    }
                }).fail(function (xhr, status, errorThrown) {
                    //ajax call failed
                    modalShow(errorThrown + "\n" + status);
                }).always(function () {
                    $("#loading").hide();
                    $("input").removeAttr("disabled");
                });
            }
            else {
                //number is less than the stock
                modalShow("Error: Value greater than stock.");
                return false;
            }
        }
    }
}

function populate_stock(retObj){
    var output = "";
    sync_stock = Math.floor(new Date().getTime() / 1000);
    
    if(stock_max_id == 0)
    {
        stock_max_id = Number(retObj[0].id);
    }
    if(stock_min_id == 0)
    {
        stock_min_id = Number(retObj[0].id);
    }    
    
    for (var x in retObj) {
        output += "<tr>";
        output += "<td>" + retObj[x].name + "<br /> (" + retObj[x].man + ")</td>";
        output += "<td>" + retObj[x].seller + "</td>";
        output += "<td>" + retObj[x].arr + "</td>";
        output += "<td>" + retObj[x].exp + "</td>";
        output += "<td id=\"stock-num-" + retObj[x].id + "\">" + retObj[x].num + "/" + retObj[x].tot + "</td>";
        output += "<td>" + retObj[x].price + "</td>";
        output += "<td id=\"stock-worth-" + retObj[x].id + "\">" + retObj[x].worth + "</td>";
        output += "<td><form class=\"stock-mod-form\" method=\"post\" action=\"\">";
        output += "<input type=\"number\" name=\"stock-mod-num\" />";
        output += "<input type=\"hidden\" name=\"stock-mod-id\" value=\"" + retObj[x].id + "\" />";
        output += "<input type=\"submit\" name=\"stock-mod\" value=\"Del\" /> </form></td>";
        output += "</tr>";
        stock_sum_worth += Number(retObj[x].worth);
        if (Number(retObj[x].id) < stock_min_id) {
            stock_min_id = Number(retObj[x].id);
        }
        if (Number(retObj[x].id) > stock_max_id) {
            stock_max_id = Number(retObj[x].id);
        }
    }

    stock_sum_worth = Math.round(stock_sum_worth * 100) / 100;
    $("#stock-worth-sum").text(stock_sum_worth);
    return output;
}

function populate_exp(retObj){
    var output = "";
    sync_exp = Math.floor(new Date().getTime() / 1000);
    for (var x in retObj) {
        output += "<tr>";
        output += "<td>" + retObj[x].name + "<br />(" + retObj[x].man + ")</td>";
        output += "<td>" + retObj[x].seller + "</td>";
        output += "<td>" + retObj[x].num + "/" + retObj[x].tot + "</td>";
        output += "<td>" + retObj[x].arr + "</td>";
        output += "<td>" + retObj[x].exp + "</td>";
        output += "</tr>";
    }
    return output;
}

function populate_med(retObj){
    output = "";
    sync_med = Math.floor(new Date().getTime() / 1000);
    for (var x in retObj) {
        output += "<tr>";
        output += "<td>" + retObj[x].name + "</td>";
        output += "<td>" + retObj[x].man + "</td>";
        output += "<td>" + retObj[x].bnum + "</td>";
        output += "</tr>";
    }
    return output
}

function trans_auto_sync(data)
{
    $.ajax({
        method: 'POST',
        data: data,
        url: pharm_url,
        dataType: 'JSON'
    }).always(function () {
    }).done(function (data, status, xhr) {
        if (data == "" || data == "[]") {
            //modalShow("Empty. No data to be fetched.");
        }
        else if (data == "Unauthorized") {
            modalShow("Error: Unauthorized access.");
        }
        else {
            var retObj = JSON.parse(data);  //returned object
            //Populate the trans table with data.
        }

    }).fail(function (xhr, status, errotThrown) {
        modalShow("Error: " + status + "\n" + errorThrown);
        return false;
    });
}

//Clear the setInterval on loadRecords()
$("#list-trans-close").click(function () {
    clearInterval(trans_Interval_var);
});

function loadRecords(more)
{
    //more -> TRUE/FALSE
    //        TRUE if function was invoked by 'Load Older Data' button
    //        Default is false - optional
    more = more || false;   //ES5 for older browser & IE support
      
    
    if (!more) {
        //$("div#cont-list-trans p#trans-status").text("Refreshing...");
        var sendData = "record=trans&max=" + trans_max_id;
    }
    else {
        $("input").attr("disabled", "disabled");
        $("div#cont-list-trans p#trans-status").text("Loading...");
        var sendData = "record=trans&min=" + trans_min_id;
    }
    
    var output = "";

    $.ajax({
        method: 'POST',
        data: sendData,
        url: pharm_url
    }).done(function (data, status, xhr) {
        if (data == "" || data == "[]") {
            //modalShow("No data");
        }
        else {
            var transObj = JSON.parse(data);
            trans_min_id = transObj[0].id;
            for (var x in transObj) {
                output += '<li><div class="trans">';
                output += '<div class="med">' + transObj[x].medname + ' (' + transObj[x].man + ')</div>';
                output += '<div class="num">' + transObj[x].num + '</div>';
                output += '<div class="date">' + transObj[x].date + '</div>';
                output += '<div class="vendor">' + transObj[x].vendor + ' (' + transObj[x].vid + ')' + '</div>';
                output += '<div class="more">';
                output += (transObj[x].more == "POS") ? "POS to " + transObj[x].buyer + " (" + transObj[x].bid + ")" : "Modified in 'ALL Stocks' tab";
                output += '</div>';
                output += '</div></li>';

                if (Number(transObj[x].id) < trans_min_id) {
                    trans_min_id = Number(transObj[x].id);
                }
                if (Number(transObj[x].id) > trans_max_id) {
                    trans_max_id = Number(transObj[x].id);
                }
            }

            if (!more) {
                $("div#cont-list-trans ul").prepend(output);
            }
            else {
                $("div#cont-list-trans ul").append(output);
                $("input").removeAttr("disabled");
            }

        }
        $("div#cont-list-trans p#trans-status").text(" ");
        $("input").removeAttr("disabled");
    }).fail(function (xhr, status, errorThrown) {
        $("div#cont-list-trans p#trans-status").text("Error. " + errorThrown);
        $("input").removeAttr("disabled");
    })
}

//Load Older Data 
//Load transactions below the trans_min_id
$("#trans-more").click(function () {
    //clearInterval(trans_Interval_var);
    loadRecords(true);
});