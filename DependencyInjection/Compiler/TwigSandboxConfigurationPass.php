<?php

namespace DWietor\Bundle\PmanagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigSandboxConfigurationPass implements CompilerPassInterface
{
    const IBNAB_PMANAGER_SANDBOX_SECURITY_POLICY_SERVICE_KEY = 'd_wietor_pmanager.twig.security_policy';
    const IBNAB_TEMPLATE_RENDERER_SERVICE_KEY                = 'd_wietor_pmanager.pdftemplate_renderer';
    const CONFIG_EXTENSION_SERVICE_KEY                       = 'oro_config.twig.config_extension';

    const FORMATTER_EXTENSION_SERVICE_KEY = 'oro_ui.twig.extension.formatter';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(self::IBNAB_PMANAGER_SANDBOX_SECURITY_POLICY_SERVICE_KEY)
            && $container->hasDefinition(self::IBNAB_TEMPLATE_RENDERER_SERVICE_KEY)
        ) {
            // register 'oro_config_value' function
            $securityPolicyDef = $container->getDefinition(self::IBNAB_PMANAGER_SANDBOX_SECURITY_POLICY_SERVICE_KEY);
            $rendererDef       = $container->getDefinition(self::IBNAB_TEMPLATE_RENDERER_SERVICE_KEY);

            if ($container->hasDefinition(self::CONFIG_EXTENSION_SERVICE_KEY)) {
                $functions = $securityPolicyDef->getArgument(4);
                $functions = array_merge($functions, ['oro_config_value']);
                $securityPolicyDef->replaceArgument(4, $functions);
                // register an twig extension implements this function
                $rendererDef->addMethodCall('addExtension', [new Reference(self::CONFIG_EXTENSION_SERVICE_KEY)]);
            }

            if ($container->hasDefinition(self::FORMATTER_EXTENSION_SERVICE_KEY)) {
                $filters = $securityPolicyDef->getArgument(1);
                $filters = array_merge($filters, ['oro_format']);
                $securityPolicyDef->replaceArgument(1, $filters);
                // register an twig extension implements this function
                $rendererDef->addMethodCall('addExtension', [new Reference(self::FORMATTER_EXTENSION_SERVICE_KEY)]);
            }
        }
    }
}
