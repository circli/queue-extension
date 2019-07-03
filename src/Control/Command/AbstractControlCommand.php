<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Command;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractControlCommand extends Command
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function getListeners(string $filter): array
    {
        $cmdPipes = ['ps -e -o pid,cmd', 'grep circli:queue:listener', 'grep -v grep'];
        if ($filter !== 'all') {
            $cmdPipes[] = 'grep ' . escapeshellarg($filter);
        }
        exec(implode(' | ', $cmdPipes), $o);
        $return = [];
        if (count($o)) {
            foreach ($o as $line) {
                [$rawPid, $rawCmd] = explode(' ', $line, 2);
                $pid = (int)$rawPid;
                if ($pid > 300) {
                    [,$listenerType] = explode(' circli:queue:listener ', $rawCmd, 2);
                    $return[] = ['pid' => $pid, 'channel' => $listenerType];
                }
            }
        }
        return $return;
    }
}
