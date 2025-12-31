<?php

namespace WCPH\Application;

use WCPH\Domain\PaymentEvent;
use WCPH\Infrastructure\EventRepository;

final class EventRecorder
{
    public function __construct(
        private EventRepository $repository
    ) {}

    public function record(PaymentEvent $event): void
    {
        // Get existing stored events (array)
        $events = $this->repository->all();

        // Append the new event as an ARRAY
        $events[] = $event->toArray();

        // Save the ARRAY back to the repository
        $this->repository->save($events);
    }
}
