var bossAdmAjaxUrl = 'http://' + location.hostname + '/administrator/ajax.index.php?option=com_boss&';

$(document).ready(function () {
    //новое поле           
    $(".toolbox").click(function () {
        var into = $('#form_builder_properties');
        var type = $(this).attr('id');
        var directory = $('#directory').val();
        var change_type_fieldid = $('#change_type_fieldid').val();
        var e = this;
        $(this).addClass('loading');
        $.get(bossAdmAjaxUrl + 'directory=' + directory + '&act=builder&task=new&plugin=' + type + '&fieldid=' + change_type_fieldid,
            function (result) {
                $(e).removeClass('loading');
                $(into).html(result);
                delete result;
                $('#change_type_fieldid').val('');
            });
    });

    //сортировка
    $("#form_builder_panel ol").sortable({
        cursor:'ns-resize',
        axis:'y',
        handle:'.handle',
        start:function (e, ui) {
            $('.wysiwyg').each(function () {
                var name = $(this).attr('name');
            });
        },
        stop:function (e, ui) {

        }
    });
});

//редактировать поле           
function bossEditField(fieldid) {
    var into = $('#form_builder_properties');
    var directory = $('#directory').val();
    var e = this;
    $(this).addClass('loading');
    $.get(bossAdmAjaxUrl + 'directory=' + directory + '&act=builder&task=edit&fieldid=' + fieldid, function (result) {
        $(e).removeClass('loading');
        $(into).html(result);
        delete result;
    });
}

function bossSaveField(directory) {

    var qString = $("#fieldForm").formSerialize();
    var field_action = $("#field_action").val();
    var url = bossAdmAjaxUrl + 'act=builder&task=savefield&field_action=' + field_action;

    $.ajax({
        type:"POST",
        data:qString,
        url:url,
        dataType:'HTML',
        success:function (fieldid) {

            $.ajax({
                type:"POST",
                url:bossAdmAjaxUrl + 'act=builder&task=showfield&directory=' + directory + '&fieldid=' + fieldid,
                dataType:'HTML',
                success:function (html) {
                    //если это новое поле, то добавляем в конец списка
                    if (field_action == 'new') {
                        $("#form_builder_panel ol").append(html);
                    }
                    //если редактируемое поле, то обновляем его
                    else {
                        $('#' + fieldid + '_li').slideUp('slow', function () {
                            $(this).replaceWith(html);
                            $(this).slideDown('slow')
                        });
                    }
                    $('#form_builder_properties').slideUp('slow', function () {
                        $(this).html('Поле сохранено');
                        $(this).slideDown('slow')
                    });
                }
            });

        }
    });
    return;
}

function bossSaveFieldOrder(directory) {
    var qString = $("#fieldList").formSerialize();

    $('#saveFieldOrderButton').addClass('loading');
    $("#form_builder_properties").html('Минуточку!');
    $.ajax({
        type:"POST",
        data:qString,
        url:bossAdmAjaxUrl + 'act=builder&task=savefieldorder&directory=' + directory,
        dataType:'HTML',
        success:function (data) {
            $('#saveFieldOrderButton').removeClass('loading');
            if (data) {
                $("#form_builder_properties").html('Порядок полей сохранен');
            }
            else {
                $("#form_builder_properties").html('Ошибка сохранения порядка полей');
            }
        }
    });
    return;
}

//изменить тип поля        
function change_type(fieldid) {
    $('#change_type_fieldid').val(fieldid);
    $("#field_action").val('change_type');
    $('#form_builder_properties').html('Теперь выберите новый тип для поля из списка ниже.');
    $('#form_builder_toolbox_header').html('Выберите новый тип для поля');
}
;

//изменить привязку полей
function change_template(fieldid) {
    var directory = $('#directory').val();
    var url = bossAdmAjaxUrl + 'act=builder&task=change_template&fieldid=' + fieldid + '&directory=' + directory;

    $.ajax({
        type:"POST",
        url:url,
        dataType:'HTML',
        success:function (data) {
            $('#form_builder_properties').slideUp('slow', function () {
                $(this).html(data);
                $(this).slideDown('slow');
            });
        }
    });
}
//вывод позиций шаблона для привязки поля
function tpl_poz_field() {

    if ($("#template_type").val() == 0 || $("#template").val() == 0) {
        return;
    }
    var url = bossAdmAjaxUrl + 'act=builder&task=load_poz';
    var qString = $("#tplForm").formSerialize();
    $.ajax({
        type:"POST",
        url:url,
        data:qString,
        dataType:'HTML',
        success:function (data) {

            $('#tpl_poz').slideUp('slow', function () {
                $(this).html(data);
                $(this).slideDown('slow');
            });

        }
    });
}
//сохранение привязки полей к позициям шаблона
function bossSavePoz() {

    var url = bossAdmAjaxUrl + 'act=builder&task=save_poz';
    var qString = $("#tplForm").formSerialize();
    $.ajax({
        type:"POST",
        url:url,
        data:qString,
        dataType:'HTML',
        success:function (data) {
            $(this).html(tpl_poz_field());
        }
    });
}

