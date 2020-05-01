<?php

namespace DWietor\Bundle\PmanagerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use DWietor\Bundle\PmanagerBundle\DependencyInjection\Compiler\PmanagerVariablesPass;
use DWietor\Bundle\PmanagerBundle\DependencyInjection\Compiler\TwigSandboxConfigurationPass;

class DWietorPmanagerBundle extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new PmanagerVariablesPass());
        $container->addCompilerPass(new TwigSandboxConfigurationPass());

    }


}
