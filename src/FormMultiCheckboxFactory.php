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
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use Mezzio\LaminasViewHelper\Helper\PluginManager as LvhPluginManager;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

final class FormMultiCheckboxFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): FormMultiCheckbox
    {
        $plugin = $container->get(HelperPluginManager::class);
        assert(
            $plugin instanceof HelperPluginManager,
            sprintf(
                '$plugin should be an Instance of %s, but was %s',
                HelperPluginManager::class,
                is_object($plugin) ? get_class($plugin) : gettype($plugin)
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

        return new FormMultiCheckbox(
            $plugin->get(EscapeHtml::class),
            $plugin->get(EscapeHtmlAttr::class),
            $plugin->get(Doctype::class),
            $plugin->get(FormLabelInterface::class),
            $lvhPluginManager->get(HtmlElementInterface::class),
            $translator
        );
    }
}
