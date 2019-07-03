<?php declare(strict_types=1);

namespace Circli\Extensions\Queue;

interface QueueInterface
{
    public function put(string $channel, string $payload, int $priority = null, int $delay = null): Job;

    public function watch(string ...$channels): QueueInterface;

    public function reserve(): Job;

    public function bury(Job $job): void;

    public function delete(Job $job): void;

    public function release(Job $job, ?int $priority = null, int $delay = null): void;
}
