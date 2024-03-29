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

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper;

use AssertionError;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\HelperInterface;
use Laminas\View\HelperPluginManager;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrorsInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRow;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class FormRowFactoryTest extends TestCase
{
    private FormRowFactory $factory;

    /** @throws void */
    protected function setUp(): void
    {
        $this->factory = new FormRowFactory();
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocationWithTranslator(): void
    {
        $formElement       = $this->createMock(FormElementInterface::class);
        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $escapeHtml        = $this->createMock(EscapeHtml::class);
        $htmlElement       = $this->createMock(HtmlElementInterface::class);
        $renderer          = $this->createMock(PartialRendererInterface::class);
        $translatePlugin   = $this->createMock(Translate::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);
        $matcher = self::exactly(4);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $translatePlugin, $formElement, $formElementErrors, $escapeHtml): HelperInterface | FormElementInterface | FormElementErrorsInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(Translate::class, $name),
                        2 => self::assertSame(FormElementInterface::class, $name),
                        3 => self::assertSame(FormElementErrorsInterface::class, $name),
                        default => self::assertSame(EscapeHtml::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $translatePlugin,
                        2 => $formElement,
                        3 => $formElementErrors,
                        default => $escapeHtml,
                    };
                },
            );

        $container = $this->createMock(ContainerInterface::class);
        $matcher   = self::exactly(3);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $id) use ($matcher, $helperPluginManager, $htmlElement, $renderer): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(HelperPluginManager::class, $id),
                        2 => self::assertSame(HtmlElementInterface::class, $id),
                        default => self::assertSame(PartialRendererInterface::class, $id),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $helperPluginManager,
                        2 => $htmlElement,
                        default => $renderer,
                    };
                },
            );

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormRow::class, $helper);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocationWithoutTranslator(): void
    {
        $formElement       = $this->createMock(FormElementInterface::class);
        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $escapeHtml        = $this->createMock(EscapeHtml::class);
        $htmlElement       = $this->createMock(HtmlElementInterface::class);
        $renderer          = $this->createMock(PartialRendererInterface::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);
        $matcher = self::exactly(3);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $formElement, $formElementErrors, $escapeHtml): HelperInterface | FormElementInterface | FormElementErrorsInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(FormElementInterface::class, $name),
                        2 => self::assertSame(FormElementErrorsInterface::class, $name),
                        default => self::assertSame(EscapeHtml::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $formElement,
                        2 => $formElementErrors,
                        default => $escapeHtml,
                    };
                },
            );

        $container = $this->createMock(ContainerInterface::class);
        $matcher   = self::exactly(3);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $id) use ($matcher, $helperPluginManager, $htmlElement, $renderer): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(HelperPluginManager::class, $id),
                        2 => self::assertSame(HtmlElementInterface::class, $id),
                        default => self::assertSame(PartialRendererInterface::class, $id),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $helperPluginManager,
                        2 => $htmlElement,
                        default => $renderer,
                    };
                },
            );

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormRow::class, $helper);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocationWithAssertionError(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn(true);

        assert($container instanceof ContainerInterface);

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage(
            '$plugin should be an Instance of Laminas\View\HelperPluginManager, but was bool',
        );

        ($this->factory)($container);
    }
}
