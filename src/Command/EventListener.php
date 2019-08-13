<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Command;

use Circli\Extensions\Queue\AsyncEventListener;
use Circli\Extensions\Queue\EventResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EventListener extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'circli:queue:listener';
    /** @var AsyncEventListener */
    private $asyncEventListener;
    /** @var EventResolver */
    private $eventResolver;

    public function __construct(AsyncEventListener $asyncEventListener, EventResolver $eventResolver)
    {
        parent::__construct();
        $this->asyncEventListener = $asyncEventListener;
        $this->eventResolver = $eventResolver;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Start listener for a specific event.')
            ->addArgument('event', InputArgument::REQUIRED, 'Event to listen for')
            ->addOption('long', 'l', InputOption::VALUE_NONE, 'Ignore max runtime');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $this->eventResolver->resolveChannel($input->getArgument('event'));

        if ($input->hasOption('long') && $input->getOption('long')) {
            $this->asyncEventListener->setTtl(-1);
        }

        $this->asyncEventListener->listen($channel);
    }
}
