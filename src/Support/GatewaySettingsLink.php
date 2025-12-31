<?php

namespace WCPH\Support;

final class GatewaySettingsLink
{
    public static function for( string $gateway ): string
    {
        return admin_url(
            'admin.php?page=wc-settings&tab=checkout#' . sanitize_key( $gateway )
        );
    }
}
