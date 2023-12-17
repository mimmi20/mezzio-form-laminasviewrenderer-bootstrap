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
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Helper\HelperInterface;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSearch;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSearchFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;

final class FormSearchFactoryTest extends TestCase
{
    private FormSearchFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormSearchFactory();
    }

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testInvocation(): void
    {
        $escapeHtml     = $this->createMock(EscapeHtml::class);
        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $doctype        = $this->createMock(Doctype::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::never())
            ->method('has');
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

        self::assertInstanceOf(FormSearch::class, $helper);
    }
}
