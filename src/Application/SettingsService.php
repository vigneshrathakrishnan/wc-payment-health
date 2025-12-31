<?php

namespace WCPH\Application;

use WCPH\Infrastructure\OptionsRepository;

final class SettingsService
{
    public function __construct(
        private OptionsRepository $repo
    ) {}

    public function isEnabled(): bool
    {
        return $this->repo->get()['enabled'];
    }

    public function shouldTrackOffline(): bool
    {
        return $this->repo->get()['track_offline'];
    }

    public function retentionLimit(): int
    {
        return (int) $this->repo->get()['retain_events'];
    }

    public function defaultRange(): string
    {
        return $this->repo->get()['default_range'];
    }
}
