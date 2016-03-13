function updateItems(r) {
    _opts.items.avaliable = r.avaliable;
    _opts.items.assigned = r.assigned;
    search('avaliable');
    search('assigned');
}

$('.btn-assign').click(function () {
    var action = $(this).data('action');
    var target = action == 'assign' ? 'avaliable' : 'assigned';
    var roles = $('select.list[data-target="' + target + '"]').val();

    $.post(_opts.assignUrl, {action: action, roles: roles}, function (r) {
        updateItems(r);
    });
    return false;
});

$('.search[data-target]').keyup(function () {
    search($(this).data('target'));
});

function search(target) {
    var $list = $('select.list[data-target="' + target + '"]');
    $list.html('');
    var q = $('.search[data-target="' + target + '"]').val();

    var groups = {
        role: [$('<optgroup label="Roles">'), false],
        permission: [$('<optgroup label="Permission">'), false],
        route: [$('<optgroup label="Routes">'), false],
    };
    $.each(_opts.items[target], function (name, group) {
        if (name.indexOf(q) >= 0) {
            $('<option>').text(name).val(name).appendTo(groups[group][0]);
            groups[group][1] = true;
        }
    });
    $.each(groups, function () {
        if (this[1]) {
            $list.append(this[0]);
        }
    });
}

// initial
search('avaliable');
search('assigned');
