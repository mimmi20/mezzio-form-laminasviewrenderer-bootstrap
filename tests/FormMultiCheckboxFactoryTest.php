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

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\View\Helper\HelperInterface;
use Psr\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabelInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormMultiCheckbox;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormMultiCheckboxFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class FormMultiCheckboxFactoryTest extends TestCase
{
    private FormMultiCheckboxFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormMultiCheckboxFactory();
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
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

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);
        $matcher = self::exactly(6);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function(string $name, ?array $options = null) use ($matcher, $escapeHtml, $translatePlugin, $escapeHtmlAttr, $doctype, $formLabel, $formHidden): HelperInterface|FormLabelInterface|FormHiddenInterface
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(Translate::class, $name),
                        2 => self::assertSame(EscapeHtml::class, $name),
                        3 => self::assertSame(EscapeHtmlAttr::class, $name),
                        4 => self::assertSame(Doctype::class, $name),
                        5 => self::assertSame(FormLabelInterface::class, $name),
                        default => self::assertSame(FormHiddenInterface::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $translatePlugin,
                        2 => $escapeHtml,
                        3 => $escapeHtmlAttr,
                        4 => $doctype,
                        5 => $formLabel,
                        default => $formHidden,
                    };
                }
            );

        $container = $this->createMock(ContainerInterface::class);
        $matcher = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function(string $id) use ($matcher, $helperPluginManager, $htmlElement): mixed
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(HelperPluginManager::class, $id),
                        default => self::assertSame(HtmlElementInterface::class, $id),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $helperPluginManager,
                        default => $htmlElement,
                    };
                }
            );

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormMultiCheckbox::class, $helper);
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testInvocationWithoutTranslator(): void
    {
        $escapeHtml     = $this->createMock(EscapeHtml::class);
        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $doctype        = $this->createMock(Doctype::class);
        $formLabel      = $this->createMock(FormLabelInterface::class);
        $htmlElement    = $this->createMock(HtmlElementInterface::class);
        $formHidden     = $this->createMock(FormHiddenInterface::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);
        $matcher = self::exactly(5);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function(string $name, ?array $options = null) use ($matcher, $escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $formHidden): HelperInterface|FormLabelInterface|FormHiddenInterface
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(EscapeHtml::class, $name),
                        2 => self::assertSame(EscapeHtmlAttr::class, $name),
                        3 => self::assertSame(Doctype::class, $name),
                        4 => self::assertSame(FormLabelInterface::class, $name),
                        default => self::assertSame(FormHiddenInterface::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $escapeHtml,
                        2 => $escapeHtmlAttr,
                        3 => $doctype,
                        4 => $formLabel,
                        default => $formHidden,
                    };
                }
            );

        $container = $this->createMock(ContainerInterface::class);
        $matcher = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function(string $id) use ($matcher, $helperPluginManager, $htmlElement): mixed
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(HelperPluginManager::class, $id),
                        default => self::assertSame(HtmlElementInterface::class, $id),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $helperPluginManager,
                        default => $htmlElement,
                    };
                }
            );

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormMultiCheckbox::class, $helper);
    }
}
