<?php

namespace WCPH\Application;

final class SummaryService
{
    public function summarize(array $events): array
    {
        $total = $success = $failure = 0;

        foreach ($events as $e) {
            if ($e['result'] === 'pending') continue;
            $total++;
            $e['result'] === 'success' ? $success++ : $failure++;
        }

        return [
            'total' => $total,
            'success' => $success,
            'failure' => $failure,
            'rate' => $total ? round($failure / $total * 100, 2) : 0,
        ];
    }
}
