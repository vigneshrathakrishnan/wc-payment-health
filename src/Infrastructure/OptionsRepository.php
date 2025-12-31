<?php

namespace WCPH\Infrastructure;

final class OptionsRepository
{
    private const OPTION_KEY = 'wcph_settings';

    public function get(): array
    {
        return wp_parse_args(
            get_option(self::OPTION_KEY, []),
            [
                'enabled'            => true,
                'retain_events'      => 500,
                'track_offline'      => true,
                'default_range'      => '24h',
                'allowed_gateways'   => [],
            ]
        );
    }

    public function update(array $settings): void
    {
        update_option(self::OPTION_KEY, $settings);
    }
}
