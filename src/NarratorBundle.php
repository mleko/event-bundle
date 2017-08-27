<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NarratorBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new DependencyInjection\Compiler\ListenerPass());
    }

}
