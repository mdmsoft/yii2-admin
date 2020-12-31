$('i.glyphicon-refresh-animate').hide();
function updateItems(r) {
    _opts.items.available = r.available;
    _opts.items.assigned = r.assigned;
    search('available');
    search('assigned');
}
function updateUsers(r) {
    _opts.users = r;
    listUsers();
}

$('#list-users').on('click', 'a[data-target]', function () {
    var $this = $(this);
    var target = $this.data('target');
    var page = _opts.users[target];
    if (page !== undefined) {
        $.get(_opts.getUserUrl, {page: page}, function (r) {
            updateUsers(r);
        });
    }

    return false;
});

$('.btn-assign').click(function () {
    var $this = $(this);
    var target = $this.data('target');
    var items = $('select.list[data-target="' + target + '"]').val();

    if (items && items.length) {
        $this.children('i.glyphicon-refresh-animate').show();
        $.post($this.attr('href'), {items: items}, function (r) {
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

function listUsers() {
    var $list = $('#list-users');
    var users = _opts.users.users.map(function (user) {
        return `<span class="label label-info"><a href="${user.link}">${user.username}</a></span>`;
    });
    users.push('<br>');
    if (_opts.users.prev) {
        users.push(`<span class="label label-primary"><a href="#" data-target="${_opts.users.prev}">&laquo;</a></span>`);
    }
    if (_opts.users.next) {
        users.push(`<span class="label label-primary"><a href="#" data-target="${_opts.users.next}">&raquo;</a></span>`);
    }
    $list.html(users.join(' '));
}

// initial
search('available');
search('assigned');
listUsers();
