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
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollectionInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormFactory;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class FormFactoryTest extends TestCase
{
    private FormFactory $factory;

    /** @throws void */
    protected function setUp(): void
    {
        $this->factory = new FormFactory();
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocation(): void
    {
        $formCollection = $this->createMock(FormCollectionInterface::class);
        $formRow        = $this->createMock(FormRowInterface::class);

        $helperPluginManager = $this->createMock(HelperPluginManager::class);
        $helperPluginManager->expects(self::never())
            ->method('has');
        $matcher = self::exactly(2);
        $helperPluginManager->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $formCollection, $formRow): FormCollectionInterface | FormRowInterface {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(FormCollectionInterface::class, $name),
                        default => self::assertSame(FormRowInterface::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $formCollection,
                        default => $formRow,
                    };
                },
            );

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(Form::class, $helper);
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
