<?php

use yii\web\View;
use yii\helpers\Markdown;
use yii\helpers\Url;

/* @var $this View */

if (($pos = strrpos($page, '/')) === false) {
    $baseDir = '';
} else {
    $baseDir = substr($page, 0, $pos) . '/';
}

if ($page == 'README.md') {
    $this->params['breadcrumbs'][] = 'Readme';
    $menus = $this->context->module->getMenus();
    $links = [];
    foreach ($menus as $menu) {
        $url = Url::to($menu['url'], true);
        $links[] = "[**{$menu['label']}**]({$url})";
    }
    $body = str_replace(':smile:', implode('  ', $links) . "\n\n", $this->render("@mdm/admin/README.md"));
} else {
    $body = $this->render("@mdm/admin/{$page}");
}

$body = preg_replace_callback('/\]\((.*?)\)/', function($matches) use($baseDir) {
    $link = $matches[1];
    if (strpos($link, '://') === false) {
        if ($link[0] == '/') {
            $link = Url::current(['page' => ltrim($link, '/')], true);
        } else {
            $link = Url::current(['page' => $baseDir . $link], true);
        }
    }
    return "]($link)";
}, $body);

echo Markdown::process($body, 'gfm');
