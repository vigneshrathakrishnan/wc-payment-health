<?php

use PHPUnit\Framework\TestCase;
use WCPH\Application\SummaryService;

final class SummaryServiceTest extends TestCase
{
    public function test_summary_calculation(): void
    {
        $service = new SummaryService();

        $summary = $service->summarize([
            ['result' => 'success'],
            ['result' => 'failure'],
            ['result' => 'success'],
        ]);

        $this->assertSame(3, $summary['total']);
        $this->assertSame(2, $summary['success']);
        $this->assertSame(1, $summary['failure']);
        $this->assertSame(33.33, $summary['rate']);
    }
}