function confirm(msg, callback, options) {
    var id = 'confirm_' + Math.ceil(100 * Math.random());
    $('body').append('<div id="' + id + '"><p></p></div>');
    $('#' + id + ' p').html(msg).dialog({
        modal:true,
        overlay:{
            opacity:0.5,
            background:"black"
        },
        title:'Подтверждение',
        buttons:{
            "Нет":function () {
                $(this).dialog("close");
                $(this).parents('div:first').remove();
                return false;
            },
            "Да":function () {
                if (callback) callback(options);
                $(this).dialog("close");
                $(this).parents('div:first').remove();
                return true;
            }

        }
    });
}

function deleteField(fieldId, fieldName, fieldTitle) {

    var msg = 'Вы действительно хотите удалить поле <strong>' + fieldTitle + '</strong>?'
    var directory = $('#directory').val();

    confirm(msg, function (options) {

        $.ajax({
            type:"POST",
            url:bossAdmAjaxUrl + 'act=builder&task=delete_field&directory=' + directory + '&fieldid=' + fieldId,
            dataType:'HTML',
            success:function (data) {
                if (data) {
                    $('#' + fieldId + '_li').slideUp('slow', function () {
                        $(this).remove();
                    });

                    $("#form_builder_properties").html('Поле ' + fieldTitle + ' удалено');
                }
                else {
                    $("#form_builder_properties").html('Ошибка удаления поля ' + fieldTitle);
                }
            }
        });

    }, '');
}
function tooltip(id) {
    //configuration properties
    var options = {
        xOffset:10,
        yOffset:25,
        tooltipId:"easyTooltip",
        clickRemove:true
    };

    $("#" + id).each(function (e) {

        var title = $(this).attr("title");
        content = $('#' + id + '_tip').html();
        $(this).attr("title", "");

        if (content != "" && content != undefined) {
            $("body").append("<div id='" + options.tooltipId + "'>" + content + "</div>");
            $("#" + options.tooltipId)
                .css("position", "absolute")
                .css("top", (e.pageY - options.yOffset) + "px")
                .css("left", (e.pageX + options.xOffset) + "px")
                .css("display", "none")
                .fadeIn("fast")
        }

        $(this).mousemove(function (e) {
            $("#" + options.tooltipId)
                .css("top", (e.pageY - options.yOffset) + "px")
                .css("left", (e.pageX + options.xOffset) + "px")
        });

        $(this).mouseout(function (e) {
            $("#" + options.tooltipId).remove();
            $(this).attr("title", title);
        });

        if (options.clickRemove) {
            $(this).mousedown(function (e) {
                $("#" + options.tooltipId).remove();
                $(this).attr("title", title);
            });
        }
    });
}

function getObject(obj) {
    var strObj;
    if (document.all) {
        strObj = document.all.item(obj);
    } else if (document.getElementById) {
        strObj = document.getElementById(obj);
    }
    return strObj;
}

function bossControlFields(directory) {

    var coll = document.fieldForm;
    var errorMSG = '';
    var iserror = 0;
    if (coll != null) {
        var elements = coll.elements;
        // loop through all input elements in form
        for (var i = 0; i < elements.length; i++) {
            // check if element is mandatory; here mosReq=1
            if (elements.item(i).getAttribute('mosReq') == 1) {
                if (elements.item(i).value == '') {
                    //alert(elements.item(i).getAttribute('mosLabel') + ':' + elements.item(i).getAttribute('mosReq'));
                    // add up all error messages
                    errorMSG += elements.item(i).getAttribute('mosLabel') + ' : Заполните это поле\n';
                    // notify user by changing background color, in this case to red
                    elements.item(i).style.background = "red";
                    iserror = 1;
                }
            }
            else if (elements.item(i).getAttribute('mosReq') == 2) {
                if (elements.item(i).value == 'null') {
                    //alert(elements.item(i).getAttribute('mosLabel') + ':' + elements.item(i).getAttribute('mosReq'));
                    // add up all error messages
                    errorMSG += elements.item(i).getAttribute('mosLabel') + ' : Заполните это поле\n';
                    // notify user by changing background color, in this case to red
                    elements.item(i).style.background = "red";
                    iserror = 1;
                }
            }
        }
    }
    if (iserror == 1) {
        alert(errorMSG);
    } else {
        document.fieldForm.type.disabled = false;
        bossSaveField(directory);
    }
}

function prep4SQL(o, fieldNames) {
    if (o.value != '') {
        o.value = o.value.replace('content_', '');
        o.value = 'content_' + o.value.replace(/[^a-zA-Z]+/g, '');
    }
    var fval = o.value;
    if (fieldNames.some(function (item) {
        return item == fval;
    })) {
        alert('Не уникальное имя поля');
    }
}
