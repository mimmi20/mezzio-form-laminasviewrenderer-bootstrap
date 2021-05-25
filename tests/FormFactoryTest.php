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
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollectionInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormFactory;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class FormFactoryTest extends TestCase
{
    private FormFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormFactory();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocation(): void
    {
        $formCollection = $this->createMock(FormCollectionInterface::class);
        $formRow        = $this->createMock(FormRowInterface::class);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::never())
            ->method('has');
        $helperPluginManager->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([FormCollectionInterface::class], [FormRowInterface::class])
            ->willReturnOnConsecutiveCalls($formCollection, $formRow);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(Form::class, $helper);
    }
}
