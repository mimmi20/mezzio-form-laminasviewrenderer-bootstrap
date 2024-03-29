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

use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Text;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Helper\Escaper\AbstractHelper;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabelInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRadio;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;
use function sprintf;

use const PHP_EOL;

final class FormRadioTest extends TestCase
{
    /** @throws Exception */
    public function testSetGetLabelAttributes(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        self::assertSame([], $helper->getLabelAttributes());

        $labelAttributes = ['class' => 'test-class', 'aria-label' => 'test'];

        self::assertSame($helper, $helper->setLabelAttributes($labelAttributes));
        self::assertSame($labelAttributes, $helper->getLabelAttributes());
    }

    /** @throws Exception */
    public function testSetGetSeperator(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        self::assertSame('', $helper->getSeparator());

        $seperator = '::test::';

        self::assertSame($helper, $helper->setSeparator($seperator));
        self::assertSame($seperator, $helper->getSeparator());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testSetWrongLabelPosition(): void
    {
        $labelPosition = 'abc';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s expects either %s::LABEL_APPEND or %s::LABEL_PREPEND; received "%s"',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\LabelPositionTrait::setLabelPosition',
                BaseFormRow::class,
                BaseFormRow::class,
                $labelPosition,
            ),
        );
        $this->expectExceptionCode(0);

        $helper->setLabelPosition($labelPosition);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testSetGetLabelPosition(): void
    {
        $labelPosition = BaseFormRow::LABEL_PREPEND;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        self::assertSame(BaseFormRow::LABEL_APPEND, $helper->getLabelPosition());

        $helper->setLabelPosition($labelPosition);

        self::assertSame($labelPosition, $helper->getLabelPosition());
    }

    /** @throws Exception */
    public function testSetGetUseHiddenElement(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        self::assertFalse($helper->getUseHiddenElement());

        $helper->setUseHiddenElement(true);

        self::assertTrue($helper->getUseHiddenElement());
    }

