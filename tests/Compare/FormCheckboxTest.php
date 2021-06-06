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
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabelInterface;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use Mezzio\LaminasViewHelper\Helper\PluginManager as LvhPluginManager;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function trim;

final class FormCheckboxTest extends AbstractTest
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
        $form = (new Factory())->createForm(require '_files/config/checkbox.config.php');

        $expected = $this->getExpected('form/checkbox.html');

        $helper = new FormCheckbox(
            $this->serviceManager->get(HelperPluginManager::class)->get(EscapeHtml::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(EscapeHtmlAttr::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(Doctype::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormLabelInterface::class),
            $this->serviceManager->get(LvhPluginManager::class)->get(HtmlElementInterface::class),
            $this->serviceManager->get(HelperPluginManager::class)->get(FormHiddenInterface::class),
            null
        );

        self::assertSame($expected, trim($helper->render($form->get('gridCheck1'))));
    }
}
