<?php declare(strict_types=1);

namespace Circli\Extensions\Queue;

use Circli\Extensions\Queue\Control\StatsCollection;
use Circli\Extensions\Queue\Exception\DeleteJobException;
use Circli\Extensions\Queue\Exception\EventNotFound;
use Circli\Extensions\Queue\Exception\ReleaseJobException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Log\NullLogger;

class AsyncEventListener
{
    public const COMMAND_CHANNEL = 'circli.queue.command.%d';

    /** @var QueueInterface */
    private $client;
    /** @var EventDispatcherInterface */
    private $dispatcher;
    /** @var NullLogger */
    private $logger;
    /** @var StatsCollection */
    private $stats;
    /** @var int */
    private $ttl;

    public function __construct(
        QueueInterface $client,
        StatsCollection $statsCollection,
        EventDispatcherInterface $dispatcher
    ) {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
        $this->logger = new NullLogger();
        $this->stats = $statsCollection;
        $this->ttl = time() + 3600 + random_int(100, 1800);
    }

    public function setTtl(int $ttl)
    {
        $this->ttl = $ttl;
    }

    public function listen(string $channel): void
    {
        $this->logger->info('Start listener: ' . $channel, [
            'pid' => getmypid(),
        ]);

        $this->stats->setTitle($channel);

        if ($this->ttl !== -1) {
            error_log('Listener will exit at: ' . date('c', $this->ttl));
        }

        $this->client->watch($channel, sprintf(self::COMMAND_CHANNEL, getmypid()));

        while (true) {
            $this->logger->debug('Waiting on job');
            $job = $this->client->reserve();
            $this->stats->increment(StatsCollection::TOTAL);
            $this->logger->debug('Received job', [
                'id' => $job->getId(),
            ]);
            $data = $job->getParsedData();
            if (!$data) {
                $this->logger->error('Empty event', [
                    'job' => $job,
                ]);
                $this->stats->increment(StatsCollection::ERROR);
                $this->client->bury($job);
                continue;
            }

            try {
                $eventCls = $data['event'];
                $event = new $eventCls(...$data['args']);
                $this->dispatcher->dispatch($event);
                $this->logger->info('Job completed');
                $this->stats->increment(StatsCollection::SUCCESS);
                $this->client->delete($job);

                if ($event instanceof StoppableEventInterface) {
                    $this->logger->warning('Event was stopped. Exiting ...', [
                        'job' => $job,
                    ]);
                    break;
                }
            }
            catch (EventNotFound $e) {
                $this->stats->increment(StatsCollection::ERROR);
                $this->logger->warning('Event not found', [
                    'job' => $job,
                ]);
                $this->client->delete($job);
            }
            catch (DeleteJobException $e) {
                $this->stats->increment(StatsCollection::ERROR);
                $this->client->delete($job);
            }
            catch (ReleaseJobException $e) {
                $this->stats->increment(StatsCollection::ERROR);
                $this->client->release($job, $e->getPriority(), $e->getDelay());
            }
            catch (\Throwable $e) {
                $this->stats->increment(StatsCollection::ERROR);
                $this->logger->warning('Unknown error when executing job', [
                    'job' => $job,
                    'type' => get_class($e),
                    'exception' => $e,
                ]);
                // don't rerun same job directly wait 30 seconds
                $this->client->release($job, null, 30);
            }

            if ($this->ttl !== -1 && time() > $this->ttl) {
                error_log('Exit command');
                break;
            }
        }

        $this->logger->info('End listener: ' . $channel, [
            'pid' => getmypid(),
        ]);
    }
}
