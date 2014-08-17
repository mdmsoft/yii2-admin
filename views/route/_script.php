<?php

use yii\helpers\Url;
?>
<style>
<?php $this->beginBlock('CSS') ?>
    option.lost{
        text-decoration: line-through;
    }
<?php $this->endBlock() ?>
</style>
<script type="text/javascript">
<?php $this->beginBlock('JS_END') ?>
    yii.process = (function ($) {
        var _onSearch = false;
        var pub = {
            refresh: function () {
                $.get('<?= Url::toRoute(['route-search']) ?>', {
                    target: 'new',
                    term: $('input[name="search_av"]').val(),
                    refresh: '1',
                },
                    function (html) {
                        $('#new').html(html);
                    });

                return false;
            },
            roleSearch: function () {
                if (!_onSearch) {
                    _onSearch = true;
                    var $th = $(this);
                    setTimeout(function () {
                        _onSearch = false;
                        var data = {
                            target: $th.data('target'),
                            term: $th.val(),
                        };
                        var target = '#' + $th.data('target');
                        $.get('<?= Url::toRoute(['route-search']) ?>', data,
                            function (html) {
                                $(target).html(html);
                            });
                    }, 500);
                }
            },
            action: function () {
                var action = $(this).data('action');
                var params = $((action == 'assign' ? '#new' : '#exists') + ', .role-search').serialize();
                var urlAssign = '<?= Url::toRoute(['assign', 'action' => 'assign']) ?>';
                var urlDelete = '<?= Url::toRoute(['assign', 'action' => 'delete']) ?>';

                $.post(action == 'assign' ? urlAssign : urlDelete,
                    params, function (r) {
                        $('#new').html(r[0]);
                        $('#exists').html(r[1]);
                    }, 'json');

                return false;
            }
        }

        return pub;
    })(window.jQuery);
<?php $this->endBlock(); ?>

<?php $this->beginBlock('JS_READY') ?>
    $('.role-search').keydown(yii.process.roleSearch);
    $('a[data-action]').click(yii.process.action);
    $('#btn-refresh').click(yii.process.refresh);
<?php $this->endBlock(); ?>
</script>
<?php
yii\web\YiiAsset::register($this);
$this->registerCss($this->blocks['CSS']);
$this->registerJs($this->blocks['JS_END'], yii\web\View::POS_END);
$this->registerJs($this->blocks['JS_READY'], yii\web\View::POS_READY);
