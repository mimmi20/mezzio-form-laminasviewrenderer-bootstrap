<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper;

use AssertionError;
use Laminas\Form\Exception\ExtensionNotLoadedException;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelectFactory;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelectInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class FormDateSelectFactoryTest extends TestCase
{
    private FormDateSelectFactory $factory;

    /** @throws void */
    protected function setUp(): void
    {
        $this->factory = new FormDateSelectFactory();
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ExtensionNotLoadedException
     */
    public function testInvocation(): void
    {
        $selectHelper = $this->createMock(FormSelectInterface::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::never())
            ->method('has');
        $helperPluginManager->expects(self::once())
            ->method('get')
            ->with(FormSelectInterface::class)
            ->willReturn($selectHelper);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormDateSelect::class, $helper);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ExtensionNotLoadedException
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
