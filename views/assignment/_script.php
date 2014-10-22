<?php

use yii\helpers\Url;
?>
<script type="text/javascript">
<?php $this->beginBlock('JS_END') ?>
    yii.process = (function ($) {
        var _onSearch = false;
        var pub = {
            roleSearch: function () {
                if (!_onSearch) {
                    _onSearch = true;
                    var $th = $(this);
                    setTimeout(function () {
                        _onSearch = false;
                        var data = {
                            id:<?= json_encode($id) ?>,
                            target:$th.data('target'),
                            term: $th.val(),
                        };
                        var target = '#' + $th.data('target');
                        $.get('<?= Url::toRoute(['role-search']) ?>', data,
                            function (html) {
                                $(target).html(html);
                            });
                    }, 500);
                }
            },
            action: function () {
                var action = $(this).data('action');
                var params = $((action == 'assign' ? '#avaliable' : '#assigned') + ', .role-search').serialize();
                var urlAssign = '<?= Url::toRoute(['assign', 'id' => $id,'action'=>'assign']) ?>';
                var urlDelete = '<?= Url::toRoute(['assign', 'id' => $id,'action'=>'delete']) ?>';
                $.post(action=='assign'?urlAssign : urlDelete,
                    params, function (r) {
                        $('#avaliable').html(r[0]);
                        $('#assigned').html(r[1]);
                    });

                return false;
            }
        }

        return pub;
    })(window.jQuery);
<?php $this->endBlock(); ?>

<?php $this->beginBlock('JS_READY') ?>
    $('.role-search').keydown(yii.process.roleSearch);
    $('a[data-action]').click(yii.process.action);
<?php $this->endBlock(); ?>
</script>
<?php
yii\web\YiiAsset::register($this);
$this->registerJs($this->blocks['JS_END'], yii\web\View::POS_END);
$this->registerJs($this->blocks['JS_READY'], yii\web\View::POS_READY);
