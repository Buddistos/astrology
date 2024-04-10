var grid;

grid = new Datatable();

grid.init({
    src: $("#datatable_ajax"),
    onSuccess: function (grid, response) {
        if ($.inArray("result_fields", response)) {
            var rf = JSON.parse(response.result_fields);
            $.each(rf, function (i, data) {
                $.each(data, function (nam, res) {
                    console.log(res);
                    $("#datatable_ajax input[name='" + nam + "']").val(res).css({'min-width': '80px', 'display': 'inline-block'});
                    ;
                });
            });
        }

        $(".select2").trigger('change.select2');

        // grid:        grid object
        // response:    json object of server side ajax response
        // execute some code after table records loaded
    },
    onError: function (grid) {
        // execute some code on network or other general error
    },
    onDataLoad: function (grid) {
        // execute some code on ajax data load
        $('#datatable_ajax [data-toggle=confirmation]').confirmation();

        // $("span.edit[data-type='phone']").inputmask("+79999999999", {
        //     //autoUnmask: true
        // });
    },
    loadingMessage: 'Загрузка...',
    dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

        // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
        // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
        // So when dropdowns used the scrollable div should be removed.
        //"dom": "<'row '<'col-md-8 col-sm-12'pli><'table-scrollable't><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r><'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>><br>",
                "dom": "<'row'<'col-md-6 col-sm-12'pli><'col-md-6 col-sm-12'<'table-group-actions pull-right'>>r><'table-scrollable dragscroll't><'row'<'col-md-8 col-sm-12 margin-bottom-10'pli>>", // datatable layout

        // save datatable state(pagination, sort, etc) in cookie.
        "bStateSave": true,

        // save custom filters to the state
        "fnStateSaveParams": function (oSettings, sValue) {
            $("#datatable_ajax tr.filter .form-control").each(function () {
                sValue[$(this).attr('name')] = $(this).val();
            });

            return sValue;
        },

        // read the custom filters from saved state and populate the filter inputs
        "fnStateLoadParams": function (oSettings, oData) {
            //Load custom filters
            $("#datatable_ajax tr.filter .form-control").each(function () {
                var element = $(this);
                if (oData[element.attr('name')]) {
                    element.val(oData[element.attr('name')]);
                }
            });

            return true;
        },
        "lengthMenu": [
            [10, 20, 50, 100, 150, -1],
            [10, 20, 50, 100, 150, "All"] // change per page values here
        ],
        "pageLength": 10, // default record count per page
        "searchDelay": 350,
        "ajax": {
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (data) { // add request parameters before submit
                data["model"] = $("#datatable_ajax").data("model");
                data["params"] = {};
                if ($("#datatable_ajax").data("parent_id")) ajaxParams["parent_id"] = $("#datatable_ajax").data("parent_id");
                if ($("#datatable_ajax").data("table_id")) ajaxParams["table_id"] = $("#datatable_ajax").data("table_id");
                $.each(ajaxParams, function (key, value) {
                    data["params"][key] = value;
                });
            },
            type: "POST",
            url: "/ajax/dataajax"
        },
        "ordering": true,
        "order": [
            [1, "asc"]
        ], // set first column as a default sort by asc
        'createdRow': function (row, data, dataIndex) {
            $(row).attr('data-id', data[0]).addClass("vs_" + $("table#datatable_ajax").data("model"));
        }
    }
});

grid.submitFilter();

// handle group actionsubmit button click
grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {

    var action = $(".table-group-action-input", grid.getTableWrapper());
    if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
        grid.setAjaxParam("customActionType", "group_action");
        grid.setAjaxParam("customActionName", action.val());
        grid.setAjaxParam("id", grid.getSelectedRows());
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
    } else if (action.val() == "") {
        App.alert({
            type: 'danger',
            icon: 'warning',
            message: 'Please select an action',
            container: grid.getTableWrapper(),
            place: 'prepend'
        });
    } else if (grid.getSelectedRowsCount() === 0) {
        App.alert({
            type: 'danger',
            icon: 'warning',
            message: 'No record selected',
            container: grid.getTableWrapper(),
            place: 'prepend'
        });
    }
});

//grid.setAjaxParam("customActionType", "group_action");
//grid.getDataTable().ajax.reload();
//grid.clearAjaxParams();
//grid.addAjaxParam("model", $("#datatable_ajax").data("model"));


$(document).ready(function () {

    if ($("#datatable_ajax").data("table_id") && $("#datatable_ajax").data("parent_id")) {
        $("select[name='table_id']").prop("disabled", true).select2(select2filterOptions);
        $("select[name='parent_id']").prop("disabled", true).select2(select2filterOptions);
    } else if ($(".form-filter.select2").is("[name='parent_id']") && $(".form-filter.select2[name='table_id']").is(":visible")) {

        var tid = $("select[name='table_id']:visible").val();
        if (tid) {
            $("select[name='parent_id'] option[data-depender!='" + tid + "']").prop("disabled", true);
            $("select[name='parent_id'] option:first-child").prop("disabled", false);
            $("select[name='parent_id']").trigger("change:select2");
        }


        $("#datatable_ajax").on("change", "select[name='parent_id']", function (event) {

            var pid = $("option:selected", this).data("depender");
            if ($("select[name='table_id']").val() == pid) {
                return;
            }

            $("select[name='table_id']").val(pid);

            if (pid) {
                $("select[name='parent_id'] option[data-depender!='" + pid + "']").prop("disabled", true);
                $("select[name='parent_id'] option:first-child").prop("disabled", false);
            } else {
                $("select[name='parent_id'] option").prop("disabled", false);
            }
            $("select[name='parent_id']").select2(select2filterOptions);
            $("select[name='table_id']").select2(select2filterOptions);

            grid.setAjaxParam('table_id', pid);
            grid.getDataTable().ajax.reload();

        }).on("change", "select[name='table_id']", function (event) {

            tid = $(this).val();

            reload = 0;
            if (tid) {
                $("select[name='parent_id'] option[data-depender!='" + tid + "']").prop("disabled", true);
                $("select[name='parent_id'] option[data-depender='" + tid + "']").prop("disabled", false);
                $("select[name='parent_id'] option:first-child").prop("disabled", false);
            } else {
                $("select[name='parent_id']").val("");
                $("select[name='parent_id'] option").prop("disabled", false);
                grid.setAjaxParam('parent_id', "");
                reload = 1;
            }

            if ($("select[name='parent_id'] option[data-depender='" + tid + "']").is(":not(:selected)")) {
                $("select[name='parent_id'] option:selected").prop("selected", false);
                $("select[name='parent_id']").val("");
                grid.setAjaxParam('parent_id', "");
                reload = 1;
            }
            $("select[name='parent_id']").select2(select2filterOptions);

            if (reload) {
                grid.getDataTable().ajax.reload();
            }
        });
    }
});
