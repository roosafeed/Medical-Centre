$(".button").click(function () {
    var bId = String(this.id),
        divClass = bId.replace("toggle-", ""),
        pos = 0,
        showTime = 800;

    $(".admin-functions .function").hide();
    $(".admin-functions #" + divClass).show(showTime);
    pos = $(".admin-functions #" + divClass).offset().top;
    $("HTML, BODY").animate({ scrollTop: pos }, 1000);
});

$(".admin-function-close").click(function () {
    $(".function").hide(500);
});

$("#modal-close").click(function () {
    $("#modal").hide(200);
});

function modalShow(data)
{
    $("#modal-body").val(data);
    $("#modal").show(200);
    return true;
}

function reHTML(data){
    $("input").attr("disabled", "disabled");
    $("option").attr("disabled", "disabled");
    $("textarea").attr("disabled", "disabled");
    $("#loading").show();

    $.ajax({
        method: 'POST',
        data: data,
        url: '/admin_handle.php',
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

$("#create-user-form").submit(function (e) {
    //Submit the Create New User form
    //-------------------------------------------
    //console.log($(this).serialize());
    var data = $(this).serialize();
    e.preventDefault();
    var con = 1;
    if ($("#new-role").val() <= 3) {
        con = confirm("Are you sure that you want to create an authoritative user?");
    }

    if (con) {
        reHTML(data);
    }

});

$("#add-med-form, #add-relation-form, #add-med-stock-form, #add-mr-form").submit(function (e) {
    var data = $(this).serialize();
    e.preventDefault();

    reHTML(data);
});

$("#reset-user-pass-form").submit(function (e) {
    var data = $(this).serialize();
    e.preventDefault();

    var con = confirm("Are you sure you want to reset their password?\n" + $("#reset-username").val());

    if (con) {
        reHTML(data);
    }
});

$("#add-relation #role-name").on('change', function () {
    console.log($(this).val());
});


$(function () {
    $("#stock-name").autocomplete({
        source: "/med_search.php",
        minLength: 2,
        select: function (event, ui) {
            $("#stock-med-id").val(ui.item.id);
        }
    });

    $(".mr-meds-input").autocomplete({
        source: "/med_search.php",
        minLength: 3
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<div>" + item.value + "<br>" + item.desc + "</div></li>" )
        .appendTo( ul );
    };

    $("#mr-user").autocomplete({
        source: function (request, response) {
            $.getJSON("/user_search.php", { term: request.term, role: 'user' },
              response);
        },
        minLength: 4,
        select: function (event, ui) {
            $("#mr-user-id").val(ui.item.id);
        }
    });

    $("#mr-dr").autocomplete({
        source: function (request, response) {
            $.getJSON("/user_search.php", { term: request.term, role: 'dr' },
              response);
        },
        minLength: 4,
        select: function (event, ui) {
            $("#mr-dr-id").val(ui.item.id);
        }
    });

    $(".med-time").button();

});

function addMed() {
    var container = document.getElementById("mr-meds");    
    var len = container.getElementsByTagName("input").length;
    len = len / 6;

    //mr-med#
    var med_input = document.createElement("input");
    med_input.type = "text";
    med_input.name = "mr-med" + (len + 1);
    med_input.id = "mr-med" + (len + 1);
    med_input.className = "mr-meds-input";
    med_input.placeholder = "Medicine"  + (len + 1);
    container.appendChild(med_input);

    //mr-med-num#
    //<input type="number" name="mr-med-num1" class="input-small" placeholder="Number" />
    var med_num = document.createElement("input");
    med_num.type = "number";
    med_num.name = "mr-med-num" + (len + 1);
    med_num.className = "input-small";
    med_num.placeholder = "Number";
    container.appendChild(med_num);

    //After food
    //<label for="mr-af1">After Food</label>
    //<input type="checkbox" name="mr-af1" id="mr-af1" class="med-time" />
    var aft_label = document.createElement("label"),
        aft_input = document.createElement("input");
    aft_label.htmlFor = "mr-af" + (len + 1);
    aft_label.innerText = "After Food";
    container.appendChild(aft_label);
    aft_input.type = "checkbox";
    aft_input.name = "mr-af" + (len + 1);
    aft_input.id = "mr-af" + (len + 1);
    aft_input.className = "med-time";
    container.appendChild(aft_input);
    
    //forenoon
    //<label for="mr-fn1">Forenoon</label>
    //<input type="checkbox" class="med-time" name="mr-fn1" id="mr-fn1" /> 
    var fn_label = document.createElement("label"),
        fn_input = document.createElement("input");
    fn_label.htmlFor = "mr-fn" + (len + 1);
    fn_label.innerText = "Forenoon";
    container.appendChild(fn_label);
    fn_input.type = "checkbox";
    fn_input.name = "mr-fn" + (len + 1);
    fn_input.id = "mr-fn" + (len + 1);
    fn_input.className = "med-time";
    container.appendChild(fn_input);

    //afternoon
    //<label for="mr-an1">Afternoon</label>
    //<input type="checkbox" class="med-time" name="mr-an1" id="mr-an1" />
    var an_label = document.createElement("label"),
        an_input = document.createElement("input");
    an_label.htmlFor = "mr-an" + (len + 1);
    an_label.innerText = "Afternoon";
    container.appendChild(an_label);
    an_input.type = "checkbox";
    an_input.name = "mr-an" + (len + 1);
    an_input.id = "mr-an" + (len + 1);
    an_input.className = "med-time";
    container.appendChild(an_input);

    //night
    //<label for="mr-nt1">Night</label>
    //<input type="checkbox" class="med-time" name="mr-nt1" id="mr-nt1" />
    var nt_label = document.createElement("label"),
        nt_input = document.createElement("input");
    nt_label.htmlFor = "mr-nt" + (len + 1);
    nt_label.innerText = "Night";
    container.appendChild(nt_label);
    nt_input.type = "checkbox";
    nt_input.name = "mr-nt" + (len + 1);
    nt_input.id = "mr-nt" + (len + 1);
    nt_input.className = "med-time";
    container.appendChild(nt_input);

    var br = document.createElement("br");
    container.appendChild(br);

    $(".med-time").button();

    $("#mr-med" + (len + 1)).autocomplete({
        source: "/med_search.php",
        minLength: 3
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<div>" + item.value + "<br>" + item.desc + "</div></li>" )
        .appendTo( ul );
    };
}

