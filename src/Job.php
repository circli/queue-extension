<?php declare(strict_types=1);

namespace Circli\Extensions\Queue;

class Job
{
    /** @var int */
    private $id;
    /** @var string */
    private $data;

    public function __construct(int $id, string $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getParsedData()
    {
        return json_decode($this->data, true);
    }
}
