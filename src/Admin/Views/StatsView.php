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

        <!-- Stats View Starts -->
        <div
            style="
                max-width: 1100px;
                /* margin: 0 auto; */
                padding-top: 24px;
            "
        >
            <div
                style="
                    display:grid;
                    grid-template-columns: 1fr 1fr 420px;
                    gap:32px;
                    align-items:start;
                "
            >
                <!-- CHART (DOMINANT, CENTERED IN ITS AREA) -->
                <div
                    style="
                        grid-column: span 2;
                        display:flex;
                        justify-content:center;
                        align-items:center;
                    "
                >
                    <canvas
                        id="wcphChart"
                        height="300"
                        style="max-width:560px;"
                    ></canvas>
                </div>

                <!-- STATS CARDS -->
                <div
                    style="
                        display:grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap:16px;
                    "
                >
                    <?php
                    $cards = [
                        'Total'     => $summary['total'],
                        'Success'   => $summary['success'],
                        'Failures'  => $summary['failure'],
                        'Failure %' => $summary['rate'] . '%',
                    ];

                    foreach ($cards as $label => $value) :
                    ?>
                        <div
                            class="card"
                            style="
                                aspect-ratio: 1 / 1;
                                padding:18px;
                                display:flex;
                                flex-direction:column;
                                justify-content:center;
                                align-items:center;
                                text-align:center;
                            "
                        >
                            <div
                                style="
                                    font-size:13px;
                                    font-weight:600;
                                    color:#50575e;
                                    margin-bottom:12px;
                                    text-transform:uppercase;
                                    letter-spacing:0.4px;
                                "
                            >
                                <?php echo esc_html($label); ?>
                            </div>

                            <div
                                style="
                                    font-size:36px;
                                    font-weight:700;
                                    line-height:1;
                                "
                            >
                                <?php echo esc_html($value); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Stats View Ends -->

        <?php
    }
}
