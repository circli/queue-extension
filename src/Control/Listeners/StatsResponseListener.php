<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Listeners;

use Circli\Extensions\Queue\Control\Events\HandleStatsCommand;
use Circli\Extensions\Queue\Control\Events\StatsResponseEvent;
use Circli\Extensions\Queue\Control\StatsCollection;
use Psr\EventDispatcher\EventDispatcherInterface;

class StatsResponseListener
{
    /** @var StatsCollection */
    private $statsCollection;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(StatsCollection $statsCollection, EventDispatcherInterface $eventDispatcher)
    {
        $this->statsCollection = $statsCollection;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(HandleStatsCommand $event)
    {
        $this->eventDispatcher->dispatch(new StatsResponseEvent($this->statsCollection, $event->getResponseChannel()));
    }
}
