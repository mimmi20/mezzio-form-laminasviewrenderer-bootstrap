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
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabelInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRadio;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRadioFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class FormRadioFactoryTest extends TestCase
{
    private FormRadioFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormRadioFactory();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithTranslator(): void
    {
        $escapeHtml      = $this->createMock(EscapeHtml::class);
        $escapeHtmlAttr  = $this->createMock(EscapeHtmlAttr::class);
        $doctype         = $this->createMock(Doctype::class);
        $formLabel       = $this->createMock(FormLabelInterface::class);
        $htmlElement     = $this->createMock(HtmlElementInterface::class);
        $translatePlugin = $this->createMock(Translate::class);
        $formHidden      = $this->createMock(FormHiddenInterface::class);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);
        $helperPluginManager->expects(self::exactly(6))
            ->method('get')
            ->withConsecutive([Translate::class], [EscapeHtml::class], [EscapeHtmlAttr::class], [Doctype::class], [FormLabelInterface::class], [FormHiddenInterface::class])
            ->willReturnOnConsecutiveCalls($translatePlugin, $escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $formHidden);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([HelperPluginManager::class], [HtmlElementInterface::class])
            ->willReturnOnConsecutiveCalls($helperPluginManager, $htmlElement);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormRadio::class, $helper);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutTranslator(): void
    {
        $escapeHtml     = $this->createMock(EscapeHtml::class);
        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $doctype        = $this->createMock(Doctype::class);
        $formLabel      = $this->createMock(FormLabelInterface::class);
        $htmlElement    = $this->createMock(HtmlElementInterface::class);
        $formHidden     = $this->createMock(FormHiddenInterface::class);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);
        $helperPluginManager->expects(self::exactly(5))
            ->method('get')
            ->withConsecutive([EscapeHtml::class], [EscapeHtmlAttr::class], [Doctype::class], [FormLabelInterface::class], [FormHiddenInterface::class])
            ->willReturnOnConsecutiveCalls($escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $formHidden);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([HelperPluginManager::class], [HtmlElementInterface::class])
            ->willReturnOnConsecutiveCalls($helperPluginManager, $htmlElement);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormRadio::class, $helper);
    }
}
