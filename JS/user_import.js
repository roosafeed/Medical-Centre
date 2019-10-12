var sheetError = 0;     //Number of rows with missing required data
var sheetOBJ;

window.onload = function () {
    //page has loaded
    if(typeof window.XLSX != "undefined")
    {
        $("#import_user").show();
    }    
    else
    {
        $("#sheet-error-msg").show();
        $("#sheet-error-msg").text("Contents on this page did not load fully. Please check the connection and try again.");
        $("#import_footer").show();
    }

    //Handle the uploaded file
    function fileHandle(e) {
        var files = e.target.files,
            f = files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#loading").show();
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, { type: 'array', cellDates: true, cellNF: false, cellText: false });
            var sheet1 = workbook.SheetNames[0];

            /* Convert the first workbook to JS objects*/
            sheetOBJ = XLSX.utils.sheet_to_json(workbook.Sheets[sheet1], { header: 2, dateNF: "DD-MM-YYYY", raw: false });
            console.log(JSON.stringify(sheetOBJ));
            //console.log(sheetOBJ);
            //Clear the contents of the result table
            $("div#import_result table tbody").html("");
            //show the contents of the sheet
            $("div#import_result").show();

            populateTable();
        };
        reader.readAsArrayBuffer(f);
    }

    document.getElementById("xlfile").addEventListener("change", fileHandle, false);
}

function populateTable() { 
    //fill the result table with the sheet object
    var i = 0;
    sheetError = 0;
    
    for (i = 0; i < sheetOBJ.length; i++) {
        
        var rowError = true;
        sheetError++;
        
        if(sheetOBJ[i]["FNAME"] && sheetOBJ[i]["ID"] && sheetOBJ[i]["EMAIL"] && sheetOBJ[i]["TYPE"])
        {
            rowError = false;
            sheetError--;
        }
        var output = "";
        output += "<tr" + ((rowError) ? ' class="rerror">' : (' id="' + sheetOBJ[i]["ID"] + '" >'));

        output += '<td data-key="type" data-index-number="' + i + '"' + ((sheetOBJ[i]["TYPE"]) ? '>' : ' class="error">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["TYPE"] || "NULL") + '</p></td>';

        output += '<td data-key="fname" data-index-number="' + i + '"' + ((sheetOBJ[i]["FNAME"]) ? '>' : ' class="error">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["FNAME"] || "NULL") + '</p></td>';

        output += '<td data-key="lname" data-index-number="' + i + '"' + ((sheetOBJ[i]["LNAME"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["LNAME"] || "NULL") + '</p></td>';

        output += '<td data-key="email" data-index-number="' + i + '"' + ((sheetOBJ[i]["EMAIL"]) ? '>' : ' class="error">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["EMAIL"] || "NULL") + '</p></td>';

        output += '<td data-key="id" data-index-number="' + i + '"' + ((sheetOBJ[i]["ID"]) ? '>' : ' class="error">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["ID"] || "NULL") + '</p></td>';

        output += '<td data-key="res" data-index-number="' + i + '"' + ((sheetOBJ[i]["RES"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["RES"] || "NULL") + '</p></td>';

        output += '<td data-key="room" data-index-number="' + i + '"' + ((sheetOBJ[i]["ROOM"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["ROOM"] || "NULL") + '</p></td>';

        output += '<td data-key="mob1" data-index-number="' + i + '"' + ((sheetOBJ[i]["MOB1"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["MOB1"] || "NULL") + '</p></td>';

        output += '<td data-key="mob2" data-index-number="' + i + '"' + ((sheetOBJ[i]["MOB2"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["MOB2"] || "NULL") + '</p></td>';

        output += '<td data-key="dob" data-index-number="' + i + '"' + ((sheetOBJ[i]["DOB"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["DOB"] || "NULL") + '</p></td>';

        output += '<td data-key="gender" data-index-number="' + i + '"' + ((sheetOBJ[i]["GENDER"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["GENDER"] || "NULL") + '</p></td>';

        output += '<td data-key="addr" data-index-number="' + i + '"' + ((sheetOBJ[i]["ADDR"]) ? '>' : ' class="error1">');
        output += '<p contenteditable="false">' + (sheetOBJ[i]["ADDR"] || "NULL") + '</p></td>';

        output += "</tr>";

        $("div#import_result table tbody").append(output);
    }

    $("#loading").hide();

    if (sheetError > 0) {
        $("#sheet-error-msg").show();
        $("#sheet-error-msg").text("There are missing required information in " + sheetError + " row(s).");
        $("#import_footer").show();
        $("#import_submit").hide();
        document.getElementById("import_submit").removeEventListener("click", importSubmit);
    }
    else {
        $("#import_footer, #import_submit").show();
        $("#sheet-error-msg").text("");
        $("#sheet-error-msg").hide();
        document.getElementById("import_submit").addEventListener("click", importSubmit);
    }
}

function importSubmit() {
    $("#loading").show();
    $("#import_submit").attr("disabled", true);
    $("#import_submit").val("Processing...");

    $.ajax({
        url: '/user_import_handle.php',
        method: 'POST',
        data: { "users": JSON.stringify(sheetOBJ) }
    }).done(function (data, status, xhr) {
        //alert(data);
        //modalShow(data);
        console.log(JSON.parse(data));
        var resultOBJ = JSON.parse(data);
        //add class 'exist' to all the users that already exist
        var i = 0;
        for (i = 0; i < resultOBJ["exist"].length; i++) {
            $("#" + resultOBJ["exist"][i]).addClass("exist");
        }
        //add class 'success' to all the users that are successfully added
        for (i = 0; i < resultOBJ["success"].length; i++) {
            $("#" + resultOBJ["success"][i]).addClass("success");
        }
        //add class 'fail' to all the users that have not been added
        for (i = 0; i < resultOBJ["fail"].length; i++) {
            $("#" + resultOBJ["fail"][i]).addClass("fail");
        }
        var resultMsg = resultOBJ["success"].length + " new users are added.<br />";
        resultMsg += resultOBJ["exist"].length + " users already exists and not modified.<br />";
        resultMsg += resultOBJ["fail"].length + " failed.";
        if (resultOBJ["fail"].length > 0) {
            resultMsg += "If there are errors correct them or else try again.";
        }

        modalShow(resultMsg);
    }).always(function () {
        $("#loading").hide();
        $("#import_submit").removeAttr("disabled");
        $("#import_submit").val("Submit");
    });
}