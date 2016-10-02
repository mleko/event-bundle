<?php

namespace Mleko\Event\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MlekoEventBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new \Mleko\Event\Bundle\DependencyInjection\Compiler\ListenerPass());
    }

}
