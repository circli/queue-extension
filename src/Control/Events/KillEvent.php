<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Events;

use Circli\Extensions\Queue\AsyncEventListener;
use Circli\Extensions\Queue\Events\PriorityEventInterface;
use Circli\Extensions\Queue\Events\QueueEventInterface;

final class KillEvent implements QueueEventInterface, PriorityEventInterface
{
    /** @var int */
    private $pid;

    public function __construct(int $pid)
    {
        $this->pid = $pid;
    }

    public function getPriority(): int
    {
        return PriorityEventInterface::HIGH_PRIORITY;
    }

    public static function getChannel(): string
    {
        throw new \RuntimeException('You can\'t listen to this event');
    }

    public function getSendChannel(): string
    {
        return sprintf(AsyncEventListener::COMMAND_CHANNEL, $this->pid);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'event' => HandleKillCommand::class,
            'args' => [$this->pid],
        ];
    }
}
