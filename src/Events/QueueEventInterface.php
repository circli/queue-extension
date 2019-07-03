<?php

namespace Circli\Extensions\Queue\Events;

interface QueueEventInterface extends \JsonSerializable
{
    public static function getChannel(): string;
    public function getSendChannel(): string;
}
