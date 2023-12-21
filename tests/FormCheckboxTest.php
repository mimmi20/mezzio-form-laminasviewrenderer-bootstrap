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

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormLabelInterface;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

use function sprintf;

use const PHP_EOL;

final class FormCheckboxTest extends TestCase
{
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

        $helper = new FormCheckbox(
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

        $helper = new FormCheckbox(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $helper->setLabelPosition($labelPosition);

        self::assertSame($labelPosition, $helper->getLabelPosition());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
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

        $helper = new FormCheckbox(
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element is of type %s',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox::render',
                Checkbox::class,
            ),
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
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

        $helper = new FormCheckbox(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Checkbox::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCheckbox::render',
            ),
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderInlineForm(): void
    {
        $name         = 'chkbox';
        $id           = 'chck-id';
        $label        = 'test-label';
        $escapedLabel = 'escaped-label';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($label)
            ->willReturn($escapedLabel);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');

        $expected = '<input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">';

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'div',
                ['class' => 'form-check form-check-inline'],
                PHP_EOL
                . '<label for="chck-id">' . PHP_EOL
                . '    <input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">' . PHP_EOL
                . '    <span>escaped-label</span>' . PHP_EOL
                . '</label>' . PHP_EOL,
            )
            ->willReturn($expected);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(['class' => 'form-check-label abc', 'for' => $id])
            ->willReturn(
                sprintf('<label for="%s">', $id),
            );
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn('</label>');

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormCheckbox(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            null,
        );

        $element = $this->createMock(Checkbox::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        default => Form::LAYOUT_INLINE,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn(['class' => 'abc']);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(['class' => 'xyz']);
        $element->expects(self::once())
            ->method('isChecked')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderVerticalFormWithTranslator(): void
    {
        $name                  = 'chkbox';
        $id                    = 'chck-id';
        $label                 = 'test-label';
        $textDomain            = 'text-domain';
        $tranlatedLabel        = 'test-label-translated';
        $escapedTranlatedLabel = 'test-label-translated-escaped';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($tranlatedLabel)
            ->willReturn($escapedTranlatedLabel);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');

        $expected = '<input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">';

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'div',
                ['class' => 'form-check'],
                PHP_EOL
                . '<label for="chck-id">' . PHP_EOL
                . '    <span>test-label-translated-escaped</span>' . PHP_EOL
                . '    <input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">' . PHP_EOL
                . '</label>' . PHP_EOL,
            )
            ->willReturn($expected);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(['class' => 'form-check-label abc', 'for' => $id])
            ->willReturn(
                sprintf('<label for="%s">', $id),
            );
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn('</label>');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain)
            ->willReturn($tranlatedLabel);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormCheckbox(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $helper->setTranslatorTextDomain($textDomain);
        $helper->setLabelPosition(BaseFormRow::LABEL_PREPEND);

        $element = $this->createMock(Checkbox::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        default => Form::LAYOUT_VERTICAL,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn(['class' => 'abc']);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(['class' => 'xyz']);
        $element->expects(self::once())
            ->method('isChecked')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderVerticalFormWithId(): void
    {
        $name                  = 'chkbox';
        $id                    = 'chck-id';
        $label                 = 'test-label';
        $textDomain            = 'text-domain';
        $tranlatedLabel        = 'test-label-translated';
        $escapedTranlatedLabel = 'test-label-translated-escaped';
        $wrap                  = false;
        $disableEscape         = false;

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($tranlatedLabel)
            ->willReturn($escapedTranlatedLabel);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');

        $expected = '<input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">';

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'div',
                ['class' => 'form-check'],
                PHP_EOL
                . '    <label for="chck-id">test-label-translated-escaped</label>' . PHP_EOL
                . '    <input class="form-check-input&#x20;xyz" id="chck-id" name="chkbox" type="checkbox" value="" checked="checked">' . PHP_EOL,
            )
            ->willReturn($expected);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(['class' => 'form-check-label abc', 'for' => $id])
            ->willReturn(
                sprintf('<label for="%s">', $id),
            );
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn('</label>');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain)
            ->willReturn($tranlatedLabel);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormCheckbox(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $helper->setTranslatorTextDomain($textDomain);
        $helper->setLabelPosition(BaseFormRow::LABEL_PREPEND);

        $element = $this->createMock(Checkbox::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        default => Form::LAYOUT_VERTICAL,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn(['class' => 'abc']);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(['class' => 'xyz', 'id' => $id]);
        $element->expects(self::once())
            ->method('isChecked')
            ->willReturn(true);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (int | string $key) use ($matcher, $disableEscape, $wrap): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getUncheckedValue');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderVerticalFormWithHiddenField1(): void
    {
        $name                  = 'chkbox';
        $id                    = 'chck-id';
        $label                 = 'test-label';
        $textDomain            = 'text-domain';
        $tranlatedLabel        = 'test-label-translated';
        $escapedTranlatedLabel = 'test-label-translated-escaped';
        $wrap                  = false;
        $disableEscape         = false;
        $uncheckedValue        = '0';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($tranlatedLabel)
            ->willReturn($escapedTranlatedLabel);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');

        $expected = '<input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">';

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'div',
                ['class' => 'form-check'],
                PHP_EOL
                . sprintf(
                    '    <input type="hidden" name="%s" value="%s"/>',
                    $name,
                    $uncheckedValue,
                ) . PHP_EOL
                . '    <label for="chck-id">test-label-translated-escaped</label>' . PHP_EOL
                . '    <input class="form-check-input&#x20;xyz" id="chck-id" name="chkbox" type="checkbox" value="" checked="checked">' . PHP_EOL,
            )
            ->willReturn($expected);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(['class' => 'form-check-label abc', 'for' => $id])
            ->willReturn(
                sprintf('<label for="%s">', $id),
            );
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn('</label>');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain)
            ->willReturn($tranlatedLabel);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormCheckbox(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $helper->setTranslatorTextDomain($textDomain);
        $helper->setLabelPosition(BaseFormRow::LABEL_PREPEND);

        $element = $this->createMock(Checkbox::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        default => Form::LAYOUT_VERTICAL,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn(['class' => 'abc']);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(['class' => 'xyz', 'id' => $id]);
        $element->expects(self::once())
            ->method('isChecked')
            ->willReturn(true);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (int | string $key) use ($matcher, $disableEscape, $wrap): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn('');

        $helper->setUncheckedValue($uncheckedValue);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     */
    public function testRenderVerticalFormWithHiddenField2(): void
    {
        $name                  = 'chkbox';
        $id                    = 'chck-id';
        $label                 = 'test-label';
        $textDomain            = 'text-domain';
        $tranlatedLabel        = 'test-label-translated';
        $escapedTranlatedLabel = 'test-label-translated-escaped';
        $wrap                  = true;
        $disableEscape         = false;
        $uncheckedValue        = '0';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($tranlatedLabel)
            ->willReturn($escapedTranlatedLabel);

        $escapeHtmlAttr = $this->createMock(EscapeHtmlAttr::class);
        $escapeHtmlAttr->expects(self::never())
            ->method('__invoke');

        $doctype = $this->createMock(Doctype::class);
        $doctype->expects(self::never())
            ->method('__invoke');

        $expected = '<input class="form-check-input&#x20;xyz" name="chkbox" type="checkbox" value="" checked="checked">';

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with(
                'div',
                ['class' => 'form-check'],
                PHP_EOL
                . sprintf(
                    '<input type="hidden" name="%s" value="%s"/>',
                    $name,
                    $uncheckedValue,
                ) . PHP_EOL
                . '<label for="chck-id">' . PHP_EOL
                . '    <span>test-label-translated-escaped</span>' . PHP_EOL
                . '    <input class="form-check-input&#x20;xyz" id="chck-id" name="chkbox" type="checkbox" value="" checked="checked">' . PHP_EOL
                . '</label>' . PHP_EOL,
            )
            ->willReturn($expected);

        $formLabel = $this->createMock(FormLabelInterface::class);
        $formLabel->expects(self::once())
            ->method('openTag')
            ->with(['class' => 'form-check-label abc', 'for' => $id])
            ->willReturn(
                sprintf('<label for="%s">', $id),
            );
        $formLabel->expects(self::once())
            ->method('closeTag')
            ->willReturn('</label>');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain)
            ->willReturn($tranlatedLabel);

        $formHidden = $this->createMock(FormHiddenInterface::class);
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(
                sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $uncheckedValue),
            );

        $helper = new FormCheckbox(
            $escapeHtml,
            $escapeHtmlAttr,
            $doctype,
            $formLabel,
            $htmlElement,
            $formHidden,
            $translator,
        );

        $helper->setTranslatorTextDomain($textDomain);
        $helper->setLabelPosition(BaseFormRow::LABEL_PREPEND);

        $element = $this->createMock(Checkbox::class);
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher): string | null {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('group_attributes', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => null,
                        default => Form::LAYOUT_VERTICAL,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('getLabelAttributes')
            ->willReturn(['class' => 'abc']);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(['class' => 'xyz', 'id' => $id]);
        $element->expects(self::once())
            ->method('isChecked')
            ->willReturn(true);
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $wrap): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('always_wrap', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $disableEscape,
                        default => $wrap,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getUncheckedValue')
            ->willReturn('');

        $helper->setUncheckedValue($uncheckedValue);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
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

        $helper = new FormCheckbox(
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

        $helper = new FormCheckbox(
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
