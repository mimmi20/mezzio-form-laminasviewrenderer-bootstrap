<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mezzio\BootstrapForm\LaminasView\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\PluginManagerInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;
use Mezzio\LaminasViewHelper\Helper\PluginManager as LvhPluginManager;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

final class FormCheckboxFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): FormCheckbox
    {
        $plugin = $container->get(HelperPluginManager::class);
        assert(
            $plugin instanceof HelperPluginManager,
            sprintf(
                '$plugin should be an Instance of %s, but was %s',
                HelperPluginManager::class,
                get_class($plugin)
            )
        );

        $lvhPluginManager = $container->get(LvhPluginManager::class);
        assert(
            $lvhPluginManager instanceof PluginManagerInterface,
            sprintf(
                '$lvhPluginManager should be an Instance of %s, but was %s',
                LvhPluginManager::class,
                is_object($lvhPluginManager) ? get_class($lvhPluginManager) : gettype($lvhPluginManager)
            )
        );

        $translator = null;

        if ($plugin->has(Translate::class)) {
            $translator = $plugin->get(Translate::class);
        }

        return new FormCheckbox(
            $lvhPluginManager->get(PartialRendererInterface::class),
            $plugin->get(EscapeHtml::class),
            $translator
        );
    }
}
