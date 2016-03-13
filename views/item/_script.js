$('i.glyphicon-refresh-animate').hide();
function updateItems(r) {
    _opts.items.avaliable = r.avaliable;
    _opts.items.assigned = r.assigned;
    search('avaliable');
    search('assigned');
}

$('.btn-assign').click(function () {
    var $this = $(this);
    var action = $(this).data('action');
    var target = action == 'assign' ? 'avaliable' : 'assigned';
    var items = $('select.list[data-target="' + target + '"]').val();

    if (items.length) {
        $this.children('i.glyphicon-refresh-animate').show();
        $.post(_opts.assignUrl, {action: action, items: items}, function (r) {
            updateItems(r);
        }).always(function () {
            $this.children('i.glyphicon-refresh-animate').hide();
        });
    }
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
