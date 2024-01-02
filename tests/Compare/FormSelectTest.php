<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Factory;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelect;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function get_debug_type;
use function sprintf;
use function trim;

final class FormSelectTest extends AbstractTestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws ContainerExceptionInterface
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRender(): void
    {
        $form = (new Factory())->createForm(require '_files/config/select.config.php');

        $expected = $this->getExpected('form/select.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $escapeHtml = $plugin->get(EscapeHtml::class);
        $hidden     = $plugin->get(FormHiddenInterface::class);

        assert(
            $escapeHtml instanceof EscapeHtml,
            sprintf(
                '$escapeHtml should be an Instance of %s, but was %s',
                EscapeHtml::class,
                get_debug_type($escapeHtml),
            ),
        );
        assert($hidden instanceof FormHiddenInterface);

        $helper = new FormSelect($escapeHtml, $hidden, null);

        self::assertSame($expected, trim($helper->render($form->get('inputState'))));
    }
}
