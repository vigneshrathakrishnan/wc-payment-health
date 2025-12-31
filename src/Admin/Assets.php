<?php

namespace WCPH\Admin;

final class Assets
{
    public static function register(): void
    {
        add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue' ] );
    }

    public static function enqueue( string $hook ): void
    {
        // Load ONLY on current plugin admin page
        if ( $hook !== 'woocommerce_page_wc-payment-health' ) {
            return;
        }

        wp_enqueue_script(
            'wcph-chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js',
            [],
            null,
            true
        );

        wp_enqueue_script(
            'wcph-stats',
            plugins_url( '../../assets/admin/stats.js', __FILE__ ),
            [ 'wcph-chartjs' ],
            '1.0.0',
            true
        );
    }
}
