<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Command;

use Circli\Extensions\Queue\Control\Events\KillEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class KillCommand extends AbstractControlCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'circli:queue:kill';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Kill event listener.')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Kill all running listeners')
            ->addOption('event', 'e', InputOption::VALUE_REQUIRED, 'Kill event of type')
            ->addOption('pid', 'p', InputOption::VALUE_REQUIRED, 'Stats from pid');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        $filter = $options['all'] || $options['pid'] ? 'all' : $options['event'];
        if (!$filter) {
            throw new \InvalidArgumentException('Must add filter');
        }
        $listeners = $this->getListeners($filter);

        if (count($listeners)) {
            foreach ($listeners as $listener) {
                if ($options['pid'] && $listener['pid'] !== (int) $options['pid']) {
                    continue;
                }
                $this->eventDispatcher->dispatch(new KillEvent($listener['pid']));
            }
        }
    }
}
