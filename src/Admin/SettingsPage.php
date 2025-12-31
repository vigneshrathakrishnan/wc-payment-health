<?php

namespace WCPH\Admin;

use WCPH\Admin\Views\StatsView;
use WCPH\Admin\Views\TableView;
use WCPH\Infrastructure\EventRepository;

final class SettingsPage
{
    public static function render(): void
    {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $tab   = $_GET['tab']   ?? 'stats';
        $tab   = in_array( $tab, [ 'stats', 'table' ], true ) ? $tab : 'stats';
        $range = $_GET['range'] ?? '24h';

        // Handle POST/GET actions ONLY for table tab
        if ( $tab === 'table' ) {
            self::handleActions();
        }

        echo '<div class="wrap">';
        echo '<h1>Payment Health</h1>';

        self::renderTabs( $tab );
        self::renderRangeSelector( $tab, $range );

        if ( $tab === 'stats' ) {
            StatsView::render( $range );
        } else {
            TableView::render( $range );
        }

        echo '</div>';
    }

    private static function renderTabs( string $active ): void
    {
        $base = admin_url( 'admin.php?page=wc-payment-health' );

        echo '<h2 class="nav-tab-wrapper">';
        echo self::tab( 'stats', 'Stats', $active, $base );
        echo self::tab( 'table', 'Table', $active, $base );
        echo '</h2>';
    }

    private static function tab(
        string $id,
        string $label,
        string $active,
        string $base
    ): string {
        $class = $id === $active ? 'nav-tab nav-tab-active' : 'nav-tab';

        $url = esc_url(
            add_query_arg(
                [
                    'tab'   => $id,
                    'range' => $_GET['range'] ?? '24h',
                ],
                $base
            )
        );

        return "<a href='{$url}' class='{$class}'>{$label}</a>";
    }

    private static function renderRangeSelector( string $tab, string $range ): void
    {
        echo '<div style="margin:12px 0;">';

        foreach ( [
            '24h' => '24 Hours',
            '7d'  => '7 Days',
            '30d' => '30 Days',
            'all' => 'All Time',
        ] as $key => $label ) {

            $url = esc_url(
                add_query_arg(
                    [
                        'tab'   => $tab,
                        'range' => $key,
                    ]
                )
            );

            $class = $range === $key ? 'button button-primary' : 'button';

            echo "<a class='{$class}' href='{$url}'>{$label}</a> ";
        }

        echo '</div><hr>';
    }

    private static function handleActions(): void
    {
        $repo = new EventRepository();

        if ( isset( $_GET['export'] ) ) {
            $repo->exportCsv();
        }

        if (
            isset( $_POST['import_csv'] ) &&
            ! empty( $_FILES['csv_file'] )
        ) {
            check_admin_referer( 'wcph_table_actions' );
            $repo->importCsv( $_FILES['csv_file'] );
        }

        if ( isset( $_POST['reset_events'] ) ) {
            check_admin_referer( 'wcph_table_actions' );
            $repo->reset();
        }
    }
}
