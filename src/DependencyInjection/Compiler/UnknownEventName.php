<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\DependencyInjection\Compiler;


class UnknownEventName extends \RuntimeException
{

    /**
     * UnknownEventName constructor.
     * @param string $serviceId
     */
    public function __construct($serviceId)
    {
        parent::__construct(sprintf("Unknown event name for listener: `%s`, `narrator.listener` tag don't have an `event` attribute and event name could't be inferred", $serviceId));
    }
}
