<?php
namespace WCPH;

use WCPH\Hooks\WooCommerceHooks;
use WCPH\Admin\Menu;
use WCPH\Admin\Assets;

require dirname(__DIR__) . '/vendor/autoload.php';

final class Bootstrap
{
    public static function init(): void
    {
        WooCommerceHooks::register();
        Menu::register();
        Assets::register();
    }
}