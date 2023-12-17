<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function get_debug_type;
use function sprintf;

final class FormRowFactory
{
    /** @throws ContainerExceptionInterface */
    public function __invoke(ContainerInterface $container): FormRow
    {
        $plugin = $container->get(HelperPluginManager::class);
        assert(
            $plugin instanceof HelperPluginManager,
            sprintf(
                '$plugin should be an Instance of %s, but was %s',
                HelperPluginManager::class,
                get_debug_type($plugin),
            ),
        );

        $translator = null;

        if ($plugin->has(Translate::class)) {
            $translator = $plugin->get(Translate::class);

            assert($translator instanceof Translate);
        }

        $formElement      = $plugin->get(FormElementInterface::class);
        $formElementError = $plugin->get(FormElementErrorsInterface::class);
        $htmlElement      = $container->get(HtmlElementInterface::class);
        $escapeHtml       = $plugin->get(EscapeHtml::class);
        $renderer         = $container->get(PartialRendererInterface::class);

        assert($formElement instanceof FormElementInterface);
        assert($formElementError instanceof FormElementErrorsInterface);
        assert($htmlElement instanceof HtmlElementInterface);
        assert(
            $escapeHtml instanceof EscapeHtml,
            sprintf(
                '$escapeHtml should be an Instance of %s, but was %s',
                EscapeHtml::class,
                get_debug_type($escapeHtml),
            ),
        );
        assert($renderer instanceof PartialRendererInterface);

        return new FormRow(
            $formElement,
            $formElementError,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );
    }
}
