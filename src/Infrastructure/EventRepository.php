<?php

namespace WCPH\Infrastructure;

final class EventRepository
{
    private const OPTION = 'wc_payment_health_events';
    private const LIMIT  = 500;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $events = get_option(self::OPTION, []);
        return is_array($events) ? $events : [];
    }

    /**
     * @param array<int, array<string, mixed>> $events
     */
    public function save(array $events): void
    {
        update_option(
            self::OPTION,
            array_slice($events, -self::LIMIT),
            false // do not autoload
        );
    }

    public function reset(): void
    {
        delete_option(self::OPTION);
    }

    public function exportCsv(): void
    {
        $events = $this->all();
        if (empty($events)) {
            wp_die('No events to export.');
        }

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=wc-payment-health-events.csv');

        $out = fopen('php://output', 'w');
        if (! $out) {
            wp_die('Unable to open output stream.');
        }

        // CSV header
        fputcsv($out, array_keys($events[0]));

        foreach ($events as $row) {
            fputcsv($out, $row);
        }

        fclose($out);
        exit;
    }

    public function importCsv(array $file): void
    {
        if (
            empty($file['tmp_name']) ||
            ! is_uploaded_file($file['tmp_name'])
        ) {
            return;
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (! $handle) {
            return;
        }

        $header = fgetcsv($handle);
        if (! is_array($header)) {
            fclose($handle);
            return;
        }

        $events = $this->all();

        /**
         * Build idempotency map using order_id (if present)
         * One event per order_id (MVP rule)
         */
        $existingOrderIds = [];

        foreach ($events as $event) {
            if (! empty($event['order_id'])) {
                $existingOrderIds[(string) $event['order_id']] = true;
            }
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }

            $event = array_combine($header, $row);
            if (! is_array($event)) {
                continue;
            }

            // If order_id exists and is already stored → skip
            if (
                ! empty($event['order_id']) &&
                isset($existingOrderIds[(string) $event['order_id']])
            ) {
                continue;
            }

            // Track newly imported order_id to avoid duplicates within same file
            if (! empty($event['order_id'])) {
                $existingOrderIds[(string) $event['order_id']] = true;
            }

            $events[] = $event;
        }

        fclose($handle);
        $this->save($events);
    }
}
