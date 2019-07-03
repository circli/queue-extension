<?php declare(strict_types=1);

namespace Circli\Extensions\Queue\Control;

class StatsCollection implements \JsonSerializable
{
    public const TOTAL = 'total';
    public const SUCCESS = 'success';
    public const ERROR = 'error';

    private $collection = [];
    private $title = '';

    public function increment($field): void
    {
        if (!isset($this->collection[$field])) {
            $this->collection[$field] = 0;
        }
        $this->collection[$field]++;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'collection' => $this->collection,
            'title' => $this->title,
        ];
    }
}
