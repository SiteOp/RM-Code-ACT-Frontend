// Bei Enter nicht absenden sondern in das nächste Feld springen
$(document).on("keypress", ":input:not(textarea):not([type=submit])", function(event) {
    if (event.keyCode == 13) {
        var fields = $(this).closest("#gradetable").find("input ");
        var index = fields.index(this) + 1;

        fields.eq(
            fields.length <= index
                ? 0
                : index
        ).focus();
        event.preventDefault();
    }
});