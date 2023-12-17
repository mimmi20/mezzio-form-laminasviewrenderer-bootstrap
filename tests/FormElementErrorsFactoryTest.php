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
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrors;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrorsFactory;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class FormElementErrorsFactoryTest extends TestCase
{
    private FormElementErrorsFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormElementErrorsFactory();
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testInvocationWithTranslator(): void
    {
        $escapeHtml      = $this->createMock(EscapeHtml::class);
        $htmlElement     = $this->createMock(HtmlElementInterface::class);
        $translatePlugin = $this->createMock(Translate::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);
        $matcher = self::exactly(2);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function(string $name, ?array $options = null) use ($matcher, $translatePlugin, $escapeHtml): HelperInterface
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(Translate::class, $name),
                        default => self::assertSame(EscapeHtml::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $translatePlugin,
                        default => $escapeHtml,
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

        self::assertInstanceOf(FormElementErrors::class, $helper);
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testInvocationWithoutTranslator(): void
    {
        $escapeHtml  = $this->createMock(EscapeHtml::class);
        $htmlElement = $this->createMock(HtmlElementInterface::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);
        $helperPluginManager->expects(self::once())
            ->method('get')
            ->with(EscapeHtml::class)
            ->willReturn($escapeHtml);

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

        self::assertInstanceOf(FormElementErrors::class, $helper);
    }
}
