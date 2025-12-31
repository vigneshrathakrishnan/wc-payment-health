<?php

namespace WCPH\Admin\Views;

use WCPH\Infrastructure\EventRepository;
use WCPH\Application\EventFilter;
use WCPH\Support\GatewayAggregator;
use WCPH\Support\GatewaySettingsLink;

final class TableView
{
    public static function render( string $range ): void
    {
        // Display notices
        if (! empty($_GET['wcph_notice'])) {
            $notice = sanitize_text_field($_GET['wcph_notice']);

            match ($notice) {
                'import_success' => print '
                    <div class="notice notice-success is-dismissible">
                        <p>Payment health data imported successfully.</p>
                    </div>
                ',
                'no_file' => print '
                    <div class="notice notice-warning is-dismissible">
                        <p>Please select a CSV file to import.</p>
                    </div>
                ',
                'import_error' => print '
                    <div class="notice notice-error is-dismissible">
                        <p>Failed to import CSV. Please check the file format.</p>
                    </div>
                ',
                default => null,
            };
        }


        $repo   = new EventRepository();

        $events = EventFilter::byRange(
            $repo->all(),
            $range
        );

        $stats = ( new GatewayAggregator() )->aggregate( $events );
        ?>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Gateway</th>
                    <th>Success</th>
                    <th>Failures</th>
                    <th>Pending</th>
                    <th>Failure %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $stats as $gateway => $row ) :

                    $success = (int) ( $row['success'] ?? 0 );
                    $failure = (int) ( $row['failure'] ?? 0 );
                    $pending = (int) ( $row['pending'] ?? 0 );

                    $total = $success + $failure;
                    $rate  = $total
                        ? round( ( $failure / $total ) * 100, 2 )
                        : 0;
                ?>
                    <tr>
                        <td>
                            <a href="<?php echo esc_url(
                                GatewaySettingsLink::for( $gateway )
                            ); ?>">
                                <?php echo esc_html( $gateway ); ?>
                            </a>
                        </td>
                        <td><?php echo $success; ?></td>
                        <td><?php echo $failure; ?></td>
                        <td><?php echo $pending; ?></td>
                        <td><?php echo esc_html( $rate ); ?>%</td>
                    </tr>
                <?php endforeach; ?>

                <?php if ( empty( $stats ) ) : ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:#666;">
                            No payment data available for this period.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TABLE ACTIONS -->
        <div style="margin:16px 0; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <!-- EXPORT CSV (GET) -->
            <a href="<?php echo esc_url(
                wp_nonce_url(
                    admin_url('admin-post.php?action=wcph_export_events'),
                    'wcph_export'
                )
            ); ?>" class="button button-secondary">
                Export CSV
            </a>

            <!-- IMPORT CSV (POST) -->
            <form
                method="post"
                enctype="multipart/form-data"
                action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"
                style="display:flex; gap:6px; align-items:center;"
            >
                <?php wp_nonce_field('wcph_table_actions'); ?>
                <input type="hidden" name="action" value="wcph_import_events">

                <input
                    type="file"
                    name="csv_file"
                    accept=".csv"
                    style="max-width:220px;"
                >

                <button type="submit" class="button">
                    Import CSV
                </button>
            </form>

            <!-- RESET DATA (POST – DANGEROUS) -->
            <form
                method="post"
                action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"
                style="margin-left:auto;"
            >
                <?php wp_nonce_field('wcph_reset_events'); ?>
                <input type="hidden" name="action" value="wcph_reset_events">

                <button
                    type="submit"
                    class="button button-secondary"
                    onclick="return confirm('Delete all payment health data? This cannot be undone.');"
                >
                    Reset Data
                </button>
            </form>
        </div>

        <?php
    }
}
