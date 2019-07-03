<?php declare(strict_types=1);

namespace Circli\Extensions\Queue;

use Circli\Contracts\ExtensionInterface;
use Circli\Contracts\InitCliApplication;
use Circli\Contracts\PathContainer;
use Circli\Core\ConditionalDefinition;
use Circli\Core\Conditions\ClassExists;
use Circli\Core\Events\PostContainerBuild;
use Circli\Extensions\Queue\Command\EventListener;
use Circli\Extensions\Queue\Control\Command\KillCommand;
use Circli\Extensions\Queue\Control\Command\ListCommand;
use Circli\Extensions\Queue\Control\Command\StatsCommand;
use Circli\Extensions\Queue\Control\CommandListener;
use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Symfony\Component\Console\Application;

final class Extension implements ExtensionInterface, ListenerProviderInterface, InitCliApplication
{
    public function getListenersForEvent(object $event): iterable
    {
        if ($event instanceof PostContainerBuild) {
            $providers = [];
            $providers[] = function (PostContainerBuild $event) {
                $diContainer = $event->getContainer()->getContainer();
                $providerStore = $event->getContainer()->getEventListenerProvider();
                $providerStore->addProvider($diContainer->get(AsyncDispatcher::class));
                $providerStore->addProvider($diContainer->get(CommandListener::class));
            };
            yield from $providers;
        }
    }

    public function __construct(PathContainer $paths)
    {
    }

    public function configure(): array
    {
        $definitionPath = dirname(__DIR__) . '/config/container';

        return [
            include $definitionPath . '/default.php',
            new ConditionalDefinition($definitionPath . '/pheanstalk.php', new ClassExists(Pheanstalk::class)),
        ];
    }

    public function initCli(Application $cli, ContainerInterface $container)
    {
        $cli->add($container->get(EventListener::class));
        $cli->add($container->get(KillCommand::class));
        $cli->add($container->get(StatsCommand::class));
        $cli->add($container->get(ListCommand::class));
    }
}
