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

namespace MezzioTest\BootstrapForm\LaminasView\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrorsInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRow;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class FormRowFactoryTest extends TestCase
{
    private FormRowFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormRowFactory();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithTranslator(): void
    {
        $formElement       = $this->createMock(FormElementInterface::class);
        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $escapeHtml        = $this->createMock(EscapeHtml::class);
        $htmlElement       = $this->createMock(HtmlElementInterface::class);
        $renderer          = $this->createMock(PartialRendererInterface::class);
        $translatePlugin   = $this->createMock(Translate::class);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);
        $helperPluginManager->expects(self::exactly(4))
            ->method('get')
            ->withConsecutive([Translate::class], [FormElementInterface::class], [FormElementErrorsInterface::class], [EscapeHtml::class])
            ->willReturnOnConsecutiveCalls($translatePlugin, $formElement, $formElementErrors, $escapeHtml);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive([HelperPluginManager::class], [HtmlElementInterface::class], [PartialRendererInterface::class])
            ->willReturnOnConsecutiveCalls($helperPluginManager, $htmlElement, $renderer);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormRow::class, $helper);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutTranslator(): void
    {
        $formElement       = $this->createMock(FormElementInterface::class);
        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $escapeHtml        = $this->createMock(EscapeHtml::class);
        $htmlElement       = $this->createMock(HtmlElementInterface::class);
        $renderer          = $this->createMock(PartialRendererInterface::class);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);
        $helperPluginManager->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive([FormElementInterface::class], [FormElementErrorsInterface::class], [EscapeHtml::class])
            ->willReturnOnConsecutiveCalls($formElement, $formElementErrors, $escapeHtml);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive([HelperPluginManager::class], [HtmlElementInterface::class], [PartialRendererInterface::class])
            ->willReturnOnConsecutiveCalls($helperPluginManager, $htmlElement, $renderer);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormRow::class, $helper);
    }
}
