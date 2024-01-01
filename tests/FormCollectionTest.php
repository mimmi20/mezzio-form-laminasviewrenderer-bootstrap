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

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\Text;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\Form;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\Stdlib\PriorityList;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\Escaper\AbstractHelper;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollection;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRowInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;
use function sprintf;

use const PHP_EOL;

final class FormCollectionTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithWrongElement(): void
    {
        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::never())
            ->method('setIndent');
        $formRow->expects(self::never())
            ->method('render');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        $element = $this->createMock(Text::class);
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasAttribute');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element is of type %s',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormCollection::render',
                FieldsetInterface::class,
            ),
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /** @throws Exception */
    public function testSetGetIndent1(): void
    {
        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::never())
            ->method('setIndent');
        $formRow->expects(self::never())
            ->method('render');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        self::assertSame($helper, $helper->setIndent(4));
        self::assertSame('    ', $helper->getIndent());
    }

    /** @throws Exception */
    public function testSetGetIndent2(): void
    {
        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::never())
            ->method('setIndent');
        $formRow->expects(self::never())
            ->method('render');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        self::assertSame($helper, $helper->setIndent('  '));
        self::assertSame('  ', $helper->getIndent());
    }

    /** @throws Exception */
    public function testSetShouldWrap(): void
    {
        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::never())
            ->method('setIndent');
        $formRow->expects(self::never())
            ->method('render');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        self::assertTrue($helper->shouldWrap());

        self::assertSame($helper, $helper->setShouldWrap(false));
        self::assertFalse($helper->shouldWrap());

        self::assertSame($helper, $helper->setShouldWrap(true));
        self::assertTrue($helper->shouldWrap());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithFormWithoutOptionsAndElements(): void
    {
        $form            = null;
        $layout          = null;
        $floating        = null;
        $attributes      = [];
        $labelAttributes = [];
        $label           = 'test-label';
        $labelEscaped    = 'test-label-escaped';
        $disableEscape   = false;
        $wrap            = true;
        $indent          = '';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::never())
            ->method('setIndent');
        $formRow->expects(self::never())
            ->method('render');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($label, 0)
            ->willReturn($labelEscaped);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(2);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $labelEscaped, $expectedLegend, $expectedFieldset): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('legend', $element),
                        default => self::assertSame('fieldset', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['class' => ''], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(sprintf('<span>%s</span>', $labelEscaped), $content),
                        default => self::assertSame(
                            PHP_EOL . '    ' . $expectedLegend . PHP_EOL,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedLegend,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        $list = new PriorityList();

        $element = $this->createMock(Form::class);
        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(5);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes): array | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('as-card', $option),
                        2 => self::assertSame('form', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('floating', $option),
                        default => self::assertSame('label_attributes', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);

        $helper->setIndent($indent);

        self::assertSame($indent . $expectedFieldset, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithFormAndElementsWithoutOptions(): void
    {
        $form            = null;
        $layout          = null;
        $floating        = null;
        $attributes      = [];
        $labelAttributes = [];
        $label           = 'test-label';
        $labelEscaped    = 'test-label-escaped';
        $disableEscape   = false;
        $wrap            = true;
        $indent          = '';

        $innerLabel        = 'inner-test-label';
        $innerLabelEscaped = 'inner-test-label-escaped';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';

        $expectedInnerLegend   = '<legend>inside</legend>';
        $expectedInnerFieldset = '<fieldset>inside</fieldset>';

        $element = $this->createMock(Form::class);

        $textElement = $this->createMock(Text::class);
        $textElement->expects(self::never())
            ->method('getOption');
        $textElement->expects(self::once())
            ->method('setOption')
            ->with('fieldset', $element);

        $buttonElement = $this->createMock(Button::class);
        $buttonElement->expects(self::never())
            ->method('getOption');
        $buttonElement->expects(self::once())
            ->method('setOption')
            ->with('fieldset', $element);

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(2);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $innerLabel, $label, $innerLabelEscaped, $labelEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($innerLabel, $value),
                        default => self::assertSame($label, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $innerLabelEscaped,
                        default => $labelEscaped,
                    };
                },
            );

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $innerLabelEscaped, $expectedInnerLegend, $labelEscaped, $expectedLegend, $expectedInnerFieldset, $expectedButton, $expectedText, $expectedFieldset): string {
                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame('legend', $element),
                        default => self::assertSame('fieldset', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame(['class' => ''], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(sprintf('<span>%s</span>', $innerLabelEscaped), $content),
                        2 => self::assertSame(
                            PHP_EOL . '        ' . $expectedInnerLegend . PHP_EOL . '    ',
                            $content,
                        ),
                        3 => self::assertSame(sprintf('<span>%s</span>', $labelEscaped), $content),
                        default => self::assertSame(
                            PHP_EOL . '    ' . $expectedLegend . PHP_EOL . '    ' . $expectedInnerFieldset . PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedInnerLegend,
                        2 => $expectedInnerFieldset,
                        3 => $expectedLegend,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(5);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): array | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('as-card', $option),
                        2 => self::assertSame('form', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('floating', $option),
                        default => self::assertSame('label_attributes', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        default => [],
                    };
                },
            );
        $collectionElement->expects(self::never())
            ->method('setOption');
        $collectionElement->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $collectionElement->expects(self::once())
            ->method('getLabel')
            ->willReturn($innerLabel);
        $collectionElement->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);
        $collectionElement->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes): bool | array | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('as-card', $option),
                        2 => self::assertSame('form', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('floating', $option),
                        5, 6, 7 => self::assertSame('show-required-mark', $option),
                        default => self::assertSame('label_attributes', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        5, 6, 7 => false,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);

        $helper->setIndent($indent);

        self::assertSame($indent . $expectedFieldset, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsWithoutOptions(): void
    {
        $form            = null;
        $layout          = null;
        $floating        = null;
        $attributes      = [];
        $labelAttributes = [];
        $label           = 'test-label';
        $labelEscaped    = 'test-label-escaped';
        $disableEscape   = false;
        $wrap            = true;
        $indent          = '';

        $innerLabel        = 'inner-test-label';
        $innerLabelEscaped = 'inner-test-label-escaped';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';

        $expectedInnerLegend   = '<legend>inside</legend>';
        $expectedInnerFieldset = '<fieldset>inside</fieldset>';

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $textElement->expects(self::never())
            ->method('getOption');
        $textElement->expects(self::once())
            ->method('setOption')
            ->with('fieldset', $element);

        $buttonElement = $this->createMock(Button::class);
        $buttonElement->expects(self::never())
            ->method('getOption');
        $buttonElement->expects(self::once())
            ->method('setOption')
            ->with('fieldset', $element);

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(2);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $innerLabel, $label, $innerLabelEscaped, $labelEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($innerLabel, $value),
                        default => self::assertSame($label, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $innerLabelEscaped,
                        default => $labelEscaped,
                    };
                },
            );

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $innerLabelEscaped, $expectedInnerLegend, $labelEscaped, $expectedLegend, $expectedInnerFieldset, $expectedButton, $expectedText, $expectedFieldset): string {
                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame('legend', $element),
                        default => self::assertSame('fieldset', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame(['class' => ''], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(sprintf('<span>%s</span>', $innerLabelEscaped), $content),
                        2 => self::assertSame(
                            PHP_EOL . '        ' . $expectedInnerLegend . PHP_EOL . '    ',
                            $content,
                        ),
                        3 => self::assertSame(sprintf('<span>%s</span>', $labelEscaped), $content),
                        default => self::assertSame(
                            PHP_EOL . '    ' . $expectedLegend . PHP_EOL . '    ' . $expectedInnerFieldset . PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedInnerLegend,
                        2 => $expectedInnerFieldset,
                        3 => $expectedLegend,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(5);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): array | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('as-card', $option),
                        2 => self::assertSame('form', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('floating', $option),
                        default => self::assertSame('label_attributes', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        default => [],
                    };
                },
            );
        $collectionElement->expects(self::never())
            ->method('setOption');
        $collectionElement->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $collectionElement->expects(self::once())
            ->method('getLabel')
            ->willReturn($innerLabel);
        $collectionElement->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);
        $collectionElement->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes): bool | array | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('as-card', $option),
                        2 => self::assertSame('form', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('floating', $option),
                        5, 6, 7 => self::assertSame('show-required-mark', $option),
                        default => self::assertSame('label_attributes', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        5, 6, 7 => false,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getTemplateElement');

        $helper->setIndent($indent);

        self::assertSame($indent . $expectedFieldset, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithFormAndElementsAndOptions(): void
    {
        $form            = 'test-form';
        $layout          = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating        = true;
        $attributes      = [];
        $labelAttributes = [];
        $label           = 'test-label';
        $disableEscape   = true;
        $wrap            = false;
        $indent          = '<!-- -->  ';

        $innerLabel = 'inner-test-label';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';

        $expectedInnerLegend   = '<legend>inside</legend>';
        $expectedInnerFieldset = '<fieldset>inside</fieldset>';

        $element = $this->createMock(Form::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $innerLabel, $indent, $expectedInnerLegend, $label, $expectedLegend, $expectedInnerFieldset, $expectedButton, $expectedText, $expectedFieldset): string {
                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame('legend', $element),
                        default => self::assertSame('fieldset', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame(['class' => ''], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(sprintf('<span>%s</span>', $innerLabel), $content),
                        2 => self::assertSame(
                            PHP_EOL . $indent . '        ' . $expectedInnerLegend . PHP_EOL . $indent . '    ',
                            $content,
                        ),
                        3 => self::assertSame($label, $content),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedInnerFieldset . PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedInnerLegend,
                        2 => $expectedInnerFieldset,
                        3 => $expectedLegend,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(7);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        6 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        6 => $floating,
                        default => [],
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $collectionElement->expects(self::once())
            ->method('getLabel')
            ->willReturn($innerLabel);
        $collectionElement->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);
        $collectionElement->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes): bool | array | string | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('floating', $option, (string) $invocation),
                        5, 6, 7 => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        5, 6, 7 => false,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);

        $helper->setIndent($indent);

        self::assertSame($indent . $expectedFieldset, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptions(): void
    {
        $form            = 'test-form';
        $layout          = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating        = true;
        $attributes      = [];
        $labelAttributes = [];
        $label           = 'test-label';
        $disableEscape   = true;
        $wrap            = false;
        $indent          = '<!-- -->  ';

        $innerLabel = 'inner-test-label';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';

        $expectedInnerLegend   = '<legend>inside</legend>';
        $expectedInnerFieldset = '<fieldset>inside</fieldset>';

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $innerLabel, $indent, $expectedInnerLegend, $label, $expectedLegend, $expectedInnerFieldset, $expectedButton, $expectedText, $expectedFieldset): string {
                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame('legend', $element),
                        default => self::assertSame('fieldset', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame(['class' => ''], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(sprintf('<span>%s</span>', $innerLabel), $content),
                        2 => self::assertSame(
                            PHP_EOL . $indent . '        ' . $expectedInnerLegend . PHP_EOL . $indent . '    ',
                            $content,
                        ),
                        3 => self::assertSame($label, $content),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedInnerFieldset . PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedInnerLegend,
                        2 => $expectedInnerFieldset,
                        3 => $expectedLegend,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(7);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        6 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        6 => $floating,
                        default => [],
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $collectionElement->expects(self::once())
            ->method('getLabel')
            ->willReturn($innerLabel);
        $collectionElement->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);
        $collectionElement->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes): bool | array | string | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('floating', $option, (string) $invocation),
                        5, 6, 7 => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        5, 6, 7 => false,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getTemplateElement');

        $helper->setIndent($indent);

        self::assertSame($indent . $expectedFieldset, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator(): void
    {
        $form            = 'test-form';
        $layout          = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating        = true;
        $attributes      = [];
        $labelAttributes = [];
        $label           = 'test-label';
        $labelTranslated = 'test-label-translated';
        $disableEscape   = true;
        $wrap            = false;
        $indent          = '<!-- -->  ';
        $textDomain      = 'test-domain';

        $innerLabel           = 'inner-test-label';
        $innerLabelTranslated = 'inner-test-label-translated';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';

        $expectedInnerLegend   = '<legend>inside</legend>';
        $expectedInnerFieldset = '<fieldset>inside</fieldset>';

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $innerLabel, $label, $textDomain, $innerLabelTranslated, $labelTranslated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($innerLabel, $message),
                        default => self::assertSame($label, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);

                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $innerLabelTranslated,
                        default => $labelTranslated,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $innerLabelTranslated, $labelTranslated, $indent, $expectedInnerLegend, $expectedLegend, $expectedInnerFieldset, $expectedButton, $expectedText, $expectedFieldset): string {
                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame('legend', $element),
                        default => self::assertSame('fieldset', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame(['class' => ''], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            sprintf('<span>%s</span>', $innerLabelTranslated),
                            $content,
                        ),
                        2 => self::assertSame(
                            PHP_EOL . $indent . '        ' . $expectedInnerLegend . PHP_EOL . $indent . '    ',
                            $content,
                        ),
                        3 => self::assertSame($labelTranslated, $content),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedInnerFieldset . PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedInnerLegend,
                        2 => $expectedInnerFieldset,
                        3 => $expectedLegend,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(7);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        6 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        6 => $floating,
                        default => [],
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $collectionElement->expects(self::once())
            ->method('getLabel')
            ->willReturn($innerLabel);
        $collectionElement->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);
        $collectionElement->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes): bool | array | string | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('floating', $option, (string) $invocation),
                        5, 6, 7 => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        5, 6, 7 => false,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getTemplateElement');

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedFieldset, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator2(): void
    {
        $form                   = 'test-form';
        $layout                 = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating               = true;
        $attributes             = [];
        $labelAttributes        = [];
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $disableEscape          = false;
        $wrap                   = false;
        $indent                 = '<!-- -->  ';
        $textDomain             = 'test-domain';

        $innerLabel                  = 'inner-test-label';
        $innerLabelTranslated        = 'inner-test-label-translated';
        $innerLabelTranslatedEscaped = 'inner-test-label-translated-escaped';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';

        $expectedInnerLegend   = '<legend>inside</legend>';
        $expectedInnerFieldset = '<fieldset>inside</fieldset>';

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $matcher    = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $message, string | null $textDomainParam = null, string | null $locale = null) use ($matcher, $innerLabel, $label, $textDomain, $innerLabelTranslated, $labelTranslated): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($innerLabel, $message),
                        default => self::assertSame($label, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);

                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $innerLabelTranslated,
                        default => $labelTranslated,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(2);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $innerLabelTranslated, $labelTranslated, $innerLabelTranslatedEscaped, $labelTranslatedEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($innerLabelTranslated, $value),
                        default => self::assertSame($labelTranslated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $innerLabelTranslatedEscaped,
                        default => $labelTranslatedEscaped,
                    };
                },
            );

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $innerLabelTranslatedEscaped, $labelTranslatedEscaped, $indent, $expectedInnerLegend, $expectedLegend, $expectedInnerFieldset, $expectedButton, $expectedText, $expectedFieldset): string {
                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame('legend', $element),
                        default => self::assertSame('fieldset', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1, 3 => self::assertSame(['class' => ''], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            sprintf('<span>%s</span>', $innerLabelTranslatedEscaped),
                            $content,
                        ),
                        2 => self::assertSame(
                            PHP_EOL . $indent . '        ' . $expectedInnerLegend . PHP_EOL . $indent . '    ',
                            $content,
                        ),
                        3 => self::assertSame($labelTranslatedEscaped, $content),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedInnerFieldset . PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedInnerLegend,
                        2 => $expectedInnerFieldset,
                        3 => $expectedLegend,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(7);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        6 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        6 => $floating,
                        default => [],
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $collectionElement->expects(self::once())
            ->method('getLabel')
            ->willReturn($innerLabel);
        $collectionElement->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);
        $collectionElement->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes): bool | array | string | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('floating', $option, (string) $invocation),
                        5, 6, 7 => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                        default => self::assertSame('label_attributes', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        5, 6, 7 => false,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getTemplateElement');

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedFieldset, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator3(): void
    {
        $form       = 'test-form';
        $layout     = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating   = true;
        $indent     = '<!-- -->  ';
        $textDomain = 'test-domain';

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';
        $expected       = PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL;

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(6);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        default => $floating,
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::never())
            ->method('getAttributes');
        $collectionElement->expects(self::never())
            ->method('getLabel');
        $collectionElement->expects(self::never())
            ->method('getLabelOption');
        $collectionElement->expects(self::never())
            ->method('hasAttribute');
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        default => false,
                    };
                },
            );
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getTemplateElement');

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);
        $helper->setShouldWrap(false);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator4(): void
    {
        $form       = 'test-form';
        $layout     = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating   = true;
        $indent     = '<!-- -->  ';
        $textDomain = 'test-domain';

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';
        $expected       = PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL;

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(6);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        default => $floating,
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::never())
            ->method('getAttributes');
        $collectionElement->expects(self::never())
            ->method('getLabel');
        $collectionElement->expects(self::never())
            ->method('getLabelOption');
        $collectionElement->expects(self::never())
            ->method('hasAttribute');
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        default => false,
                    };
                },
            );
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getTemplateElement')
            ->willReturn(null);

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);
        $helper->setShouldWrap(false);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator5(): void
    {
        $form               = 'test-form';
        $layout             = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating           = true;
        $indent             = '<!-- -->  ';
        $textDomain         = 'test-domain';
        $templateAttributes = ['class' => 'template-class'];

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $templateElement = $this->createMock(Text::class);
        $templateElement->expects(self::never())
            ->method('getOption');
        $templateElement->expects(self::never())
            ->method('setOption');

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton   = $indent . '    <button></button>';
        $expectedText     = $indent . '    <text></text>';
        $expectedTemplate = $indent . '    <template></template>';
        $renderedTemplate = '<template>template-content</template>';

        $expected = PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent . '    ' . $renderedTemplate . PHP_EOL;

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(3))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(3);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $templateElement, $buttonElement, $textElement, $expectedTemplate, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($templateElement, $element),
                        2 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedTemplate,
                        2 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('template', ['class' => 'template-class'], $expectedTemplate . PHP_EOL . $indent)
            ->willReturn($renderedTemplate);

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(6);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        default => $floating,
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::never())
            ->method('getAttributes');
        $collectionElement->expects(self::never())
            ->method('getLabel');
        $collectionElement->expects(self::never())
            ->method('getLabelOption');
        $collectionElement->expects(self::never())
            ->method('hasAttribute');
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $templateAttributes): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('template_attributes', $option, (string) $invocation),
                        3 => self::assertSame('form', $option, (string) $invocation),
                        4 => self::assertSame('layout', $option, (string) $invocation),
                        5 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $templateAttributes,
                        3 => $form,
                        4 => $layout,
                        5 => $floating,
                        default => false,
                    };
                },
            );
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getTemplateElement')
            ->willReturn($templateElement);

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);
        $helper->setShouldWrap(false);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator6(): void
    {
        $form               = 'test-form';
        $layout             = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating           = true;
        $indent             = '<!-- -->  ';
        $textDomain         = 'test-domain';
        $templateAttributes = ['class' => 'template-class'];

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $templateList = new PriorityList();

        $templateElement = $this->createMock(Collection::class);
        $matcher         = self::exactly(4);
        $templateElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        default => $floating,
                    };
                },
            );
        $templateElement->expects(self::never())
            ->method('setOption');
        $templateElement->expects(self::never())
            ->method('getAttributes');
        $templateElement->expects(self::never())
            ->method('getLabel');
        $templateElement->expects(self::never())
            ->method('getLabelOption');
        $templateElement->expects(self::never())
            ->method('hasAttribute');
        $templateElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($templateList);
        $templateElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $templateElement->expects(self::never())
            ->method('getTemplateElement');

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton   = $indent . '    <button></button>';
        $expectedText     = $indent . '    <text></text>';
        $renderedTemplate = '<template>template-content</template>';

        $expected = PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent . '    ' . $renderedTemplate . PHP_EOL;

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('template', ['class' => 'template-class'], PHP_EOL . $indent)
            ->willReturn($renderedTemplate);

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(6);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        default => $floating,
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::never())
            ->method('getAttributes');
        $collectionElement->expects(self::never())
            ->method('getLabel');
        $collectionElement->expects(self::never())
            ->method('getLabelOption');
        $collectionElement->expects(self::never())
            ->method('hasAttribute');
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $templateAttributes): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('template_attributes', $option, (string) $invocation),
                        3 => self::assertSame('form', $option, (string) $invocation),
                        4 => self::assertSame('layout', $option, (string) $invocation),
                        5 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $templateAttributes,
                        3 => $form,
                        4 => $layout,
                        5 => $floating,
                        default => false,
                    };
                },
            );
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getTemplateElement')
            ->willReturn($templateElement);

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);
        $helper->setShouldWrap(false);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator7(): void
    {
        $form               = 'test-form';
        $layout             = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating           = true;
        $indent             = '<!-- -->  ';
        $textDomain         = 'test-domain';
        $templateAttributes = ['class' => 'template-class'];

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $templateList = new PriorityList();

        $templateElement = $this->createMock(Collection::class);
        $matcher         = self::exactly(4);
        $templateElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        default => $floating,
                    };
                },
            );
        $templateElement->expects(self::never())
            ->method('setOption');
        $templateElement->expects(self::never())
            ->method('getAttributes');
        $templateElement->expects(self::never())
            ->method('getLabel');
        $templateElement->expects(self::never())
            ->method('getLabelOption');
        $templateElement->expects(self::never())
            ->method('hasAttribute');
        $templateElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($templateList);
        $templateElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $templateElement->expects(self::never())
            ->method('getTemplateElement');

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton   = $indent . '    <button></button>';
        $expectedText     = $indent . '    <text></text>';
        $renderedTemplate = '<template>template-content</template>';

        $expected = PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent . '    ' . $renderedTemplate . PHP_EOL;

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('template', ['class' => 'template-class'], PHP_EOL . $indent)
            ->willReturn($renderedTemplate);

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(6);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        default => $floating,
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::never())
            ->method('getAttributes');
        $collectionElement->expects(self::never())
            ->method('getLabel');
        $collectionElement->expects(self::never())
            ->method('getLabelOption');
        $collectionElement->expects(self::never())
            ->method('hasAttribute');
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $templateAttributes): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('template_attributes', $option, (string) $invocation),
                        3 => self::assertSame('form', $option, (string) $invocation),
                        4 => self::assertSame('layout', $option, (string) $invocation),
                        5 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $templateAttributes,
                        3 => $form,
                        4 => $layout,
                        5 => $floating,
                        default => false,
                    };
                },
            );
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getTemplateElement')
            ->willReturn($templateElement);

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);
        $helper->setShouldWrap(false);

        $helperObject = $helper();

        assert($helperObject instanceof FormCollection);

        self::assertSame($expected, $helperObject->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator8(): void
    {
        $form               = 'test-form';
        $layout             = \Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL;
        $floating           = true;
        $indent             = '<!-- -->  ';
        $textDomain         = 'test-domain';
        $templateAttributes = ['class' => 'template-class'];

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $matcher     = self::exactly(2);
        $textElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $textElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $templateList = new PriorityList();

        $templateElement = $this->createMock(Collection::class);
        $matcher         = self::exactly(4);
        $templateElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('form', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        default => $floating,
                    };
                },
            );
        $templateElement->expects(self::never())
            ->method('setOption');
        $templateElement->expects(self::never())
            ->method('getAttributes');
        $templateElement->expects(self::never())
            ->method('getLabel');
        $templateElement->expects(self::never())
            ->method('getLabelOption');
        $templateElement->expects(self::never())
            ->method('hasAttribute');
        $templateElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($templateList);
        $templateElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $templateElement->expects(self::never())
            ->method('getTemplateElement');

        $buttonElement = $this->createMock(Button::class);
        $matcher       = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        default => self::assertSame('layout', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        default => $layout,
                    };
                },
            );
        $matcher = self::exactly(2);
        $buttonElement->expects($matcher)
            ->method('setOption')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $element): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('floating', $key),
                        default => self::assertSame('fieldset', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertTrue($value),
                        default => self::assertSame($element, $value),
                    };
                },
            );

        $expectedButton   = $indent . '    <button></button>';
        $expectedText     = $indent . '    <text></text>';
        $renderedTemplate = '<template>template-content</template>';

        $expected = PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . $indent . '    ' . $renderedTemplate . PHP_EOL;

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '    ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('template', ['class' => 'template-class'], PHP_EOL . $indent)
            ->willReturn($renderedTemplate);

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, $translator);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(6);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): string | bool | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        3 => self::assertSame('as-card', $option, (string) $invocation),
                        1, 4 => self::assertSame('form', $option, (string) $invocation),
                        2, 5 => self::assertSame('layout', $option, (string) $invocation),
                        default => self::assertSame('floating', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        3 => null,
                        1, 4 => $form,
                        2, 5 => $layout,
                        default => $floating,
                    };
                },
            );
        $collectionElement->expects(self::once())
            ->method('setOption')
            ->with('floating', true);
        $collectionElement->expects(self::never())
            ->method('getAttributes');
        $collectionElement->expects(self::never())
            ->method('getLabel');
        $collectionElement->expects(self::never())
            ->method('getLabelOption');
        $collectionElement->expects(self::never())
            ->method('hasAttribute');
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $templateAttributes): string | bool | array | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('as-card', $option, (string) $invocation),
                        2 => self::assertSame('template_attributes', $option, (string) $invocation),
                        3 => self::assertSame('form', $option, (string) $invocation),
                        4 => self::assertSame('layout', $option, (string) $invocation),
                        5 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame(
                            'show-required-mark',
                            $option,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => null,
                        2 => $templateAttributes,
                        3 => $form,
                        4 => $layout,
                        5 => $floating,
                        default => false,
                    };
                },
            );
        $element->expects(self::never())
            ->method('getAttributes');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getTemplateElement')
            ->willReturn($templateElement);

        $helper->setIndent($indent);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper($element, false));
        self::assertFalse($helper->shouldWrap());
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws InvalidServiceException
     * @throws ServiceNotFoundException
     * @throws \Laminas\View\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithCollectionAndElementsAndOptionsAndTranslator9(): void
    {
        $form                 = null;
        $layout               = null;
        $floating             = null;
        $elementAttributes    = ['name' => 'element-name', 'class' => 'card-body body-1'];
        $collectionAttributes = ['name' => 'collection-name', 'class' => 'card-body body-2'];
        $labelAttributes      = ['class' => 'card-title card-title-1'];
        $cardAttributes       = ['class' => 'card card-1'];
        $colAttributes        = ['class' => 'col col-1'];
        $label                = 'test-label';
        $labelEscaped         = 'test-label-escaped';
        $disableEscape        = false;
        $wrap                 = true;
        $indent               = '';

        $innerLabel        = 'inner-test-label';
        $innerLabelEscaped = 'inner-test-label-escaped';

        $expectedLegend   = '<legend></legend>';
        $expectedFieldset = '<fieldset></fieldset>';
        $expectedCard     = '<card></card>';
        $expectedCol      = '<col></col>';

        $expectedInnerLegend   = '<legend>inside</legend>';
        $expectedInnerFieldset = '<fieldset>inside</fieldset>';

        $element = $this->createMock(Collection::class);

        $textElement = $this->createMock(Text::class);
        $textElement->expects(self::never())
            ->method('getOption');
        $textElement->expects(self::once())
            ->method('setOption')
            ->with('fieldset', $element);

        $buttonElement = $this->createMock(Button::class);
        $buttonElement->expects(self::never())
            ->method('getOption');
        $buttonElement->expects(self::once())
            ->method('setOption')
            ->with('fieldset', $element);

        $expectedButton = $indent . '    <button></button>';
        $expectedText   = $indent . '    <text></text>';

        $formRow = $this->createMock(FormRowInterface::class);
        $formRow->expects(self::exactly(2))
            ->method('setIndent')
            ->with($indent . '            ');
        $matcher = self::exactly(2);
        $formRow->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element, string | null $labelPosition = null) use ($matcher, $buttonElement, $textElement, $expectedButton, $expectedText): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($buttonElement, $element),
                        default => self::assertSame($textElement, $element),
                    };

                    self::assertNull($labelPosition);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedButton,
                        default => $expectedText,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher    = self::exactly(2);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                static function (string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $innerLabel, $label, $innerLabelEscaped, $labelEscaped): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($innerLabel, $value),
                        default => self::assertSame($label, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $innerLabelEscaped,
                        default => $labelEscaped,
                    };
                },
            );

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(6);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $innerLabelEscaped, $expectedInnerLegend, $labelEscaped, $expectedLegend, $expectedInnerFieldset, $expectedButton, $expectedText, $expectedFieldset, $expectedCard, $expectedCol): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 3 => self::assertSame('legend', $element, (string) $invocation),
                        5, 6 => self::assertSame('div', $element, (string) $invocation),
                        default => self::assertSame('fieldset', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(['class' => ''], $attribs, (string) $invocation),
                        2 => self::assertSame(
                            ['class' => 'card-body body-2'],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'card-title'],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            ['class' => 'card-body body-1'],
                            $attribs,
                            (string) $invocation,
                        ),
                        5 => self::assertSame(
                            ['class' => 'card card-1'],
                            $attribs,
                            (string) $invocation,
                        ),
                        6 => self::assertSame(['class' => 'col col-1'], $attribs, (string) $invocation),
                        default => self::assertSame([], $attribs, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            sprintf('<span>%s</span>', $innerLabelEscaped),
                            $content,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            PHP_EOL . '                ' . $expectedInnerLegend . PHP_EOL . '            ',
                            $content,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            sprintf('<span>%s</span>', $labelEscaped),
                            $content,
                            (string) $invocation,
                        ),
                        5 => self::assertSame(
                            PHP_EOL . '    ' . $expectedFieldset . PHP_EOL . '    ',
                            $content,
                            (string) $invocation,
                        ),
                        6 => self::assertSame(
                            PHP_EOL . '    ' . $expectedCard . PHP_EOL,
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            PHP_EOL . '            ' . $expectedLegend . PHP_EOL . '            ' . $expectedInnerFieldset . PHP_EOL . $expectedButton . PHP_EOL . $expectedText . PHP_EOL . '        ',
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expectedInnerLegend,
                        2 => $expectedInnerFieldset,
                        3 => $expectedLegend,
                        5 => $expectedCard,
                        6 => $expectedCol,
                        default => $expectedFieldset,
                    };
                },
            );

        $helper = new FormCollection($formRow, $escapeHtml, $htmlElement, null);

        $innerList = new PriorityList();

        $collectionElement = $this->createMock(Collection::class);
        $matcher           = self::exactly(5);
        $collectionElement->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating): array | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('as-card', $option),
                        2 => self::assertSame('form', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('floating', $option),
                        default => self::assertSame('label_attributes', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => null,
                        2 => $form,
                        3 => $layout,
                        4 => $floating,
                        default => [],
                    };
                },
            );
        $collectionElement->expects(self::never())
            ->method('setOption');
        $collectionElement->expects(self::once())
            ->method('getAttributes')
            ->willReturn($collectionAttributes);
        $collectionElement->expects(self::once())
            ->method('getLabel')
            ->willReturn($innerLabel);
        $collectionElement->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);
        $collectionElement->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $collectionElement->expects(self::once())
            ->method('getIterator')
            ->willReturn($innerList);
        $collectionElement->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $collectionElement->expects(self::never())
            ->method('getTemplateElement');

        $list = new PriorityList();
        $list->insert('x', $textElement);
        $list->insert('y', $buttonElement);
        $list->insert('z', $collectionElement);

        $element->expects(self::never())
            ->method('getName');
        $matcher = self::exactly(11);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $layout, $floating, $labelAttributes, $cardAttributes, $colAttributes): bool | array | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('as-card', $option),
                        2, 9 => self::assertSame('form', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('floating', $option),
                        5, 6, 7 => self::assertSame('show-required-mark', $option),
                        10 => self::assertSame('card_attributes', $option),
                        11 => self::assertSame('col_attributes', $option),
                        default => self::assertSame('label_attributes', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => true,
                        2, 9 => $form,
                        3 => $layout,
                        4 => $floating,
                        5, 6, 7 => false,
                        10 => $cardAttributes,
                        11 => $colAttributes,
                        default => $labelAttributes,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($elementAttributes);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $matcher = self::exactly(2);
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
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('getIterator')
            ->willReturn($list);
        $element->expects(self::once())
            ->method('shouldCreateTemplate')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getTemplateElement');

        $helper->setIndent($indent);

        self::assertSame($indent . $expectedCol, $helper->render($element));
    }
}
