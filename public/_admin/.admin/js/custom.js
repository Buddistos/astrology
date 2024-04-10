var field_id, token, datatable;
var $modal = $("#ajax-modal");
var $ajaxReady = $("#ajaxReady");
var $rfModal = $("#rfModal");

/* Перевод для datatables */
var language = {
    "processing": "Подождите...",
    "search": "Поиск:",
    "lengthMenu": "Показать _MENU_ записей",
    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
    "infoEmpty": "Записи с 0 до 0 из 0 записей",
    "infoFiltered": "(отфильтровано из _MAX_ записей)",
    "infoPostFix": "",
    "loadingRecords": "Загрузка записей...",
    "zeroRecords": "Записи отсутствуют.",
    "emptyTable:": "В таблице отсутствуют данные",
    "infoEmpty": "Нечего показывать",
    "emptyTable": "Нет данных для таблицы",
    "zeroRecords": "Записей не найдено",
    "lengthMenu": "<span class='seperator'>|</span>Показывать _MENU_ записей",
    "info": "<span class='seperator'>|</span>Найдено записей: _TOTAL_",
    "metronicGroupActions": "Выбрано записей _TOTAL_:  ",
    "metronicAjaxRequestGeneralError": "Невозможно завершить запрос. Проверьте соединение с интернетом",
    "paginate": {
        "first": "Первая",
        "previous": "Предыдущая",
        "next": "Следующая",
        "last": "Последняя",
        "page": "Страница",
        "pageOf": "из"
    },
    "aria": {
        "sortAscending": ": активировать для сортировки столбца по возрастанию",
        "sortDescending": ": активировать для сортировки столбца по убыванию"
    }
};

