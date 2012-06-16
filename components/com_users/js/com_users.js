$(document).ready(function () {
    $("#save").click(function () {
        $("input#task").val('saveUserEdit');
        $("#mosUserForm").submit();
    });
    $("#cancel").click(function () {
        $("input#task").val('cancel');
        $("#mosUserForm").submit();
    });
    if ((jQuery.inArray("jquery.validate", _js_defines) > -1)) {
        jQuery.validator.messages.required = "";
        $("#mosUserForm").validate();
    }
});