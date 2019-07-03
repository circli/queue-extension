<?php declare(strict_types=1);

namespace Circli\Extensions\Queue;

use Circli\Extensions\Queue\Events\DelayedQueueEventInterface;
use Circli\Extensions\Queue\Events\PriorityEventInterface;
use Circli\Extensions\Queue\Events\QueueEventInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class AsyncDispatcher implements ListenerProviderInterface
{
    /** @var QueueInterface */
    private $client;

    public function __construct(QueueInterface $client)
    {
        $this->client = $client;
    }

    public function __invoke(QueueEventInterface $event)
    {
        $priority = PriorityEventInterface::DEFAULT_PRIORITY;
        if ($event instanceof PriorityEventInterface) {
            $priority = $event->getPriority();
        }
        if ($event instanceof DelayedQueueEventInterface) {
            $job = $this->client->put(
                $event->getSendChannel(),
                json_encode($event),
                $priority,
                $event->getDelay()
            );
        }
        else {
            $job = $this->client->put($event->getSendChannel(), json_encode($event), $priority);
        }
    }

    public function getListenersForEvent(object $event): iterable
    {
        if ($event instanceof QueueEventInterface) {
            return [$this];
        }
        return [];
    }
}
