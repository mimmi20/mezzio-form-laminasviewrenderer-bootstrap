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

use Psr\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Helper\HelperInterface;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormButton;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormButtonFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class FormButtonFactoryTest extends TestCase
{
    private FormButtonFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormButtonFactory();
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testInvocationWithTranslator(): void
    {
        $translatePlugin = $this->createMock(Translate::class);
        $escapeHtml      = $this->createMock(EscapeHtml::class);
        $escapeHtmlAttr  = $this->createMock(EscapeHtmlAttr::class);
        $doctype         = $this->createMock(Doctype::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);
        $matcher = self::exactly(4);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function(string $name, ?array $options = null) use ($matcher, $translatePlugin, $escapeHtml, $escapeHtmlAttr, $doctype): HelperInterface
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(Translate::class, $name),
                        2 => self::assertSame(EscapeHtml::class, $name),
                        3 => self::assertSame(EscapeHtmlAttr::class, $name),
                        default => self::assertSame(Doctype::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $translatePlugin,
                        2 => $escapeHtml,
                        3 => $escapeHtmlAttr,
                        default => $doctype,
                    };
                }
            );

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormButton::class, $helper);
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

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);
        $matcher = self::exactly(3);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function(string $name, ?array $options = null) use ($matcher, $escapeHtml, $escapeHtmlAttr, $doctype): HelperInterface
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(EscapeHtml::class, $name),
                        2 => self::assertSame(EscapeHtmlAttr::class, $name),
                        default => self::assertSame(Doctype::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $escapeHtml,
                        2 => $escapeHtmlAttr,
                        default => $doctype,
                    };
                }
            );

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormButton::class, $helper);
    }
}
