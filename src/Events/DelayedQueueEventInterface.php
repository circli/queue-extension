<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Events;

interface DelayedQueueEventInterface extends QueueEventInterface
{
    public function getDelay(): int;
}
