<?php declare(strict_types=1);

namespace Circli\Extensions\Queue;

interface EventResolver
{
    public function resolveChannel(string $event): string;
}