<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Listeners;

use Circli\Extensions\Queue\Control\Events\HandleKillCommand;

class KillListener
{
    public function __invoke(HandleKillCommand $event)
    {
        $event->kill();
    }
}