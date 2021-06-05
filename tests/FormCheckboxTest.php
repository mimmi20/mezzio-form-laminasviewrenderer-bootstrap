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
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabelInterface;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function sprintf;

use const PHP_EOL;

final class FormCheckboxTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     */
    public function testSetWrongLabelPosition(): void
    {
        $labelPosition = 'abc';

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

        $formLabel = $this->getMockBuilder(FormLabelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCheckbox($escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $htmlElement, null);

        $this->expectException(\Laminas\Form\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s expects either %s::LABEL_APPEND or %s::LABEL_PREPEND; received "%s"',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\LabelPositionTrait::setLabelPosition',
                BaseFormRow::class,
                BaseFormRow::class,
                $labelPosition
            )
        );
        $this->expectExceptionCode(0);

        $helper->setLabelPosition($labelPosition);
    }

    /**
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function testSetGetLabelPosition(): void
    {
        $labelPosition = BaseFormRow::LABEL_PREPEND;

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

        $formLabel = $this->getMockBuilder(FormLabelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCheckbox($escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $htmlElement, null);

        $helper->setLabelPosition($labelPosition);

        self::assertSame($labelPosition, $helper->getLabelPosition());
    }

    /**
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderWithWrongElement(): void
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

        $formLabel = $this->getMockBuilder(FormLabelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCheckbox($escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $htmlElement, null);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::never())
            ->method('getName');

        $this->expectException(\Laminas\Form\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element is of type %s',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox::render',
                Checkbox::class
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
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

        $formLabel = $this->getMockBuilder(FormLabelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCheckbox($escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $htmlElement, null);

        $element = $this->getMockBuilder(Checkbox::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox::render'
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderInlineForm(): void
    {
        $name         = 'chkbox';
        $id           = 'chck-id';
        $label        = 'test-label';
        $escapedLabel = 'escaped-label';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($label)
            ->willReturn($escapedLabel);

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

        $expected = '<input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'div',
                ['class' => ['form-check', 'form-check-inline']],
                PHP_EOL . '<label for="chck-id">' . PHP_EOL . '    <input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">' . PHP_EOL . '    <span>escaped-label</span>' . PHP_EOL . '</label>' . PHP_EOL . PHP_EOL
            )
            ->willReturn($expected);

        $formLabel = $this->getMockBuilder(FormLabelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(['class' => 'form-check-label abc', 'for' => $id])
            ->willReturn(
                sprintf('<label for="%s">', $id)
            );
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn('</label>');

        $helper = new FormCheckbox($escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $htmlElement, null);

        $element = $this->getMockBuilder(Checkbox::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getOption')
            ->with('layout')
            ->willReturn(Form::LAYOUT_INLINE);
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn(['class' => 'abc']);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject(['class' => 'xyz']));
        $element->expects(self::once())
            ->method('isChecked')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelOption')
            ->withConsecutive(['disable_html_escape'], ['always_wrap'])
            ->willReturnOnConsecutiveCalls(false, true);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderVerticalFormWithTranslator(): void
    {
        $name                  = 'chkbox';
        $id                    = 'chck-id';
        $label                 = 'test-label';
        $textDomain            = 'text-domain';
        $tranlatedLabel        = 'test-label-translated';
        $escapedTranlatedLabel = 'test-label-translated-escaped';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($tranlatedLabel)
            ->willReturn($escapedTranlatedLabel);

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

        $expected = '<input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">';

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'div',
                ['class' => ['form-check']],
                PHP_EOL . '<label for="chck-id">' . PHP_EOL . '    <span>test-label-translated-escaped</span>' . PHP_EOL . '    <input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">' . PHP_EOL . '</label>' . PHP_EOL . PHP_EOL
            )
            ->willReturn($expected);

        $formLabel = $this->getMockBuilder(FormLabelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(['class' => 'form-check-label abc', 'for' => $id])
            ->willReturn(
                sprintf('<label for="%s">', $id)
            );
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn('</label>');

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain)
            ->willReturn($tranlatedLabel);

        $helper = new FormCheckbox($escapeHtml, $escapeHtmlAttr, $doctype, $formLabel, $htmlElement, $translator);

        $helper->setTranslatorTextDomain($textDomain);
        $helper->setLabelPosition(BaseFormRow::LABEL_PREPEND);

        $element = $this->getMockBuilder(Checkbox::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getOption')
            ->with('layout')
            ->willReturn(Form::LAYOUT_VERTICAL);
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn(['class' => 'abc']);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject(['class' => 'xyz']));
        $element->expects(self::once())
            ->method('isChecked')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelOption')
            ->withConsecutive(['disable_html_escape'], ['always_wrap'])
            ->willReturnOnConsecutiveCalls(false, true);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);

        self::assertSame($expected, $helper->render($element));
    }
}