var toastOption = {
    "closeButton": true,
    "debug": false,
    "positionClass": "toast-top-right",
    "onclick": null,
    "showDuration": "1000",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

toastr.options = toastOption;

var select2Options = {
    allowClear: true,
    tags: true,
    width: "100%",
    dropdownAutoWidth : true
};
var select2filterOptions = {
    tags: true,
    width: "style",
    dropdownAutoWidth : true
};

function getUrlVars(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function responsive_filemanager_callback(myfield) {
    console.log('RF callback');
    var originUrl = window.location.origin;
    var pattern = new RegExp(originUrl, 'g');
    var url = $("#" + myfield).val().replace(pattern, '');
    $("input." + myfield).val(decodeURI(url));
    if ($modal.is(":visible")) {
        setTimeout("$modal.modal('show')", 50);
    }
}


function iframeLoaded(sel) {
    var iFrameID = document.getElementById(sel);
    if (iFrameID) {
        // here you can make the height, I delete it first, then I make it again
        iFrameID.height = "";
        iFrameID.height = (iFrameID.contentWindow.document.body.scrollHeight + 50) + "px";
    }
}

$modal.on('shown.bs.modal', function () {
    $('input:first', $modal).focus()
})

$(window).resize(function () {
    if ($("#rfModal iframe").is(":visible")) {
        $("#rfModal iframe").height(window.innerHeight - 145);
    }
});

function ajaxAnswer(data, autohide = 5000) {
    var message = $.inArray("message", data) ? data["message"] : '';
    var dataWin = $.inArray("win", data) && data["win"] ? data["win"] : 'success';
    var error = $.inArray("error", data) ? data["error"] : 0;
    var rld = $.inArray("reload", data) ? data["reload"] : 0;
    var rdr = $.inArray("redirect", data) ? data["redirect"] : 0;
    var console_out = $.inArray("console", data) ? data["console"] : 0;

    if (console_out && $(".console_output").is(".portlet-body")) {
        $(".portlet-body.console_output:not(:empty)").html($(".portlet-body.console_output").html() + "<br>" + console_out);
        $(".portlet-body.console_output:empty").html(console_out);
    }

    /* Обновляем страницу, возвращаемся на уровень выше или остаемся */
    if (rld === 1) {
        message += "<br> Обновление через 2 секунды.";
        autohide = 2000;
        reloadPage = function () {
            location.reload();
        }
    } else if (rld === -1) {
        message += "<br> Обновление через 2 секунды.";
        autohide = 2000;
        reloadPage = function () {
            href = location.href.replace(/(\/create|\/[^\/]*\/edit)($|\/(.*)$)/, "");
            location.href = href;
        }
    } else if (rdr) {
        //location.href = rdr;
    } else {
        reloadPage = function () {
            return false;
        }
    }

    /* Закрываем модальное окно, если оно открыто */
    if ($modal.is(":visible")) {
        //$modal.modal("hide");
    }

    /* Готовим срок жизни и тип уведомления: error, warning, success */
    if (error) {
        message = "Ошибка! " + message;
        win = "error";
    } else if (!message) {
        win = "warning";
    } else {
        win = dataWin;
    }
    toastr.options.timeOut = autohide;
    toastr.options.onHidden = function () {
        reloadPage();
    }

    toastr[win](message, "Уведомление");
    return;
    /*
     $(".ajaxAnswer", $ajaxReady).html(msg);
     $ajaxReady.modal();
     */
}


function get_dz(seldz) {
    Dropzone.autoDiscover = false;
    var imagePath = "/upload";
    seldz.dropzone({
        url: "/ajax/dzupload",
        init: function () {
            _this = this;
            id = $(this.element).attr("id");
            json = $("input[name=" + id + "]").val();
            if (json) {
                mocks = JSON.parse(json);
                $.each(mocks, function () {
                    mockFile = this;
                    _this.options.addedfile.call(_this, mockFile);
                    _this.options.thumbnail.call(_this, mockFile, (mockFile.path ? mockFile.path : "/upload") + "/" + mockFile.name);

                    // Create the remove button
                    var removeButton = Dropzone.createElement("<a href='javascript:;' class='removeImage btn red btn-sm btn-block' data-img='" + mockFile.name + "'><i class='fa fa-close'></i></a>");
                    mockFile.previewElement.appendChild(removeButton);
                });
                $(".dz-progress", this.element).remove();
            }
            this.on("addedfile", function (file) {

                // Create the remove button
                var removeButton = Dropzone.createElement("<a href='javascript:;' class='removeImage btn red btn-sm btn-block' data-img='" + file.name + "'><i class='fa fa-close'></i></a>");

                // Capture the Dropzone instance as closure.
                var _this = this;

                // Listen to the click event
                removeButton.addEventListener("click", function (e) {
                    // Make sure the button click doesn't submit the form:
                    e.preventDefault();
                    e.stopPropagation();

                    // Remove the file preview.
                    _this.removeFile(file);
                    // If you want to the delete the file on the server as well,
                    // you can do the AJAX request here.
                });

                // Add the button to the file preview element.
                file.previewElement.appendChild(removeButton);
            });
        },
        uploadMultiple: true,
        paramName: 'dz-files',
        maxFilesize: 16, //Mb
        maxFiles: 14,
        accept: function (file, done) {
            if (file.name.match(/[^0-9a-zA-Zа-яА-Я_\s\-&\.]/)) {
                done("Некорректное имя файла! " + file.name);
            } else {
                done();
            }
        },
        sending: function (file, xhr, formData) {
            form = $(this.element).parents(".portlet form").get(0);
            formData.append('path', imagePath);
            formData.append('_token', token);
            formData.append('dz-field', $(this.element).attr("id"));
            formData.append('dz-watermark', $(this.element).data('watermark'));
            formData.append('dz-module', $(form).data('module'));
            formData.append('dz-module-id', $(form).data('module-id'));
        }
    });
}

var tinyHeight = 200;
var tinyEditor; //Дополнительные кнопки в toolbar. Инициируются на стороне сайта.



function get_edit(sel) {
    if (tinymce == undefined) return;
    tinymce.init({
        selector: sel,
        height: tinyHeight,
        language: 'ru',
        plugins: [
            "advlist autolink lists link image charmap preview hr anchor",
            "searchreplace wordcount visualblocks visualchars fullscreen codemirror",
            "media nonbreaking save table contextmenu directionality",
            "template paste textcolor colorpicker textpattern"
        ],
        fontsize_formats: '6px 7px 8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 22px 24px 26px 28px 30px 34px 38px 42px 46px 52px 58px 64px 72px',
        toolbar1: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image preview media | forecolor backcolor | attribs | fullscreen",
        toolbar2: "styleselect fontsizeselect | hr colbutton " + (typeof(tinyEditor) === "function" ? "siteadded" : ""),
        filemanager_title: "Файловый менеджер",
        relative_urls: false,
        external_filemanager_path: "/.admin/assets/global/plugins/filemanager/",
        external_plugins: {"filemanager": "/.admin/assets/global/plugins/filemanager/plugin.min.js"},
        file_picker_types: 'file image media',
        valid_elements: "*[*]",
        media_strict: false,
        file_picker_callback: function (cb, value, meta) {
            var width = window.innerWidth - 30;
            var height = window.innerHeight - 60;
            if (width > 1800) width = 1800;
            if (height > 1200) height = 1200;
            if (width > 600) {
                var width_reduce = (width - 20) % 138;
                width = width - width_reduce + 10;
            }
            var urltype = 2;
            if (meta.filetype == 'image') {
                urltype = 1;
            }
            if (meta.filetype == 'media') {
                urltype = 3;
            }
            var title = "RESPONSIVE FileManager";
            if (typeof this.settings.filemanager_title !== "undefined" && this.settings.filemanager_title) {
                title = this.settings.filemanager_title;
            }
            var akey = "key";
            if (typeof this.settings.filemanager_access_key !== "undefined" && this.settings.filemanager_access_key) {
                akey = this.settings.filemanager_access_key;
            }
            var sort_by = "";
            if (typeof this.settings.filemanager_sort_by !== "undefined" && this.settings.filemanager_sort_by) {
                sort_by = "&sort_by=" + this.settings.filemanager_sort_by;
            }
            var descending = "false";
            if (typeof this.settings.filemanager_descending !== "undefined" && this.settings.filemanager_descending) {
                descending = this.settings.filemanager_descending;
            }
            var fldr = "";
            if (typeof this.settings.filemanager_subfolder !== "undefined" && this.settings.filemanager_subfolder) {
                fldr = "&fldr=" + this.settings.filemanager_subfolder;
            }
            var crossdomain = "";
            if (typeof this.settings.filemanager_crossdomain !== "undefined" && this.settings.filemanager_crossdomain) {
                crossdomain = "&crossdomain=1";
                if (window.addEventListener) {
                    window.addEventListener('message', filemanager_onMessage, false);
                } else {
                    window.attachEvent('onmessage', filemanager_onMessage);
                }
            }
            tinymce.activeEditor.windowManager.open({
                title: title,
                file: this.settings.external_filemanager_path + 'dialog.php?type=' + urltype + '&descending=' + descending + sort_by + fldr + crossdomain + '&lang=' + this.settings.language + '&akey=' + akey,
                width: width,
                height: height,
                resizable: true,
                maximizable: true,
                inline: 1
            }, {
                setUrl: function (url) {
                    var originUrl = window.location.origin;
                    url = url.replace(originUrl, '');
                    console.log('TinyMCE setUrl ' + url);
                    cb(url);
                }
            });
        },
        codemirror: {
            indentOnInit: true, // Whether or not to indent code on init.
            path: 'CodeMirror'
        },
        plugin_preview_width: '1200',
        image_advtab: true,
        tabfocus_elements: ":prev,:next",
        table_advtab: true,
        table_cell_advtab: true,
        table_row_advtab: true,
        table_default_styles: {
            'width': '100%'
        },
        table_responsive_width: true,
        contextmenu: "link image code | inserttable cell row column deletetable",
        link_class_list: [
            {title: 'Нет', value: ''},
            {title: 'Увеличить фото', value: 'fancybox'}
        ],
        rel_list: [
            {title: 'Нет', value: ''},
            {title: 'Галерея 1', value: 'group1'},
            {title: 'Галерея 2', value: 'group1'},
            {title: 'Галерея 3', value: 'group1'}
        ],
        theme_advanced_resizing: true,
        setup: tinyEditor,
        content_css: '/.admin/assets/global/plugins/bootstrap/css/bootstrap.min.css'
    });
}


/**
 * Ниже скрипты для обработки поля по типу group
 * https://stackoverflow.com/questions/19797370/twitter-typeahead-js-show-all-options-when-click-focus
 */
var ComponentsTypeahead = function () {
    var bloodHound = new Array();
    $('.typeahead').each(function (id, el) {

        bloodHound[id] = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.name);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            identify: function (obj) {
                return obj.name;
            },
            prefetch: {
                url: "/ajax/typeahead?table=" + $(el).data("table") + "&field=" + $(el).data("field"),
                cache: false,
                filter: function (list) {
                    return $.map(list, function (data) {
                        return {name: data};
                    });
                }
            }
        });

        bloodHound[id].initialize();

        $(el).typeahead(
            {
                minLength: 0
            }, {
                displayKey: "name",
                hint: true,
                limit: 100,
                highLight: true,
                source: function (q, sync) {
                    if (q === '') {
                        sync(bloodHound[id].all()); // This is the only change needed to get 'ALL' items as the defaults
                    } else {
                        bloodHound[id].search(q, sync);
                    }
                }
            }
        );
    });
}

