<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractControlCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'circli:queue:list';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('List event listener.')
            ->addOption('event', 'e', InputOption::VALUE_REQUIRED, 'Kill event of type');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        $listeners = $this->getListeners($options['event'] ?? 'all');

        if (count($listeners)) {
            $table = new Table($output);

            $table
                ->setColumnWidths([20, 20])
                ->setStyle('borderless')
                ->setHeaders(['Listener', 'Pid']);

            foreach ($listeners as $listener) {
                $table->addRow([$listener['channel'], $listener['pid']]);
            }
            $table->render();
        }
    }
}
