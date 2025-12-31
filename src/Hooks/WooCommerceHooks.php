<?php

namespace WCPH\Hooks;

use WCPH\Application\EventRecorder;
use WCPH\Domain\PaymentEvent;
use WCPH\Infrastructure\EventRepository;

final class WooCommerceHooks
{
    public static function register(): void
    {
        $recorder = new EventRecorder(new EventRepository());

        add_action(
            'woocommerce_order_status_changed',
            function ($orderId, $oldStatus, $newStatus, $order) use ($recorder) {
                if (! $order) {
                    return;
                }

                // Prevent duplicate recording
                if ($order->get_meta('_wcph_recorded')) {
                    return;
                }

                if (in_array($newStatus, ['processing', 'completed'], true)) {
                    self::record($recorder, $orderId, 'success', 'order_status_' . $newStatus);

                    $order->update_meta_data('_wcph_recorded', '1');
                    $order->save();
                }

                if ($newStatus === 'failed') {
                    self::record($recorder, $orderId, 'failure', 'order_status_failed');

                    $order->update_meta_data('_wcph_recorded', '1');
                    $order->save();
                }
            },
        10, 4);

        // Export events as CSV
        add_action('admin_post_wcph_export_events', function () {
            if (! current_user_can('manage_woocommerce')) {
                wp_die('Unauthorized');
            }

            // Match the nonce used in the export link
            check_admin_referer('wcph_export');

            $repo = new \WCPH\Infrastructure\EventRepository();
            $repo->exportCsv();

            // NO redirect after export
            exit;
        });


        // Import events from CSV
        add_action('admin_post_wcph_import_events', function () {
            if (! current_user_can('manage_woocommerce')) {
                wp_die('Unauthorized');
            }

            check_admin_referer('wcph_table_actions');

            $redirect = wp_get_referer() ?: admin_url();

            // No file selected
            if (empty($_FILES['csv_file']) || empty($_FILES['csv_file']['tmp_name'])) {
                wp_safe_redirect(
                    add_query_arg('wcph_notice', 'no_file', $redirect)
                );
                exit;
            }

            $repo = new \WCPH\Infrastructure\EventRepository();

            try {
                $repo->importCsv($_FILES['csv_file']);

                wp_safe_redirect(
                    add_query_arg('wcph_notice', 'import_success', $redirect)
                );
                exit;

            } catch (\Throwable $e) {
                wp_safe_redirect(
                    add_query_arg('wcph_notice', 'import_error', $redirect)
                );
                exit;
            }
        });

        // Reset events
        add_action('admin_post_wcph_reset_events', function () {
            if (! current_user_can('manage_woocommerce')) {
                wp_die('Unauthorized');
            }

            check_admin_referer('wcph_reset_events');

            $repo = new \WCPH\Infrastructure\EventRepository();
            $repo->reset();

            wp_safe_redirect(
                add_query_arg(
                    'wcph_notice',
                    'reset_success',
                    wp_get_referer() ?: admin_url()
                )
            );
            exit;
        });
    }

    private static function record(
        EventRecorder $recorder,
        int $orderId,
        string $result,
        string $hook
    ): void {
        $order = wc_get_order($orderId);
        if (! $order) return;

        $recorder->record(
            new PaymentEvent(
                $order->get_id(),
                $order->get_payment_method() ?: 'unknown',
                $order->get_meta('_stripe_upe_payment_type') ?: 'default',
                $result,
                current_time('mysql'),
                '',
                $hook
            )
        );
    }
}
