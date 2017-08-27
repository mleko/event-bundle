<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


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
