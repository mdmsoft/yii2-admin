$('i.glyphicon-refresh-animate').hide();
function updateRoutes(r) {
    _opts.routes.avaliable = r.avaliable;
    _opts.routes.assigned = r.assigned;
    search('avaliable');
    search('assigned');
}

$('#btn-new').click(function () {
    var $this = $(this);
    var route = $('#inp-route').val().trim();
    if (route != '') {
        $this.children('i.glyphicon-refresh-animate').show();
        $.post(_opts.newUrl, {route: route}, function (r) {
            $('#inp-route').val('').focus();
            updateRoutes(r);
        }).always(function () {
            $this.children('i.glyphicon-refresh-animate').hide();
        });
    }
});

$('.btn-assign').click(function () {
    var $this = $(this);
    var action = $this.data('action');
    var target = action == 'assign' ? 'avaliable' : 'assigned';
    var routes = $('select.list[data-target="' + target + '"]').val();

    if (routes.length) {
        $this.children('i.glyphicon-refresh-animate').show();
        $.post(_opts.assignUrl, {action: action, routes: routes}, function (r) {
            updateRoutes(r);
        }).always(function () {
            $this.children('i.glyphicon-refresh-animate').hide();
        });
    }
    return false;
});

$('#btn-refresh').click(function () {
    var $icon = $(this).children('span.glyphicon');
    $icon.addClass('glyphicon-refresh-animate');
    $.post(_opts.refreshUrl, function (r) {
        updateRoutes(r);
    }).always(function () {
        $icon.removeClass('glyphicon-refresh-animate');
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
    $.each(_opts.routes[target], function () {
        var r = this;
        if (r.indexOf(q) >= 0) {
            $('<option>').text(r).val(r).appendTo($list);
        }
    });
}

// initial
search('avaliable');
search('assigned');
