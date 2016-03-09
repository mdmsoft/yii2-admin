function updateRoutes(r) {
    _opts.routes.avaliable = r.avaliable;
    _opts.routes.assigned = r.assigned;
    search('avaliable');
    search('assigned');
}

$('#btn-new').click(function () {
    var route = $('#inp-route').val().trim();
    if (route != '') {
        $.post(_opts.newUrl, {route: route}, function (r) {
            $('#inp-route').val('').focus();
            updateRoutes(r);
        });
    }
});

$('.btn-assign').click(function () {
    var action = $(this).data('action');
    var target = action == 'assign' ? 'avaliable' : 'assigned';
    var routes = $('select.list[data-target="' + target + '"]').val();
    
    $.post(_opts.assignUrl, {action: action, routes: routes}, function (r) {
        updateRoutes(r);
    });
    return false;
});

$('#btn-refresh').click(function (){
    $.post(_opts.refreshUrl, function (r) {
        updateRoutes(r);
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
