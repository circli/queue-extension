<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Events;

use Psr\EventDispatcher\StoppableEventInterface;

final class HandleKillCommand implements StoppableEventInterface
{
    /** @var int */
    private $pid;
    /** @var bool */
    private $kill;

    public function __construct(int $pid)
    {
        $this->pid = $pid;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function kill(): void
    {
        $this->kill = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->kill === true;
    }
}
