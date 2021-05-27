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
use Laminas\Form\Element\File;
use Laminas\Form\Exception\DomainException;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormFile;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function sprintf;

final class FormFileTest extends TestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithoutName(): void
    {
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

        $helper = new FormFile($escapeHtml, $escapeHtmlAttr, $doctype);

        $element = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormFile::render'
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testRenderWithName1(): void
    {
        $name     = 'name';
        $expexted = '<input class="abc" multiple="multiple" type="file" name="name&#x5B;&#x5D;">';

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

        $helper = new FormFile($escapeHtml, $escapeHtmlAttr, $doctype);

        $element = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject(['class' => 'abc', 'multiple' => true]));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn(['value' => 'xyz']);

        self::assertSame($expexted, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testRenderWithName2(): void
    {
        $name     = 'name';
        $expexted = '<input class="abc" type="file" name="name">';

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

        $helper = new FormFile($escapeHtml, $escapeHtmlAttr, $doctype);

        $element = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject(['class' => 'abc']));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn('efg');

        self::assertSame($expexted, $helper->render($element));
    }
}