    /** @throws Exception */
    public function testSetGetUncheckedValue(): void
    {
        $uncheckedValue = '0';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        self::assertSame('', $helper->getUncheckedValue());

        $helper->setUncheckedValue($uncheckedValue);

        self::assertSame($uncheckedValue, $helper->getUncheckedValue());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderWithWrongElement(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Text::class);
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element is of type %s',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\AbstractFormMultiCheckbox::render',
                MultiCheckboxElement::class,
            ),
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderWithoutName(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('getValueOptions');
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('useHiddenElement');
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::never())
            ->method('getUncheckedValue');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRadio::getName',
            ),
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderWithoutId(): void
    {
        $name            = 'test-name';
        $value1          = 'xyz';
        $value2          = 'def';
        $value2Escaped   = 'def-escaped';
        $value3          = 'abc';
        $class           = 'test-class';
        $ariaLabel       = 'test';
        $labelClass      = 'xyz';
        $valueOptions    = [$value3 => $value2];
        $attributes      = ['class' => $class, 'aria-label' => $ariaLabel];
        $labelAttributes = ['class' => $labelClass];
        $labelStart      = '<label>';
        $labelEnd        = '</label>';
        $renderedField   = PHP_EOL
            . '        ' . $labelStart . PHP_EOL
            . '            ' . sprintf(
                '<input class="form-check-input&#x20;%s" aria-label="%s" name="%s" type="radio" value="%s">',
                $class,
                $ariaLabel,
                $name,
                $value3,
            ) . PHP_EOL
            . '            ' . sprintf('<span>%s</span>', $value2Escaped) . PHP_EOL
            . '        ' . $labelEnd . PHP_EOL
            . '    ';
        $expected        = '<div></div>';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($value2)
            ->willReturn($value2Escaped);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(false);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(
                [
                    'class' => sprintf('form-check-label %s', $labelClass),
                ],
            )
            ->willReturn($labelStart);
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', ['class' => 'form-check form-check-inline'], $renderedField)
            ->willReturn($expected);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        self::assertSame('    ' . $expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderWithIdAndNoWarp(): void
    {
        $name            = 'test-name';
        $id              = 'test-id';
        $value1          = 'xyz';
        $value2          = 'def';
        $value2Escaped   = 'def-escaped';
        $value3          = 'abc';
        $class           = 'test-class';
        $ariaLabel       = 'test';
        $labelClass      = 'xyz';
        $valueOptions    = [$value3 => $value2];
        $attributes      = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id];
        $labelAttributes = ['class' => $labelClass];
        $labelStart      = '<label>';
        $labelEnd        = '</label>';
        $expected        = '<div></div>';
        $renderedField   = PHP_EOL
        . '        ' . sprintf(
            '<input class="form-check-input&#x20;%s" aria-label="%s" id="%s" name="%s" type="radio" value="%s">',
            $class,
            $ariaLabel,
            $id,
            $name,
            $value3,
        ) . PHP_EOL
        . '        ' . $labelStart . $value2Escaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap            = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($value2)
            ->willReturn($value2Escaped);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(false);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(
                [
                    'class' => sprintf('form-check-label %s', $labelClass),
                    'for' => $id,
                ],
            )
            ->willReturn($labelStart);
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', ['class' => 'form-check form-check-inline'], $renderedField)
            ->willReturn($expected);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | array | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        3 => ['class' => true],
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $wrap): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('label_position', $key),
                        2 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => BaseFormRow::LABEL_APPEND,
                        2 => false,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        self::assertSame('    ' . $expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderPrependWithoutId(): void
    {
        $name            = 'test-name';
        $value1          = 'xyz';
        $value2          = 'def';
        $value2Escaped   = 'def-escaped';
        $value3          = 'abc';
        $class           = 'test-class';
        $ariaLabel       = 'test';
        $labelClass      = 'xyz';
        $valueOptions    = [$value3 => $value2];
        $attributes      = ['class' => $class, 'aria-label' => $ariaLabel];
        $labelAttributes = ['class' => $labelClass];
        $labelStart      = '<label>';
        $labelEnd        = '</label>';
        $renderedField   = PHP_EOL
            . '        ' . $labelStart . PHP_EOL
            . '            ' . sprintf('<span>%s</span>', $value2Escaped) . PHP_EOL
            . '            ' . sprintf(
                '<input class="form-check-input&#x20;%s" aria-label="%s" name="%s" type="radio" value="%s">',
                $class,
                $ariaLabel,
                $name,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelEnd . PHP_EOL
            . '    ';
        $expected        = '<div></div>';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($value2)
            ->willReturn($value2Escaped);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(false);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(
                [
                    'class' => sprintf('form-check-label %s', $labelClass),
                ],
            )
            ->willReturn($labelStart);
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', ['class' => 'form-check form-check-inline'], $renderedField)
            ->willReturn($expected);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | array | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        3 => ['class' => 1],
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        $helper->setLabelPosition(BaseFormRow::LABEL_PREPEND);

        self::assertSame('    ' . $expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderPrependWithIdAndNoWarp(): void
    {
        $name            = 'test-name';
        $id              = 'test-id';
        $value1          = 'xyz';
        $value2          = 'def';
        $value2Escaped   = 'def-escaped';
        $value3          = 'abc';
        $class           = 'test-class';
        $ariaLabel       = 'test';
        $labelClass      = 'xyz';
        $valueOptions    = [$value3 => $value2];
        $attributes      = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id];
        $labelAttributes = ['class' => $labelClass];
        $labelStart      = '<label>';
        $labelEnd        = '</label>';
        $expected        = '<div></div>';
        $renderedField   = PHP_EOL
            . '        ' . $labelStart . $value2Escaped . $labelEnd . PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s" aria-label="%s" id="%s" name="%s" type="radio" value="%s">',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . '    ';
        $wrap            = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($value2)
            ->willReturn($value2Escaped);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(false);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(
                [
                    'class' => sprintf('form-check-label %s', $labelClass),
                    'for' => $id,
                ],
            )
            ->willReturn($labelStart);
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', ['class' => 'form-check form-check-inline'], $renderedField)
            ->willReturn($expected);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | array | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        3 => ['class' => 'form-check'],
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $wrap): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('label_position', $key),
                        2 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => BaseFormRow::LABEL_PREPEND,
                        2 => false,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        $helper->setLabelPosition(BaseFormRow::LABEL_PREPEND);

        self::assertSame('    ' . $expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderWithIdAndNoWarpWithoutEscape(): void
    {
        $name            = 'test-name';
        $id              = 'test-id';
        $value1          = 'xyz';
        $value2          = 'def';
        $value3          = 'abc';
        $class           = 'test-class';
        $ariaLabel       = 'test';
        $labelClass      = 'xyz';
        $valueOptions    = [$value3 => $value2];
        $attributes      = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id];
        $labelAttributes = ['class' => $labelClass, 'test'];
        $labelStart      = '<label>';
        $labelEnd        = '</label>';
        $expected        = '<div></div>';
        $renderedField   = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s" aria-label="%s" id="%s" name="%s" type="radio" value="%s">',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2 . $labelEnd . PHP_EOL
            . '    ';
        $wrap            = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(false);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(
                [
                    'class' => sprintf('form-check-label %s', $labelClass),
                    'for' => $id,
                ],
            )
            ->willReturn($labelStart);
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', ['class' => 'form-check form-check-inline'], $renderedField)
            ->willReturn($expected);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $wrap): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('label_position', $key),
                        2 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => BaseFormRow::LABEL_APPEND,
                        2 => true,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);

        self::assertSame('    ' . $expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderXhtmlWithTranslator(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $valueOptions            = [$value3 => $value2];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $textDomain              = 'test-domain';
        $renderedField           = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s" aria-label="%s" id="%s" name="%s" type="radio" value="%s"/>',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap                    = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($value2Translated)
            ->willReturn($value2TranslatedEscaped);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(
                [
                    'class' => sprintf('form-check-label %s', $labelClass),
                    'for' => $id,
                ],
            )
            ->willReturn($labelStart);
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', ['class' => 'form-check form-check-inline'], $renderedField)
            ->willReturn($expected);

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($value2, $textDomain)
            ->willReturn($value2Translated);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $wrap): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('label_position', $key),
                        2 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => BaseFormRow::LABEL_APPEND,
                        2 => false,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame('    ' . $expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderMultiOption(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $expectedSummary         = '    <div></div>' . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" name="%s" type="radio" id="%s" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $id,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField2          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField3          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst', $labelClass),
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst3', $labelClass),
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expectedSummary, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderMultiOptionInlineWithHiddenField1(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $uncheckedValue          = '0';
        $expectedSummary         = '    ' . sprintf(
            '<input type="hidden" name="%s" value="%s"/>',
            $name,
            $uncheckedValue,
        ) . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" name="%s" type="radio" id="%s" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $id,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField2          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField3          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst', $labelClass),
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst3', $labelClass),
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | bool | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => false,
                        3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn('');

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);
        $helper->setTranslatorTextDomain($textDomain);
        $helper->setUncheckedValue($uncheckedValue);

        self::assertSame($expectedSummary, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderMultiOptionInlineWithHiddenField2(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $uncheckedValue          = '0';
        $expectedSummary         = '    ' . sprintf(
            '<input type="hidden" name="%s" value="%s"/>',
            $name,
            $uncheckedValue,
        ) . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" name="%s" type="radio" id="%s" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $id,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField2          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField3          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst', $labelClass),
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst3', $labelClass),
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline form-switch'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn($labelAttributes);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | bool | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => true,
                        3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn($uncheckedValue);

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expectedSummary, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderMultiOptionInlineWithHiddenField3(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true, 'id' => 'zero-id'];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $uncheckedValue          = '0';
        $expectedSummary         = '    ' . sprintf(
            '<input type="hidden" name="%s" value="%s"/>',
            $name,
            $uncheckedValue,
        ) . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" id="%s" name="%s" type="radio" value="%s"/>',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField2          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField3          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst', $labelClass),
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst3', $labelClass),
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn([]);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn($uncheckedValue);

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($helper, $helper->setLabelAttributes($labelAttributes));
        self::assertSame($expectedSummary, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testInvokeMultiOptionInlineWithHiddenField1(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true, 'id' => 'zero-id'];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $uncheckedValue          = '0';
        $expectedSummary         = '    ' . sprintf(
            '<input type="hidden" name="%s" value="%s"/>',
            $name,
            $uncheckedValue,
        ) . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" id="%s" name="%s" type="radio" value="%s"/>',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField2          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField3          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst', $labelClass),
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst3', $labelClass),
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn([]);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn($uncheckedValue);

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($helper, $helper->setLabelAttributes($labelAttributes));

        $helperObject = $helper();

        assert($helperObject instanceof FormRadio);

        self::assertSame($expectedSummary, $helperObject->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testInvokeMultiOptionInlineWithHiddenField2(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true, 'id' => 'zero-id'];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $uncheckedValue          = '0';
        $expectedSummary         = '    ' . sprintf(
            '<input type="hidden" name="%s" value="%s"/>',
            $name,
            $uncheckedValue,
        ) . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>' . PHP_EOL . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" id="%s" name="%s" type="radio" value="%s"/>',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField2          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $renderedField3          = PHP_EOL
            . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst', $labelClass),
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst3', $labelClass),
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn([]);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn($uncheckedValue);

        $helper->setLabelPosition(BaseFormRow::LABEL_APPEND);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($helper, $helper->setLabelAttributes($labelAttributes));
        self::assertSame($expectedSummary, $helper($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testInvokeMultiOptionInlineWithHiddenField3(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $indent                  = '<!-- -->  ';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true, 'id' => 'zero-id'];
        $labelAttributes         = ['class' => $labelClass, 'test'];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $uncheckedValue          = '0';
        $expectedSummary         = $indent . '    ' . sprintf(
            '<input type="hidden" name="%s" value="%s"/>',
            $name,
            $uncheckedValue,
        ) . PHP_EOL . $indent . '    <div></div>' . PHP_EOL . $indent . '    <div></div>' . PHP_EOL . $indent . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . $indent . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . $indent . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" id="%s" name="%s" type="radio" value="%s"/>',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . $indent . '    ';
        $renderedField2          = PHP_EOL
            . $indent . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . $indent . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . $indent . '    ';
        $renderedField3          = PHP_EOL
            . $indent . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . $indent . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . $indent . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst', $labelClass),
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst3', $labelClass),
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn([]);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn($uncheckedValue);

        $helper->setTranslatorTextDomain($textDomain);

        $labelPosition = BaseFormRow::LABEL_PREPEND;

        self::assertSame($helper, $helper->setLabelAttributes($labelAttributes));
        self::assertSame($helper, $helper->setIndent($indent));
        self::assertSame($expectedSummary, $helper($element, $labelPosition));
        self::assertSame($labelPosition, $helper->getLabelPosition());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testInvokeMultiOptionInlineWithHiddenField4(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $value2                  = 'def';
        $value2Translated        = 'def-translated';
        $value2TranslatedEscaped = 'def-translated-escaped';
        $value3                  = 'abc';
        $value3Translated        = 'abc-translated';
        $value3TranslatedEscaped = 'abc-translated-escaped';
        $name4                   = 'ghj';
        $name4Translated         = 'ghj-translated';
        $name4TranslatedEscaped  = 'ghj-translated-escaped';
        $value4                  = 'jkl';
        $class                   = 'test-class';
        $ariaLabel               = 'test';
        $labelClass              = 'xyz';
        $indent                  = '<!-- -->  ';
        $valueOptions            = [
            [
                'value' => $value3,
                'label' => $value2,
                'selected' => false,
                'disabled' => false,
                'label_attributes' => ['class' => 'rst rst-test ', 'data-img' => 'sample1'],
                'attributes' => [
                    'class' => 'efg',
                    'id' => $id,
                ],
            ],
            [
                'value' => $value1,
                'label' => $value3,
                'selected' => false,
                'label_attributes' => ['class' => 'rst2', 'data-vid' => 'sample2'],
                'attributes' => [
                    'class' => 'efg2',
                    'aria-disabled' => 'true',
                    'id' => 'test-id2',
                ],
            ],
            [
                'value' => $value4,
                'label' => $name4,
                'disabled' => false,
                'label_attributes' => ['class' => null, 'data-img' => 'sample3'],
                'attributes' => [
                    'class' => 'efg3',
                    'aria-disabled' => 'false',
                    'id' => 'test-id3',
                ],
            ],
        ];
        $attributes              = ['class' => $class, 'aria-label' => $ariaLabel, 'disabled' => true, 'selected' => true, 'id' => 'zero-id'];
        $labelAttributes         = ['class' => $labelClass, 'test', 'data-show' => 'yes', 'data-visible' => true];
        $labelStart              = '<label>';
        $labelEnd                = '</label>';
        $expected                = '<div></div>';
        $uncheckedValue          = '0';
        $expectedSummary         = $indent . '    ' . sprintf(
            '<input type="hidden" name="%s" value="%s"/>',
            $name,
            $uncheckedValue,
        ) . PHP_EOL . $indent . '    <div></div>' . PHP_EOL . $indent . '    <div></div>' . PHP_EOL . $indent . '    <div></div>';
        $textDomain              = 'test-domain';
        $renderedField1          = PHP_EOL
            . $indent . '        ' . $labelStart . $value2TranslatedEscaped . $labelEnd . PHP_EOL
            . $indent . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg" aria-label="%s" id="%s" name="%s" type="radio" value="%s"/>',
                $class,
                $ariaLabel,
                $id,
                $name,
                $value3,
            ) . PHP_EOL
            . $indent . '    ';
        $renderedField2          = PHP_EOL
            . $indent . '        ' . $labelStart . $value3TranslatedEscaped . $labelEnd . PHP_EOL
            . $indent . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg2" aria-label="%s" disabled="disabled" name="%s" type="radio" aria-disabled="true" id="test-id2" value="%s" checked="checked"/>',
                $class,
                $ariaLabel,
                $name,
                $value1,
            ) . PHP_EOL
            . $indent . '    ';
        $renderedField3          = PHP_EOL
            . $indent . '        ' . $labelStart . $name4TranslatedEscaped . $labelEnd . PHP_EOL
            . $indent . '        ' . sprintf(
                '<input class="form-check-input&#x20;%s&#x20;efg3" aria-label="%s" name="%s" type="radio" aria-disabled="false" id="test-id3" value="%s"/>',
                $class,
                $ariaLabel,
                $name,
                $value4,
            ) . PHP_EOL
            . $indent . '    ';
        $wrap                    = false;
        $disableEscape           = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(3);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $value2Translated, $value3Translated, $name4Translated, $value2TranslatedEscaped, $value3TranslatedEscaped, $name4TranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2Translated, $value),
                        2 => self::assertSame($value3Translated, $value),
                        default => self::assertSame($name4Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2TranslatedEscaped,
                        2 => $value3TranslatedEscaped,
                        default => $name4TranslatedEscaped,
                    };
                },
            );

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::once())
            ->method('isXhtml')
            ->willReturn(true);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $matcher   = self::exactly(3);
        $formLabel->expects($matcher)
            ->method('openTag')
            ->willReturnCallback(
                static function (array | ElementInterface | null $attributesOrElement = null) use ($matcher, $labelClass, $id, $labelStart): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst rst-test', $labelClass),
                                'data-show' => 'yes',
                                'data-visible' => true,
                                'data-img' => 'sample1',
                                'for' => $id,
                            ],
                            $attributesOrElement,
                        ),
                        2 => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s rst2', $labelClass),
                                'data-show' => 'yes',
                                'data-visible' => true,
                                'data-vid' => 'sample2',
                                'for' => 'test-id2',
                            ],
                            $attributesOrElement,
                        ),
                        default => self::assertSame(
                            [
                                'class' => sprintf('form-check-label %s', $labelClass),
                                'data-show' => 'yes',
                                'data-visible' => true,
                                'data-img' => 'sample3',
                                'for' => 'test-id3',
                            ],
                            $attributesOrElement,
                        ),
                    };

                    return $labelStart;
                },
            );
        $formLabel->expects(self::exactly(3))
            ->method('closeTag')
            ->willReturn($labelEnd);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $renderedField1, $renderedField2, $renderedField3, $expected): string {
                    self::assertSame('div', $element);
                    self::assertSame(['class' => 'form-check form-check-inline'], $attribs);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($renderedField1, $content),
                        2 => self::assertSame($renderedField2, $content),
                        default => self::assertSame($renderedField3, $content),
                    };

                    return $expected;
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(3);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $value2, $value3, $name4, $textDomain, $value2Translated, $value3Translated, $name4Translated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($value2, $message),
                        2 => self::assertSame($value3, $message),
                        default => self::assertSame($name4, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $value2Translated,
                        2 => $value3Translated,
                        default => $name4Translated,
                    };
                },
            );

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $element = $this->createMock(Radio::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn([]);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('switch', $option),
                        3 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2, 3 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): bool {
                    match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4, 7 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn($uncheckedValue);

        $helper->setTranslatorTextDomain($textDomain);

        $labelPosition = BaseFormRow::LABEL_PREPEND;

        self::assertSame($helper, $helper->setLabelAttributes($labelAttributes));
        self::assertSame($helper, $helper->setIndent($indent));
        self::assertSame($expectedSummary, $helper($element, $labelPosition));
        self::assertSame($labelPosition, $helper->getLabelPosition());
    }

    /** @throws Exception */
    public function testSetGetIndent1(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        self::assertSame($helper, $helper->setIndent(4));
        self::assertSame('    ', $helper->getIndent());
    }

    /** @throws Exception */
    public function testSetGetIndent2(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');
        $doctype->expects(self::never())
            ->method('isXhtml');

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::never())
            ->method('openTag');
        $formLabel->expects(self::never())
            ->method('closeTag');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormRadio(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        self::assertSame($helper, $helper->setIndent('  '));
        self::assertSame('  ', $helper->getIndent());
    }
}
