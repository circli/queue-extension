<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Events;

use Circli\Extensions\Queue\Control\StatsCollection;
use Circli\Extensions\Queue\Events\QueueEventInterface;

final class StatsResponseEvent implements QueueEventInterface
{
    /** @var StatsCollection */
    private $stats;
    /** @var string */
    private $channel;

    public function __construct(StatsCollection $stats, string $channel)
    {
        $this->stats = $stats;
        $this->channel = $channel;
    }

    public static function getChannel(): string
    {
        throw new \RuntimeException('Can\'t listen to this event');
    }

    public function getSendChannel(): string
    {
        return $this->channel;
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
        return $this->stats->jsonSerialize();
    }
}
