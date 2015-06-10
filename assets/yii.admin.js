yii.admin = (function ($) {
    var _onSearch = false;
    var pub = {
        userId: undefined,
        roleName: undefined,
        assignUrl: undefined,
        searchUrl: undefined,
        assign: function (action) {
            var params = {
                id: pub.userId,
                action: action,
                roles: $('#list-' + (action == 'assign' ? 'avaliable' : 'assigned')).val(),
            };
            $.post(pub.assignUrl, params,
                function () {
                    pub.searchAssignmet('avaliable', true);
                    pub.searchAssignmet('assigned', true);
                });
        },
        searchAssignmet: function (target, force) {
            if (!_onSearch || force) {
                _onSearch = true;
                var $inp = $('#search-' + target);
                setTimeout(function () {
                    var data = {
                        id: pub.userId,
                        target: target,
                        term: $inp.val(),
                    };
                    $.get(pub.searchUrl, data,
                        function (r) {
                            var $list = $('#list-' + target);
                            $list.html('');
                            if (r.Roles) {
                                var $group = $('<optgroup label="Roles">');
                                $.each(r.Roles, function () {
                                    $('<option>').val(this).text(this).appendTo($group);
                                });
                                $group.appendTo($list);
                            }
                            if (r.Permissions) {
                                var $group = $('<optgroup label="Permissions">');
                                $.each(r.Permissions, function () {
                                    $('<option>').val(this).text(this).appendTo($group);
                                });
                                $group.appendTo($list);
                            }
                        }).done(function () {
                        _onSearch = false;
                    });
                }, 500);
            }
        },
        // role & permission
        addChild: function (action) {
            var params = {
                id: pub.roleName,
                action: action,
                roles: $('#list-' + (action == 'assign' ? 'avaliable' : 'assigned')).val(),
            };
            $.post(pub.assignUrl, params,
                function () {
                    pub.searchRole('avaliable', true);
                    pub.searchRole('assigned', true);
                });
        },
        searchRole: function (target, force) {
            if (!_onSearch || force) {
                _onSearch = true;
                var $inp = $('#search-' + target);
                setTimeout(function () {
                    var data = {
                        id: pub.roleName,
                        target: target,
                        term: $inp.val(),
                    };
                    $.get(pub.searchUrl, data,
                        function (r) {
                            var $list = $('#list-' + target);
                            $list.html('');
                            if (r.Roles) {
                                var $group = $('<optgroup label="Roles">');
                                $.each(r.Roles, function () {
                                    $('<option>').val(this).text(this).appendTo($group);
                                });
                                $group.appendTo($list);
                            }
                            if (r.Permissions) {
                                var $group = $('<optgroup label="Permissions">');
                                $.each(r.Permissions, function () {
                                    $('<option>').val(this).text(this).appendTo($group);
                                });
                                $group.appendTo($list);
                            }
                            if (r.Routes) {
                                var $group = $('<optgroup label="Routes">');
                                $.each(r.Routes, function () {
                                    $('<option>').val(this).text(this).appendTo($group);
                                });
                                $group.appendTo($list);
                            }
                        }).done(function () {
                        _onSearch = false;
                    });
                }, 500);
            }
        },
        // route
        assignRoute: function (action) {
            var params = {
                action: action,
                routes: $('#list-' + (action == 'assign' ? 'avaliable' : 'assigned')).val(),
            };
            $.post(pub.assignUrl, params,
                function () {
                    pub.searchRoute('avaliable', 0, true);
                    pub.searchRoute('assigned', 0, true);
                });
        },
        searchRoute: function (target, refresh, force) {
            if (!_onSearch || force) {
                _onSearch = true;
                var $inp = $('#search-' + target);
                setTimeout(function () {
                    var data = {
                        target: target,
                        term: $inp.val(),
                        refresh: refresh,
                    };
                    $.get(pub.searchUrl, data,
                        function (r) {
                            var $list = $('#list-' + target);
                            $list.html('');
                            $.each(r, function (key, val) {
                                var $opt = $('<option>').val(key).text(key);
                                if (!val) {
                                    $opt.addClass('lost');
                                }
                                $opt.appendTo($list);
                            });

                        }).done(function () {
                        _onSearch = false;
                    });
                }, 500);
            }
        },
        initProperties: function (properties) {
            $.each(properties, function (key, val) {
                pub[key] = val;
            });
        },
    }
    return pub;
})(jQuery)