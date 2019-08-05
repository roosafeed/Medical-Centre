var pos_med_num = 0;

function addMed_POS() {
    //creates extra fields for more medicines to be added in the #pos-meds
    var container = document.getElementById("pos-meds");  
    //var len = container.getElementsByTagName("input").length;
    //len = len / 4;
    pos_med_num += 1;


    /*
    <div id="pos-med-1" class="pos-med-class">           
        <input name="pos-med-batch-1" type="text" id="pos-med-batch-1" placeholder="Medicine Batch" />
        <input name="pos-batch-id-1" type="hidden" id="pos-batch-id-1" value="" />
        <input name="pos-med-num-1" type="number" id="pos-med-num-1" placeholder="Quantity" /><br />
        <input type="button" id="del-med-1" value="Remove" onclick="delMed_POS()"><br />
    </div>
    */

    //create all the elements required

    //create the sub container
    var sub = document.createElement("div");
    sub.id = "pos-med-" + pos_med_num;
    sub.className = "pos-med-class";
    
    //br
    var br = document.createElement("br");

    //pos-med-batch-#
    var med_input = document.createElement("input");
    med_input.type = "text";
    med_input.name = "pos-med-batch-" + pos_med_num;
    med_input.id = "pos-med-batch-" + pos_med_num;
    //med_input.className = "pos-meds-input";
    med_input.placeholder = "Medicine Batch "  + pos_med_num;
    sub.appendChild(med_input);

    //pos-batch-id-#
    var med_id = document.createElement("input");
    med_id.type = "hidden";
    med_id.name = "pos-batch-id-" + pos_med_num;
    med_id.id = "pos-batch-id-" + pos_med_num;
    med_id.value = "";
    sub.appendChild(med_id);

    //pos-med-num-#
    var med_num = document.createElement("input");
    med_num.type = "number";
    med_num.name = "pos-med-num-" + pos_med_num;
    med_num.id = "pos-med-num-" + pos_med_num;
    //med_num.value = 0;
    //med_num.className = "pos-meds-input";
    med_num.placeholder = "Quantity";
    sub.appendChild(med_num);

    sub.appendChild(br);

    //del-med-#
    var med_del = document.createElement("input");
    med_del.type = "button";
    med_del.value = "Remove";
    med_del.id = "del-med-" + pos_med_num;
    sub.appendChild(med_del);
 
    
    //sub.appendChild(br);
    container.appendChild(sub);

    //add event listener for 'Remove' button
    $("#del-med-" + pos_med_num).click(function () {
        var del_id = this.id.replace("del-med-", "#pos-med-");
        $(del_id).remove();
    });


    //autocomplete medicines

    $("#pos-med-batch-" + pos_med_num).autocomplete({
        source: function (request, response) {
            $.getJSON("/med_search.php", { term: request.term, type: 'stock-med' },
              response);
        },
        minLength: 3,
        select: function (event, ui) {
            var batch_id = this.id.replace("pos-med-batch-", "#pos-batch-id-");
            $(batch_id).val(ui.item.bid);
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li>")
        .append("<div>" + item.value + "<br>" + item.desc + "</div></li>")
        .appendTo(ul);
    };
    

    
}

//Autocomplete Rollnumber/ Employee id
//Medicines can be despatched only to existing users
$("#pos-roll").autocomplete({
    source: function (request, response) {
        $.getJSON("/user_search.php", { term: request.term, role: 'user' },
            response);
    },
    select: function (event, ui) {
        $("#pos-user-id").val(ui.item.id);
    },
    minLength: 3
});

//Handle the form submission
$("#form-pos").submit(function (e) {
    var data = $(this).serialize();
    e.preventDefault();

    //alert(data);
    data = "pos-user-id=" + $("#pos-user-id").val();

    var i = 0;
    var med_list = $("#pos-meds").children();
    while (i < med_list.length) {
        var id = med_list[i].id.replace("pos-med-", "");
        var j = 0;
        //search for the medicine id and quantity in the list of children of the medicine list
        while (med_list[i].children[j].id != ("pos-batch-id-" + id) || j < med_list[i].children[j].length) {
            j++;
        }
        data += "&med-id-" + i + "=" + med_list[i].children[j].value;

        j = 0;
        while (med_list[i].children[j].id != ("pos-med-num-" + id) || j < med_list[i].children[j].length) {
            j++;
        }
        data += "&med-num-" + i + "=" + med_list[i].children[j].value;

        i++;
    }

    reHTML(data, '/admin_handle.php');
});

document.getElementById("form-pos").onreset = function () {
    $(".pos-med-class").remove();
    pos_med_num = 0;
}

