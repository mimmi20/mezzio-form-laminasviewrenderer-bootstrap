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
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormTime;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormTimeFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class FormTimeFactoryTest extends TestCase
{
    private FormTimeFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormTimeFactory();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocation(): void
    {
        $escapeHtml     = $this->createMock(EscapeHtml::class);
        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $doctype        = $this->createMock(Doctype::class);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::never())
            ->method('has');
        $helperPluginManager->expects(self::exactly(3))
            ->method('get')
            ->withConsecutive([EscapeHtml::class], [EscapeHtmlAttr::class], [Doctype::class])
            ->willReturnOnConsecutiveCalls($escapeHtml, $escapeHtmlAttr, $doctype);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(FormTime::class, $helper);
    }
}
