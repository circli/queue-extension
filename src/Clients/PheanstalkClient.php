<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Clients;

use Circli\Extensions\Queue\Job;
use Circli\Extensions\Queue\QueueInterface;
use Pheanstalk\Contract\PheanstalkInterface;

class PheanstalkClient implements QueueInterface
{
    /** @var PheanstalkInterface */
    private $client;

    public function __construct(PheanstalkInterface $client)
    {
        $this->client = $client;
    }

    public function put(string $channel, string $payload, int $priority = null, int $delay = null): Job
    {
        $job = $this->client
            ->useTube($channel)
            ->put(
                $payload,
                $priority ?? PheanstalkInterface::DEFAULT_PRIORITY,
                $delay ?? PheanstalkInterface::DEFAULT_DELAY
            );

        return new Job($job->getId(), $job->getData());
    }

    public function watch(string ...$channels): QueueInterface
    {
        foreach ($channels as $channel) {
            $this->client->watch($channel);
        }
        $this->client->ignore('default');
        return $this;
    }

    public function reserve(): Job
    {
        $job = $this->client->reserve();

        return new Job($job->getId(), $job->getData());
    }

    public function bury(Job $job): void
    {
        $this->client->bury(new \Pheanstalk\Job($job->getId(), $job->getData()));
    }

    public function delete(Job $job): void
    {
        $this->client->delete(new \Pheanstalk\Job($job->getId(), $job->getData()));
    }

    public function release(Job $job, ?int $priority = null, int $delay = null): void
    {
        $this->client->release(
            new \Pheanstalk\Job($job->getId(), $job->getData()),
            $priority ?? PheanstalkInterface::DEFAULT_PRIORITY,
            $delay ?? PheanstalkInterface::DEFAULT_DELAY
        );
    }
}