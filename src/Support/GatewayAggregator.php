<?php

namespace WCPH\Support;

final class GatewayAggregator
{
    public function aggregate( array $events ): array
    {
        $stats = [];

        foreach ( $events as $e ) {
            $gateway = $e['gateway'] ?? 'unknown';
            $result  = $e['result']  ?? 'failure';

            $stats[ $gateway ] ??= [
                'success' => 0,
                'failure' => 0,
                'pending' => 0,
            ];

            if ( isset( $stats[ $gateway ][ $result ] ) ) {
                $stats[ $gateway ][ $result ]++;
            }
        }

        return $stats;
    }
}
