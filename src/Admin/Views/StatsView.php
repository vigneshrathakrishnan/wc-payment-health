<?php

namespace WCPH\Admin\Views;

use WCPH\Infrastructure\EventRepository;
use WCPH\Application\SummaryService;
use WCPH\Application\EventFilter;

final class StatsView
{
    public static function render( string $range ): void
    {
        $repo   = new EventRepository();
        $events = EventFilter::byRange( $repo->all(), $range );
        $summary = ( new SummaryService() )->summarize( $events );

        wp_localize_script(
            'wcph-stats',
            'WCPH_STATS',
            [
                'success' => (int) $summary['success'],
                'failure' => (int) $summary['failure'],
            ]
        );
        ?>

        <div style="display:flex;gap:24px;margin-top:20px;">
            <div style="flex:1;max-width:320px;">
                <canvas id="wcphChart" height="240"></canvas>
            </div>

            <div style="flex:1;">
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
                    <div class="card"><strong>Total</strong><br><?php echo $summary['total']; ?></div>
                    <div class="card"><strong>Success</strong><br><?php echo $summary['success']; ?></div>
                    <div class="card"><strong>Failures</strong><br><?php echo $summary['failure']; ?></div>
                    <div class="card"><strong>Failure %</strong><br><?php echo $summary['rate']; ?>%</div>
                </div>
            </div>
        </div>

        <?php
    }
}