function formatState(state) {
    if (!state.id) {
        return state.text;
    }
    var $state = $('<span>' + state.text + '</span>');
    return $state;
};

$(document).ready(function () {

    var Initialization = function (doc) {
        this_ = doc ? $(doc) : "body";
        //Fancybox init
        $(".fancybox").fancybox();

        //Dropzone init
        if ($("div", this_).is(".dropzone")) {
            get_dz($(".dropzone", this_));
        }

        //Tinymce init
        get_edit("textarea.texteditor");

        $("#ajax-modal .make-switch").bootstrapSwitch();

        //Autosizer for textarea
        autosize($('.autosizeme'));

        //Make tooltip for elements .tooltip
        $('[data-toggle="tooltip"]').tooltip({
            placement: "left"
        });

        //Select2 init
        if($("form").is(".ajaxForm_")){
            $(".select2", this_).select2(select2Options);
        }
        if ($("table").is("#datatable_ajax")) {
            $("#datatable_ajax .select2").select2(select2filterOptions);
        }

        $(".select2-no-search", this_).select2($.extend(select2Options, {minimumResultsForSearch: "Infinity"}));
        $(".select2ajax", this_).select2({
            ajax: {
                url: "/ajax/ajaxselect",
                dataType: "json",
                data: function (params) {
                    return {
                        q: params.term, // search term
                        options: $(this).data("options")
                    };
                },
                processResults: function (data, params) {
                    // var $option = $('<option selected>Loading...</option>');
                    // $(".select2ajax option", this_).remove();
                    // $(".select2ajax", this_).append($option).trigger('change'); // append the option and update Select2

                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used

                    return {
                        results: data
                    };
                },
                cache: true
            },
            templateResult: formatState,
            templateSelection: formatState
        });

        if ($("input").is(".typeahead")) {
            ComponentsTypeahead();
        }

        if (jQuery().sortable) {
            $(".dropzone").sortable({
                items: ".dz-preview",
                opacity: 0.8,
                revert: 250, // animation in milliseconds
                update: function (b, c) {
                    var imgList = new Array();
                    $(".dz-preview", this).each(function (id, el) {
                        img = $(".dz-filename", this).text();
                        imgList[id] = img;
                    })
                    $.ajax({
                        url: "/ajax/dzsort",
                        data: "_token=" + token + "&table=" + $(this).data("table") + "&module_id=" + $(this).data("module-id") + "&field=" + $(this).attr("id") + "&images=" + JSON.stringify(imgList),

                        type: 'POST',
                        success: function (data) {
                            console.log(data);
                        }
                    })
                }
            }).disableSelection();
        }
    }
    var placeholder = 'Выберите из списка';

    //Get token
    token = token ? token : $("input[name=_token]").val();

    Initialization();
    /**
     * Обработка выбора картинок через файловый менеджер
     */
    $("body").on("click", ".filemanager", function () {
        field_id = $(this).attr("id");
        $rfModal.modal('show');
    }).on('show.bs.modal', "#rfModal", function (e) {
        src = "/.admin/assets/global/plugins/filemanager/dialog.php?type=2&relative_url=0&lang=ru&field_id=" + field_id;
        $("#rfModal iframe").attr("src", src).height(window.innerHeight - 145);
    });
    //Конец обработки выбора картинок

    $("body").on("click", ".removeImage", function (e) {
        form = $(this).parents(".portlet form").get(0);
        field = $(this).parents(".dropzone").get(0);
        preview = $(this).parent('.dz-preview');
        file = $(this).data("img");
        // Make sure the button click doesn't submit the form:
        // Remove the file preview.
        // If you want to the delete the file on the server as well,
        // you can do the AJAX request here.
        $(this).remove();
        $.ajax({
            url: "/ajax/dzremove",
            type: "POST",
            data: "_token=" + token + "&img=" + file + "&dz-module=" + $(form).data('module') + "&dz-module-id=" + $(form).data('module-id') + "&dz-field=" + $(field).attr("id"),
            success: function () {
                preview.remove();
            }
        });
    }).on('submit', "form.ajaxForm", function (event) {
        //Отправка формы через Ajax
        event.stopPropagation();
        event.preventDefault();
        tinyMCE.triggerSave();
        form = this;
        var data = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: $(form).attr('method'),
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data, textStatus, jqXHR) {
                ajaxAnswer(data);
                //setTimeout("$('.modal-button', $ajaxReady).click()", autohide);
            },
            error: function (data) {
                ajaxAnswer("Ошибка: " + data);
            }
        })
        return false;
    }).on("click", ".optionSetting", function () {
        //Вставка Опций для примера при создании нового подя для Модуля
        view = '{\n  "list": {\n    "0": "Нет",\n    "1": "Да"\n  }\n}';
        ta = $("textarea[name=options]");
        ta.val(view);
        autosize.update(ta);
    }).on("click", ".optionTemplate", function () {
        //Вставка Опций для примера при создании нового подя для Модуля
        view = '{\n  "default": "Выберите это значение, если в настройках модуля указана родительская таблица",\n  "list": {\n    "0": "Нет",\n    "1": "Да"\n  },\n  "table": {\n    "name":"Имя_таблицы",\n    "value":"Поле_для_значений",\n    "text":"Поле_для_отображения",\n    "depends":"Ведущее_поле"\n  },\n  "hidden":false\n}';
        ta = $("textarea[name=options]");
        ta.val(view);
        autosize.update(ta);
    }).on("click", ".optionFilters", function () {
        //Вставка Опций для отображения
        $.ajax({
            url: '/ajax/getfilterviews',
            success: function (data) {
                view = JSON.stringify(data).replace(/({|,)/g, "$1\n  ").replace(/(})/g, "\n  $1");
                ta = $("textarea[name=options]");
                ta.val(view);
            }
        });
        view = '{\n  "default": "Выберите это значение, если в настройках модуля указана родительская таблица",\n  "list": {\n    "0": "Нет",\n    "1": "Да"\n  },\n  "table": {\n    "name": "Имя_таблицы",\n    "value": "Поле_для_значений",\n    "text": "Поле_для_отображения",\n    "depends": "Ведущее_поле"\n  },\n  "hidden": false\n}';
        ta = $("textarea[name=options]");
        ta.val(view);
        autosize.update(ta);
    }).on("confirmed.bs.confirmation", ".delete-action", function () {
        console.log("Q");
        if($(this).parents('tr').length > 0){
            parent_tr = $($(this).parents('tr').get(0));
        }else{
            parent_tr = $($(this).parents('li.dd3-item').get(0));
        }
        parent_tr.addClass("justDeleted");
        $.ajax({
            url: $(this).data("url"),
            data: "_method=DELETE&_token=" + token,
            type: "POST",
            success: function (data) {
                ajaxAnswer(data);
                parent_tr.slideUp('slow', function () {
                    $(this).remove();
                });
            }
        });
    }).on("canceled.bs.confirmation", ".delete-action", function () {
        parent_tr = $($(this).parents('tr').get(0));
        parent_tr.css("background-color", "transparent");
    });

    $("div#dataTree").each(function () {
        console.log(this);
        var myList;
        //Древовидная таблица
        $(this).nestable()
            .on('change', function () {
                /* on change event */
                myList = $(this).nestable('serialize');
                $("input[name=json-tree]").val(JSON.stringify(myList));
            });
        $('.dd').nestable('collapseAll');
        myList = $(this).nestable('serialize');
        $("input[name=json-tree]").val(JSON.stringify(myList));
    });

    if ($("table").is(".dataList")) {
        $('.dataList').DataTable({
            bStateSave: true,
            rowReorder: true,
            snapX: true,
            scrollX: false,
            language: language,
            pageLength: 25,
            selector: "td:nth-child(2)",
            order: [0, 'asc'],
            columnDefs: [
                {orderable: true, className: $("table").is("#mainTable") ? "reorder" : "", targets: 0, visible: $("table").is("#mainTable") ? true : false},
                {orderable: false, className: $("table").is("#mainTable") ? "" : "reorder", targets: 1},
                {orderable: false, targets: "nosort"},
                {
                    "targets": -1,
                    "width": 80,
                    "class": "text-center",
                    "orderable": false
                }
            ],
        }).on("row-reorder", function (e, diff, edit) {
            model = $(this).data("model");
            for (var i = 0, ien = diff.length; i < ien; i++) {
                $(diff[i].node).addClass("reordered").data("order", diff[i].newData);
            }
            var ids = [];
            $("tbody tr", this).each(function () {
                ids.push({'id': $(this).data('id'), 'order': $(this).data('order')});
            });
            json = JSON.stringify(ids);
            $.ajax({
                url: "/ajax/reorder",
                data: "_token=" + token + "&model=" + model + "&rows=" + json,
                type: "POST",
                success: function (data) {
                    ajaxAnswer(data);
                }
            });
        }).draw();
        $(".table.dataList").show();
    }


    $("body").on("click", ".ajax-edit", function () {
        // create the backdrop and wait for next modal to be triggered
        $("body").modalmanager("loading");
        var el = $(this);
        $modal.load(el.attr("data-url"), "", function () {
            $modal.modal();
            Initialization($modal);
        });
    });

    $("body").on("click", ".ajax-send", function () {
        type = $(this).data("method");
        url = $(this).data("url");
        $.ajax({
            url: url,
            data: "_token=" + token,
            type: type,
            success: function (data) {
                ajaxAnswer(data);
            }
        })
    });
    /*
        $("a[href^='#']").click(function(){
            location.href = location.href.replace(/#(.)+$/, "") + $(this).attr("href");
        });
        var hash = location.href.replace(/^(.)+#/, "#");
        $("a[href='" + hash + "']").click();
    */


    $("body").on("change", ".dataList .group-checkable", function () {
        var table = $(this).parents("table.dataList").get(0);
        var set = $(".checkboxes", table);
        var checked = jQuery(this).is(":checked");
        set.each(function () {
            if (checked) {
                $(this).prop("checked", true);
                $(this).parents('tr').addClass("active");
            } else {
                $(this).prop("checked", false);
                $(this).parents('tr').removeClass("active");
            }
        });
    }).on('change', '.dataList .checkboxes', function () {
        $(this).parents('tr').toggleClass("active");
    });
});

