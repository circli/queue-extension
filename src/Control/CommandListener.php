<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control;

use Circli\Extensions\Queue\Control\Events\HandleKillCommand;
use Circli\Extensions\Queue\Control\Events\HandleStatsCommand;
use Circli\Extensions\Queue\Control\Listeners\KillListener;
use Circli\Extensions\Queue\Control\Listeners\StatsResponseListener;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class CommandListener implements ListenerProviderInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param object $event
     *   An event for which to return the relevant listeners.
     * @return iterable[callable]
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($event instanceof HandleKillCommand) {
            return [new KillListener()];
        }
        if ($event instanceof HandleStatsCommand) {
            return [$this->container->get(StatsResponseListener::class)];
        }
        return [];
    }
}
