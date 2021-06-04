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

use ArrayObject;
use Laminas\Form\Element\Button;
use Laminas\Form\Exception\DomainException;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormImage;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function sprintf;

final class FormImageTest extends TestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithoutSource(): void
    {
        $element = $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('src')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('getOption');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->getMockBuilder(Doctype::class)
            ->disableOriginalConstructor()
            ->getMock();
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $helper = new FormImage($escapeHtml, $escapeHtmlAttr, $doctype);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned src; none discovered',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormImage::render'
            )
        );
        $this->expectExceptionCode(0);
        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithoutName(): void
    {
        $src = 'http://test-uri.test';

        $element = $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('src')
            ->willReturn($src);
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('getOption');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->getMockBuilder(Doctype::class)
            ->disableOriginalConstructor()
            ->getMock();
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $helper = new FormImage($escapeHtml, $escapeHtmlAttr, $doctype);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormInput::render'
            )
        );
        $this->expectExceptionCode(0);
        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderHtml(): void
    {
        $src   = 'http://test-uri.test';
        $name  = 'test-name';
        $class = 'test-class';
        $value = 'test-value';

        $expected = sprintf('<input class="form-control&#x20;%s" name="%s" type="image">', $class, $name);

        $element = $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(['class' => $class]);
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('src')
            ->willReturn($src);
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('getOption');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->getMockBuilder(Doctype::class)
            ->disableOriginalConstructor()
            ->getMock();
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(false);

        $helper = new FormImage($escapeHtml, $escapeHtmlAttr, $doctype);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderXHtml(): void
    {
        $src   = 'http://test-uri.test';
        $name  = 'test-name';
        $class = 'test-class';
        $value = 'test-value';

        $expected = sprintf('<input class="form-control&#x20;%s" name="%s" type="image"/>', $class, $name);

        $element = $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(['class' => $class]);
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('src')
            ->willReturn($src);
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('getOption');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->getMockBuilder(Doctype::class)
            ->disableOriginalConstructor()
            ->getMock();
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $helper = new FormImage($escapeHtml, $escapeHtmlAttr, $doctype);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderReadonlyXHtml(): void
    {
        $src   = 'http://test-uri.test';
        $name  = 'test-name';
        $class = 'test-class';
        $value = 'test-value';

        $expected = sprintf('<input class="form-control-plaintext" name="%s" type="image"/>', $name);

        $element = $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject(['class' => $class, 'readonly' => true]));
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('src')
            ->willReturn($src);
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::once())
            ->method('getOption')
            ->with('plain')
            ->willReturn(true);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->getMockBuilder(Doctype::class)
            ->disableOriginalConstructor()
            ->getMock();
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $helper = new FormImage($escapeHtml, $escapeHtmlAttr, $doctype);

        self::assertSame($expected, $helper->render($element));
    }
}
