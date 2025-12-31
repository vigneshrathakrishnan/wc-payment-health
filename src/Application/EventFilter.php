<?php

namespace WCPH\Application;

final class EventFilter
{
    public static function byRange(array $events, string $range): array
    {
        if ($range === 'all') {
            return $events;
        }

        $since = match ($range) {
            '7d'  => strtotime('-7 days'),
            '30d' => strtotime('-30 days'),
            default => strtotime('-24 hours'),
        };

        return array_values(array_filter(
            $events,
            static fn (array $e): bool =>
                !empty($e['occurred_at']) &&
                strtotime($e['occurred_at']) !== false &&
                strtotime($e['occurred_at']) >= $since
        ));
    }
}
