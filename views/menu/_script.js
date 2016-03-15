$('#parent_name').autocomplete({
    source: function (request, response) {
        var result = [];
        var limit = 10;
        var term = request.term.toLowerCase();
        $.each(_opts.menus, function () {
            var menu = this;
            if (term == '' || menu.name.toLowerCase().indexOf(term) >= 0 ||
                (menu.parent_name && menu.parent_name.toLowerCase().indexOf(term) >= 0) ||
                (menu.route && menu.route.toLowerCase().indexOf(term) >= 0)) {
                result.push(menu);
                limit--;
                if (limit <= 0) {
                    return false;
                }
            }
        });
        response(result);
    },
    focus: function (event, ui) {
        $('#parent_name').val(ui.item.name);
        return false;
    },
    select: function (event, ui) {
        $('#parent_name').val(ui.item.name);
        $('#parent_id').val(ui.item.id);
        return false;
    },
    search: function () {
        $('#parent_id').val('');
    }
}).autocomplete("instance")._renderItem = function (ul, item) {
    return $("<li>")
        .append($('<a>').append($('<b>').text(item.name)).append('<br>')
            .append($('<i>').text(item.parent_name + ' | ' + item.route)))
        .appendTo(ul);
};

$('#route').autocomplete({
    source: _opts.routes,
});