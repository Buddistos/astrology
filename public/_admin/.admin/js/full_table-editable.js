function restoreRow(oTable, nRow) {
    var aData = oTable.fnGetData(nRow);
    var jqTds = $('>td', nRow);

    for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
        oTable.fnUpdate(aData[i], nRow, i, false);
    }

    oTable.fnDraw();
}

function editRow(oTable, nRow) {
    var aData = oTable.fnGetData(nRow);
    console.log(aData);
    var jqTds = $('>td', nRow);
    for (var i = 1, iLen = jqTds.length; i < iLen - 2; i++) {
        jqTds[i].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[i] + '" style="width: 100%!important;">';
    }

    // jqTds[0].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[0] + '">';
    // jqTds[1].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[1] + '">';
    // jqTds[2].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[2] + '">';
    // jqTds[3].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[3] + '">';
    jqTds[i++].innerHTML = '<a class="edit" href="">Save</a>';
    jqTds[i++].innerHTML = '<a class="cancel" href="">Cancel</a>';
}

function saveRow(oTable, nRow) {
    var jqInputs = $('input', nRow);
    oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
    oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
    oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
    oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
    oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 4, false);
    oTable.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 5, false);
    oTable.fnDraw();
}

function cancelEditRow(oTable, nRow) {
    var jqInputs = $('input', nRow);
    oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
    oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
    oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
    oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
    oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 4, false);
    oTable.fnDraw();
}

function restoreCell(tdCell) {
    var oldData = $(tdCell).data('oldData');
    $(tdCell).html(oldData);
    $(tdCell).removeData('oldData');
    oldData = null;
    oldField = null;
    oldText = null;
    var tr = $(this).parents('tr')[0];
    if ($(".editme", tr).is(":visible")) {
        $("span.edit", tdCell).removeClass("pseudolink");
    }
}
var cell;
function oneValueChange(data, is_select){
    var text;

    var val = $(data).val();
    if(is_select){
        text = $(data).select2('data')[0].text;
    }else{
        text = val;
    }
    console.log(oldText, text);
    if(oldText == text){
        data["message"] = "Оставлено без изменений";
        data["win"] = "warning";
        ajaxAnswer(data);
        return false;
    }

//    var td = $(data).parents('td')[0];
    restoreCell(cell);
    //var tr = $(cell).parents('tr')[0];
    var id = $(cell).data("id");
    var field = $(cell).data("field");
    var model = $("#full_table").data("model");

    // console.log(data, val, text, tr, id, field, model);

    if(text == ""){
        $(cell).html("<i class='fa fa-edit'></i>");
    }else{
        $(cell).text(text);
    }

    if($(cell).data("type") == "switcher"){
        if(val == 1){
            $(cell).addClass("label-success");
            $(cell).removeClass("label-warning");
        }else{
            $(cell).addClass("label-warning");
            $(cell).removeClass("label-success");
        }
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/ajax/onevaluechange",
        type: "POST",
        data: "model=" + model + "&field=" + field + "&id=" + id + "&value=" + encodeURIComponent(val),
        success: function (data) {
            ajaxAnswer(data);
        }
    });

}

var nEditing = null;
var nNew = false;
var oTable;
var oldText = null;
var oldField = null;

$(document).ready(function () {

    var table = $('#full_table');
    oTable = datatable;

// table-edit
    table.on('mouseenter', 'tbody > tr', function () {
        $('td span.edit', this).addClass("pseudolink");
        var edit_url =  $("a.edit-action", this).attr("href");
//        $('td:first-child', this).prepend('<div class="editme"><a href="' + edit_url + '" data-id="' + $(this).data("id") + '"><i class="fa fa-edit"></i></a></div>');
    });
    table.on('mouseleave', 'tbody > tr', function () {
        $('td span.edit', this).removeClass("pseudolink");
//        $(".editme", this).remove();
    });


    table.on('click', 'td span.edit', function (e) {
        e.stopPropagation();
        e.preventDefault();
        if($("input", this).is(".edit-field")){
            return;
        }

        /**
         * Возвращаем данные предыдущей ячейки
         */
        if (oldField != null) {
            restoreCell(oldField);
        }

        /**
         * Запоминаем данные текущей ячейки
         */
/*
        var td = $(this).parents('td')[0];
        oldField = td;
        oldText = $('span.edit', td).text();
*/

        cell = this;
        oldField = cell;
        oldText = $(cell).text();

        $(cell).data('oldData', $(cell).html());

        edit_type = $(cell).data("type");
        edit_field = $(cell).data("field");

        var types = new Array('select', 'ajaxselect', 'switcher');
        var is_select = 0;


        if ($.inArray(edit_type, types) >= 0) {
            newselect = $("select[data-name='" + edit_field + "']");
            newselect.remove("option:first-child")
            $("option:first-child", newselect).text("Отмена");
            htmltext = '<select class="select2 select-edit-table edit-field" name="' + edit_field + '"></select>';
            console.log(newselect.html());
            is_select = 1;
        } else {
            htmltext = '<input type="text" class="form-control input-small input-edit-table edit-field" value="' + $(this).text() + '" style="width: 100%!important;">';
        }
        $(cell).html(htmltext);

        if(edit_type == "phone"){
            $(".input-edit-table").inputmask("+79999999999", {
                //autoUnmask: true
            });
        }else if(edit_type == "_finance"){
            $(".input-edit-table").autoNumeric("init", {
                digitGroupSeparator: " ",
                digitalGroupSpacing: 3,
                formatOnPageLoad: false
            });
        }else if(edit_type == "email"){
            $(".input-edit-table").inputmask("email", {
                mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
                greedy: false,
                onBeforePaste: function (pastedValue, opts) {
                    pastedValue = pastedValue.toLowerCase();
                    return pastedValue.replace("mailto:", "");
                },
                definitions: {
                    '*': {
                        validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
                        casing: "lower"
                    }
                }
            });
        }

        if (is_select) {
            //$("select.select2[name='" + edit_field + "'] option:first-child").text("Отмена");
            $("select.select2[name='" + edit_field + "']").html(newselect.html()).select2();
            $("select.select2[name='" + edit_field + "']")
                .select2(select2filterOptions)
                .select2('open')
                .on('change', function (e) {
                    oneValueChange(this, 1);
                    return false;
                })
                .on('select2:close', function () {
                    var td = $(this).parents('td')[0];
                    restoreCell(cell);
                });
            console.log($("select.select2[name='" + edit_field + "']"));
        } else {
            $("input.edit-field")
                .on("blur focusout", function () {
                    oneValueChange(this, 0);
                    restoreCell(cell);
                    return false;
                }).keyup(function (e) {
                    if (e.which == 13) { //ENTER
                        oneValueChange(this, 0);
                        var td = $(this).parents('td')[0];
                        restoreCell(cell);
                    } else if (e.which == 27) { //ESC
                        var td = $(this).parents('td')[0];
                        restoreCell(cell);
                    }
                });
        }

        $(".edit-field").focus();

    });

    table.on('click', '.editme a', function (e) {

    });


    table.on('click', '.cancel', function (e) {
        e.preventDefault();
        if (nNew) {
            oTable.fnDeleteRow(nEditing);
            nEditing = null;
            nNew = false;
        } else {
            restoreRow(oTable, nEditing);
            nEditing = null;
        }
        return false;
    });

});
