<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control\Command;

use Circli\Extensions\Queue\Control\Events\GetStatsEvent;
use Circli\Extensions\Queue\QueueInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends AbstractControlCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'circli:queue:stats';
    /** @var QueueInterface */
    private $queue;

    public function __construct(EventDispatcherInterface $eventDispatcher, QueueInterface $queue)
    {
        parent::__construct($eventDispatcher);
        $this->queue = $queue;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Get stats from event listener.')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Stats from all running listeners')
            ->addOption('event', 'e', InputOption::VALUE_REQUIRED, 'Stats from event of type')
            ->addOption('pid', 'p', InputOption::VALUE_REQUIRED, 'Stats from pid');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        $filter = $options['all'] || $options['pid'] ? 'all' : $options['event'];
        if (!$filter) {
            $filter = 'all';
        }
        $listeners = $this->getListeners($filter);

        if (count($listeners)) {
            foreach ($listeners as $listener) {
                if ($options['pid'] && $listener['pid'] !== (int) $options['pid']) {
                    continue;
                }
                $responseChannel = Uuid::uuid4();
                $this->queue->watch($responseChannel->toString());
                $this->eventDispatcher->dispatch(new GetStatsEvent($listener['pid'], $responseChannel));
            }
            $q = count($listeners);
            while (true) {
                $job = $this->queue->reserve();
                $data = $job->getParsedData();

                $tableData = [];
                foreach ($data['collection'] as $key => $value) {
                    $tableData[] = [$key, $value];
                }

                $table = new Table($output);
                $table
                    ->setHeaderTitle($data['title'])
                    ->setColumnWidths([20, 20])
                    ->setHeaders(['Type', 'Count'])
                    ->setRows($tableData);
                $table->render();

                echo "\n\n";
                $q--;
                if ($q === 0) {
                    exit;
                }
            }
        }
    }
}
