<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Events;

interface PriorityEventInterface
{
    public const DEFAULT_PRIORITY = 1024;
    public const HIGH_PRIORITY = 500;
    public const LOW_PRIORITY = 2000;

    public function getPriority(): int;
}