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

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\Form\Exception\DomainException;
use Laminas\Form\Factory;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormColor;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function get_debug_type;
use function sprintf;
use function trim;

final class FormColorTest extends AbstractTestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     * @throws ContainerExceptionInterface
     */
    public function testRender(): void
    {
        $form = (new Factory())->createForm(require '_files/config/color.config.php');

        $expected = $this->getExpected('form/color.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $escapeHtml     = $plugin->get(EscapeHtml::class);
        $escapeHtmlAttr = $plugin->get(EscapeHtmlAttr::class);
        $docType        = $plugin->get(Doctype::class);

        assert(
            $escapeHtml instanceof EscapeHtml,
            sprintf(
                '$escapeHtml should be an Instance of %s, but was %s',
                EscapeHtml::class,
                get_debug_type($escapeHtml),
            ),
        );
        assert(
            $escapeHtmlAttr instanceof EscapeHtmlAttr,
            sprintf(
                '$escapeHtmlAttr should be an Instance of %s, but was %s',
                EscapeHtmlAttr::class,
                get_debug_type($escapeHtmlAttr),
            ),
        );
        assert(
            $docType instanceof Doctype,
            sprintf(
                '$docType should be an Instance of %s, but was %s',
                Doctype::class,
                get_debug_type($docType),
            ),
        );

        $helper = new FormColor($escapeHtml, $escapeHtmlAttr, $docType);

        self::assertSame($expected, trim($helper->render($form->get('inputColor'))));
    }
}
