<?php

use Circli\Core\Config;
use Circli\Extensions\Queue\Clients\PheanstalkClient;
use Circli\Extensions\Queue\QueueInterface;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;

return [
    PheanstalkInterface::class => function (ContainerInterface $container) {
        $config = $container->get(Config::class);

        // Create using autodetection of socket implementation
        return Pheanstalk::create($config->get('queue.host'));
    },
    QueueInterface::class => \DI\autowire(PheanstalkClient::class),
];
