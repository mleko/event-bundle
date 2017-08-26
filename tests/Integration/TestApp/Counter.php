<?php


namespace Mleko\Narrator\Bundle\Tests\Integration\TestApp;


class Counter
{
    private $count = 0;
    private $lastEvent;

    public function handle($event)
    {
        $this->count++;
        $this->lastEvent = $event;
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
