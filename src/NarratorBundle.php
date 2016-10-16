<?php
/**
 * @package narrator-bundle
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
