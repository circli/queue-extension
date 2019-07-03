<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Events;

final class HandleStatsCommand
{
    /** @var int */
    private $pid;
    /** @var string */
    private $responseChannel;

    public function __construct(int $pid, string $responseChannel)
    {
        $this->pid = $pid;
        $this->responseChannel = $responseChannel;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getResponseChannel(): string
    {
        return $this->responseChannel;
    }
}
