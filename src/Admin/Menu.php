<?php

namespace WCPH\Admin;

use WCPH\Admin\SettingsPage;

final class Menu
{
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'addMenuPage']);
    }

    public static function addMenuPage(): void
    {
        add_submenu_page(
            'woocommerce',
            'Payment Health',
            'Payment Health',
            'manage_woocommerce',
            'wc-payment-health',
            [SettingsPage::class, 'render']
        );
    }
}