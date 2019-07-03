<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control;

use Psr\EventDispatcher\ListenerProviderInterface;

class EventProvider implements ListenerProviderInterface
{
    /**
     * @param object $event
     *   An event for which to return the relevant listeners.
     * @return iterable[callable]
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event): iterable
    {

    }
}
