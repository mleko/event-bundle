<?php


namespace Mleko\Narrator\Bundle\Tests\Integration\TestApp;


class StdCounter
{
    private $count = 0;
    private $lastEvent;

    public function handle(\stdClass $event)
    {
        $this->count++;
        $this->lastEvent = $event;
    }

    public function handleParameterLess()
    {
        $this->count++;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getLastEvent()
    {
        return $this->lastEvent;
    }
}
