<?php

use yii\helpers\Url;
?>
<script type="text/javascript">
<?php $this->beginBlock('JS_END') ?>
    yii.process = (function($) {
        var _onSearch = false;
        var pub = {
            roleSearch: function() {
                if (!_onSearch) {
                    _onSearch = true;
                    var $th = $(this);
                    setTimeout(function() {
                        _onSearch = false;
                        var data = {term: $th.val()};
                        if ($th.data('userid')) {
                            data.userId = $th.data('userid');
                        }
                        var target = '#' + $th.data('target');
                        $.get('<?= Url::toRoute(['role-search']) ?>', data,
                            function(html) {
                                $(target).html(html);
                            });
                    }, 500);
                }
            },
            action: function() {
                var action = $(this).data('action');
                var params = $((action == 'assign' ? '#avaliable' : '#assigned')+', .role-search[data-userid]').serialize();
                $.post('<?= Url::toRoute(['assign','id'=>$userId]) ?>&action='+action,
                params,function(html){
                    $('#assigned').html(html);
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
