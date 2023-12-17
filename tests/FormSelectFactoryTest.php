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
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelect;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelectFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class FormSelectFactoryTest extends TestCase
{
    private FormSelectFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormSelectFactory();
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testInvocationWithTranslator(): void
    {
        $escapeHtml      = $this->createMock(EscapeHtml::class);
        $formHidden      = $this->createMock(FormHiddenInterface::class);
        $translatePlugin = $this->createMock(Translate::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);
        $matcher = self::exactly(3);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function (string $name, ?array $options = null) use ($matcher, $translatePlugin, $escapeHtml, $formHidden): HelperInterface|FormHiddenInterface
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(Translate::class, $name),
                        2 => self::assertSame(EscapeHtml::class, $name),
                        default => self::assertSame(FormHiddenInterface::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $translatePlugin,
                        2 => $escapeHtml,
                        default => $formHidden,
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

        self::assertInstanceOf(FormSelect::class, $helper);
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testInvocationWithoutTranslator(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $formHidden = $this->createMock(FormHiddenInterface::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);
        $matcher = self::exactly(2);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                function (string $name, ?array $options = null) use ($matcher, $escapeHtml, $formHidden): HelperInterface|FormHiddenInterface
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(EscapeHtml::class, $name),
                        default => self::assertSame(FormHiddenInterface::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $escapeHtml,
                        default => $formHidden,
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

        self::assertInstanceOf(FormSelect::class, $helper);
    }
}
