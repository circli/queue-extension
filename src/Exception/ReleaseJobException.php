<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Exception;

interface ReleaseJobException extends \Throwable
{
    public function getPriority(): int;
    public function getDelay(): int;
}