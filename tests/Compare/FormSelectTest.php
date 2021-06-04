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

namespace MezzioTest\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\Form\Exception\DomainException;
use Laminas\Form\Factory;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelect;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function trim;

final class FormSelectTest extends AbstractTest
{
    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws ContainerExceptionInterface
     *
     * @coversNothing
     */
    public function testRender(): void
    {
        $form = (new Factory())->createForm(require '_files/config/select.config.php');

        $expected = $this->getExpected('form/select.html');

        $helper = new FormSelect(
            $this->serviceManager->get(HelperPluginManager::class)->get(EscapeHtml::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormHiddenInterface::class),
            null
        );

        self::assertSame($expected, trim($helper->render($form->get('inputState'))));
    }
}
