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
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Text;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\FormInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\Form;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrorsInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementInterface;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormRow;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function array_merge;
use function get_debug_type;
use function sprintf;

use const PHP_EOL;

final class FormRowTest extends TestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderWithWrongFormOption(): void
    {
        $form = true;

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn($form);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getMessages');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage(
            sprintf(
                '$form should be an Instance of %s or null, but was %s',
                FormInterface::class,
                get_debug_type($form),
            ),
        );

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderHiddenWithoutFormOptionAndLabel(): void
    {
        $label        = '';
        $messages     = [];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithoutFormOptionAndLabel(): void
    {
        $label        = '';
        $messages     = [];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithoutFormOptionAndLabel2(): void
    {
        $label        = '';
        $messages     = ['x' => 'y'];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return false;
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', 'is-invalid');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithoutFormOptionAndLabel3(): void
    {
        $label        = '';
        $messages     = ['x' => 'y'];
        $type         = 'hidden';
        $class        = 'test-class';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', $class . ' is-invalid');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        default => self::assertSame('class', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        default => $class,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderHiddenWithLabelWithoutFormOption(): void
    {
        $label        = 'test-label';
        $messages     = [];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithLabelWithoutFormOption(): void
    {
        $label        = 'test-label';
        $messages     = [];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithLabelWithoutFormOption2(): void
    {
        $label        = 'test-label';
        $messages     = ['x' => 'y'];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return false;
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', 'is-invalid');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithLabelWithoutFormOption3(): void
    {
        $label        = 'test-label';
        $messages     = ['x' => 'y'];
        $type         = 'hidden';
        $class        = 'test-class';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', $class . ' is-invalid');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        default => self::assertSame('class', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        default => $class,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderHiddenWithLabelAndTranslatorWithoutFormOption(): void
    {
        $label        = 'test-label';
        $messages     = [];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $renderErrors = false;
        $textDomain   = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithLabelAndTranslatorWithoutFormOption(): void
    {
        $label        = 'test-label';
        $messages     = [];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;
        $textDomain   = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithLabelAndTranslatorWithoutFormOption2(): void
    {
        $label        = 'test-label';
        $messages     = ['x' => 'y'];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;
        $textDomain   = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return false;
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', 'is-invalid');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('type')
            ->willReturn($type);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderPartialWithLabelAndTranslatorWithoutFormOption3(): void
    {
        $label        = 'test-label';
        $messages     = ['x' => 'y'];
        $type         = 'hidden';
        $class        = 'test-class';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;
        $textDomain   = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', $class . ' is-invalid');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        default => self::assertSame('class', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        default => $class,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::once())
            ->method('render')
            ->with(
                $partial,
                [
                    'element' => $element,
                    'label' => $label,
                    'labelAttributes' => [],
                    'labelPosition' => \Laminas\Form\View\Helper\FormRow::LABEL_PREPEND,
                    'renderErrors' => $renderErrors,
                    'indent' => $indent,
                ],
            )
            ->willReturn($expected);

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel(): void
    {
        $label            = '';
        $messages         = [];
        $type             = 'text';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $renderErrors     = false;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = null;
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent): bool | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $required): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel4(): void
    {
        $label            = '';
        $messages         = [];
        $type             = 'text';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $renderErrors     = false;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = null;
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent): bool | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('required')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('setAttribute');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $required): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel5(): void
    {
        $label            = '';
        $messages         = ['x' => 'y'];
        $type             = 'text';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $renderErrors     = false;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = null;
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent): bool | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return false;
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', 'is-invalid');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $required): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel6(): void
    {
        $label            = '';
        $messages         = ['x' => 'y'];
        $type             = 'text';
        $class            = 'test-class';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $renderErrors     = false;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = null;
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent): bool | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        default => self::assertSame('class', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', $class . ' is-invalid');
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel7(): void
    {
        $label            = '';
        $messages         = ['x' => 'y'];
        $type             = 'text';
        $class            = 'test-class';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $expectedErrors   = '<errors></errors>';
        $renderErrors     = true;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = null;
        $id               = 'test-id';
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent): bool | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        3 => self::assertSame('id', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 4 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        default => self::assertSame($id . 'Feedback', $value),
                    };
                },
            );
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        4 => self::assertSame('id', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        4 => $id,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected . $expectedErrors, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel8(): void
    {
        $label            = '';
        $messages         = ['x' => 'y'];
        $type             = 'text';
        $class            = 'test-class';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $expectedErrors   = '<errors></errors>';
        $renderErrors     = true;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = null;
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent): bool | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        default => self::assertSame('id', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        2 => true,
                        default => false,
                    };
                },
            );
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', $class . ' is-invalid');
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected . $expectedErrors, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel9(): void
    {
        $label            = '';
        $messages         = ['x' => 'y'];
        $type             = 'text';
        $class            = 'test-class';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $expectedErrors   = '<errors></errors>';
        $renderErrors     = true;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = null;
        $id               = 'test-id';
        $aria             = 'aria-described';
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent): bool | null {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        3 => self::assertSame('id', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        default => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                    };
                },
            );
        $matcher = self::exactly(5);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        5 => self::assertSame('id', $key),
                        4 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        5 => $id,
                        4 => $aria,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected . $expectedErrors, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel10(): void
    {
        $label            = '';
        $messages         = ['x' => 'y'];
        $type             = 'text';
        $class            = 'test-class';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $renderErrors     = false;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = 'help';
        $helpAttributes   = ['a' => 'b'];
        $expectedHelp     = '<help></help>';
        $id               = 'test-id';
        $aria             = 'aria-described';
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes): bool | array | string | null {
                    match ($matcher->numberOfInvocations()) {
                        1, 7 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        6 => self::assertSame('help_attributes', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 7 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        6 => $helpAttributes,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(4);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        3 => self::assertSame('id', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        default => self::assertSame($aria . ' ' . $id . 'Help', $value),
                    };
                },
            );
        $matcher = self::exactly(6);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        4, 6 => self::assertSame('id', $key),
                        5 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        4, 6 => $id,
                        5 => $aria,
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', $helpAttributes + ['id' => $id . 'Help'], $helpContent)
            ->willReturn($expectedHelp);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame(
            $expected . PHP_EOL . $indent . '    ' . $expectedHelp,
            $helper->render($element),
        );
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderTextWithoutFormOptionAndLabel11(): void
    {
        $label            = '';
        $messages         = ['x' => 'y'];
        $type             = 'text';
        $class            = 'test-class';
        $indent           = '<!-- -->  ';
        $expected         = '<hidden></hidden>';
        $expectedErrors   = '<errors></errors>';
        $renderErrors     = true;
        $required         = true;
        $showRequiredMark = false;
        $layout           = null;
        $helpContent      = 'help';
        $helpAttributes   = ['a' => 'b'];
        $expectedHelp     = '<help></help>';
        $id               = 'test-id';
        $aria             = 'aria-described';
        $form             = null;
        $textDomain       = 'text-domain';

        $element = $this->createMock(ElementInterface::class);
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes): bool | array | string | null {
                    match ($matcher->numberOfInvocations()) {
                        1, 7 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        6 => self::assertSame('help_attributes', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 7 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        6 => $helpAttributes,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(6);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        3, 5 => self::assertSame('id', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        5, 6, 8 => self::assertSame('id', $key),
                        4, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        5, 6, 8 => $id,
                        4 => $aria,
                        7 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('div', $helpAttributes + ['id' => $id . 'Help'], $helpContent)
            ->willReturn($expectedHelp);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::never())
            ->method('__invoke');

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame(
            $expected . $expectedErrors . PHP_EOL . $indent . '    ' . $expectedHelp,
            $helper->render($element),
        );
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderHorizontalForm(): void
    {
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $messages               = ['x' => 'y'];
        $type                   = 'radio';
        $class                  = 'test-class';
        $indent                 = '<!-- -->  ';
        $expected               = '<hidden></hidden>';
        $expectedErrors         = '<errors></errors>';
        $renderErrors           = true;
        $required               = true;
        $showRequiredMark       = false;
        $layout                 = Form::LAYOUT_HORIZONTAL;
        $helpContent            = 'help';
        $helpAttributes         = ['a' => 'b'];
        $expectedHelp           = '<help></help>';
        $id                     = 'test-id';
        $aria                   = 'aria-described';
        $form                   = null;
        $rowAttributes          = ['c' => 'd'];
        $colAttributes          = ['e' => 'f'];
        $labelAttributes        = ['g' => 'h'];
        $labelColAttributes     = ['i' => 'j'];
        $expectedLegend         = '<legend></legend>';
        $expectedCol1           = '<col1></col1>';
        $expectedCol2           = '<col2></col2>';
        $expectedRow            = '<row></row>';
        $textDomain             = 'text-domain';

        $element = $this->createMock(Radio::class);
        $matcher = self::exactly(15);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes, $rowAttributes, $colAttributes, $labelAttributes, $labelColAttributes): bool | array | string | null {
                    match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 9, 11, 15 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('row_attributes', $option),
                        6 => self::assertSame('col_attributes', $option),
                        8 => self::assertSame('label_attributes', $option),
                        10 => self::assertSame('label_col_attributes', $option),
                        14 => self::assertSame('help_attributes', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 9, 11, 15 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        4 => $rowAttributes,
                        6 => $colAttributes,
                        8 => $labelAttributes,
                        10 => $labelColAttributes,
                        14 => $helpAttributes,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(6);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        3, 5 => self::assertSame('id', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        5, 6, 8 => self::assertSame('id', $key),
                        4, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        5, 6, 8 => $id,
                        4 => $aria,
                        7 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '        ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '        ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(5);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $id, $labelColAttributes, $labelAttributes, $helpAttributes, $colAttributes, $rowAttributes, $labelTranslatedEscaped, $helpContent, $expected, $expectedCol1, $expectedCol2, $expectedErrors, $expectedLegend, $expectedHelp, $expectedRow, $indent): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('legend', $element, (string) $invocation),
                        5 => self::assertSame('fieldset', $element, (string) $invocation),
                        default => self::assertSame('div', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $labelColAttributes + $labelAttributes + ['class' => 'col-form-label'],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $helpAttributes + ['id' => $id . 'Help'],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'form-control required'],
                            $attribs,
                            (string) $invocation,
                        ),
                        4 => self::assertSame($colAttributes, $attribs, (string) $invocation),
                        default => self::assertSame(
                            $rowAttributes + ['class' => 'row'],
                            $attribs,
                            (string) $invocation,
                        ),
                    };

                    match ($invocation) {
                        1 => self::assertSame($labelTranslatedEscaped, $content, (string) $invocation),
                        2 => self::assertSame($helpContent, $content, (string) $invocation),
                        3 => self::assertSame(
                            PHP_EOL . $expected . PHP_EOL . $indent . '        ',
                            $content,
                            (string) $invocation,
                        ),
                        4 => self::assertSame(
                            PHP_EOL . $indent . '        ' . $expectedCol1 . $expectedErrors . PHP_EOL . $indent . '        ' . $expectedHelp . PHP_EOL . $indent . '    ',
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedCol2 . PHP_EOL . $indent,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expectedLegend,
                        2 => $expectedHelp,
                        3 => $expectedCol1,
                        4 => $expectedCol2,
                        default => $expectedRow,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($labelTranslated, 0)
            ->willReturn($labelTranslatedEscaped);

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain, null)
            ->willReturn($labelTranslated);

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedRow, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderVerticalForm(): void
    {
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $messages               = ['x' => 'y'];
        $type                   = 'radio';
        $class                  = 'test-class';
        $indent                 = '<!-- -->  ';
        $expected               = '<hidden></hidden>';
        $expectedErrors         = '<errors></errors>';
        $renderErrors           = true;
        $required               = true;
        $showRequiredMark       = false;
        $layout                 = Form::LAYOUT_VERTICAL;
        $helpContent            = 'help';
        $helpAttributes         = ['a' => 'b'];
        $expectedHelp           = '<help></help>';
        $id                     = 'test-id';
        $aria                   = 'aria-described';
        $form                   = null;
        $colAttributes          = ['e' => 'f'];
        $labelAttributes        = ['g' => 'h'];
        $legendAttributes       = ['i' => 'j'];
        $expectedLegend         = '<legend></legend>';
        $expectedCol1           = '<col1></col1>';
        $expectedCol2           = '<col2></col2>';
        $textDomain             = 'text-domain';
        $disableEscape          = false;

        $element = $this->createMock(Radio::class);
        $matcher = self::exactly(14);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes, $colAttributes, $labelAttributes, $legendAttributes): bool | array | string | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 5, 7, 9, 14 => self::assertSame('form', $option, (string) $invocation),
                        2 => self::assertSame('show-required-mark', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('col_attributes', $option, (string) $invocation),
                        6 => self::assertSame('label_attributes', $option, (string) $invocation),
                        8 => self::assertSame('legend_attributes', $option, (string) $invocation),
                        13 => self::assertSame('help_attributes', $option, (string) $invocation),
                        10 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame('help_content', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1, 5, 7, 9, 14 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        4 => $colAttributes,
                        6 => $labelAttributes,
                        8 => $legendAttributes,
                        13 => $helpAttributes,
                        10 => false,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        5, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('id', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(10);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        4, 6, 7, 9 => self::assertSame('id', $key),
                        5, 8 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        4, 6, 7, 9 => $id,
                        5 => $aria,
                        8 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $id, $legendAttributes, $helpAttributes, $colAttributes, $labelTranslatedEscaped, $helpContent, $expected, $expectedCol1, $expectedCol2, $expectedErrors, $expectedLegend, $expectedHelp, $indent): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('legend', $element, (string) $invocation),
                        4 => self::assertSame('fieldset', $element, (string) $invocation),
                        default => self::assertSame('div', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            $legendAttributes + ['class' => 'form-label'],
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $helpAttributes + ['id' => $id . 'Help'],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'form-control required'],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame($colAttributes, $attribs, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame($labelTranslatedEscaped, $content, (string) $invocation),
                        2 => self::assertSame($helpContent, $content, (string) $invocation),
                        3 => self::assertSame(
                            PHP_EOL . $expected . PHP_EOL . $indent . '    ',
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedCol1 . $expectedErrors . PHP_EOL . $indent . '    ' . $expectedHelp . PHP_EOL . $indent . '',
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expectedLegend,
                        2 => $expectedHelp,
                        3 => $expectedCol1,
                        default => $expectedCol2,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($labelTranslated, 0)
            ->willReturn($labelTranslatedEscaped);

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain, null)
            ->willReturn($labelTranslated);

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedCol2, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderHorizontalForm2(): void
    {
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $messages               = ['x' => 'y'];
        $type                   = 'radio';
        $class                  = 'test-class';
        $indent                 = '<!-- -->  ';
        $expected               = '<hidden></hidden>';
        $expectedErrors         = '<errors></errors>';
        $renderErrors           = true;
        $required               = true;
        $showRequiredMark       = false;
        $layout                 = Form::LAYOUT_HORIZONTAL;
        $helpContent            = 'help';
        $helpAttributes         = ['a' => 'b'];
        $expectedHelp           = '<help></help>';
        $id                     = 'test-id';
        $aria                   = 'aria-described';
        $form                   = null;
        $rowAttributes          = ['c' => 'd'];
        $colAttributes          = ['e' => 'f'];
        $labelAttributes        = ['g' => 'h'];
        $labelColAttributes     = ['i' => 'j'];
        $expectedCol            = '<col1></col1>';
        $expectedRow            = '<row></row>';
        $textDomain             = 'text-domain';
        $disableEscape          = false;

        $element = $this->createMock(Button::class);
        $matcher = self::exactly(15);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes, $rowAttributes, $colAttributes, $labelAttributes, $labelColAttributes): bool | array | string | null {
                    match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 9, 11, 15 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('row_attributes', $option),
                        6 => self::assertSame('col_attributes', $option),
                        8 => self::assertSame('label_attributes', $option),
                        10 => self::assertSame('label_col_attributes', $option),
                        14 => self::assertSame('help_attributes', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 9, 11, 15 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        4 => $rowAttributes,
                        6 => $colAttributes,
                        8 => $labelAttributes,
                        10 => $labelColAttributes,
                        14 => $helpAttributes,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(6);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        3, 5 => self::assertSame('id', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(8);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        5, 6, 8 => self::assertSame('id', $key),
                        4, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        5, 6, 8 => $id,
                        4 => $aria,
                        7 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '        ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '        ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $id, $helpAttributes, $colAttributes, $rowAttributes, $helpContent, $expected, $expectedCol, $expectedErrors, $expectedRow, $expectedHelp, $indent): string {
                    self::assertSame('div', $element);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($helpAttributes + ['id' => $id . 'Help'], $attribs),
                        2 => self::assertSame($colAttributes, $attribs),
                        default => self::assertSame($rowAttributes + ['class' => 'row'], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($helpContent, $content),
                        2 => self::assertSame(
                            PHP_EOL . $expected . $expectedErrors . PHP_EOL . $indent . '        ' . $expectedHelp . PHP_EOL . $indent . '    ',
                            $content,
                        ),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedCol . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedHelp,
                        2 => $expectedCol,
                        default => $expectedRow,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($labelTranslated, 0)
            ->willReturn($labelTranslatedEscaped);

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain, null)
            ->willReturn($labelTranslated);

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedRow, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderVerticalForm2(): void
    {
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $messages               = ['x' => 'y'];
        $type                   = 'radio';
        $class                  = 'test-class';
        $indent                 = '<!-- -->  ';
        $expected               = '<hidden></hidden>';
        $expectedErrors         = '<errors></errors>';
        $renderErrors           = true;
        $required               = true;
        $showRequiredMark       = false;
        $layout                 = Form::LAYOUT_VERTICAL;
        $helpContent            = 'help';
        $helpAttributes         = ['a' => 'b'];
        $expectedHelp           = '<help></help>';
        $id                     = 'test-id';
        $aria                   = 'aria-described';
        $form                   = null;
        $colAttributes          = ['e' => 'f'];
        $labelAttributes        = ['g' => 'h'];
        $expectedCol            = '<col1></col1>';
        $textDomain             = 'text-domain';
        $disableEscape          = false;

        $element = $this->createMock(Button::class);
        $matcher = self::exactly(11);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes, $colAttributes, $labelAttributes): bool | array | string | null {
                    match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 11 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('col_attributes', $option),
                        6 => self::assertSame('label_attributes', $option),
                        10 => self::assertSame('help_attributes', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 11 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        4 => $colAttributes,
                        6 => $labelAttributes,
                        10 => $helpAttributes,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        5, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('id', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        4, 6, 7, 9 => self::assertSame('id', $key),
                        5, 8 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        4, 6, 7, 9 => $id,
                        5 => $aria,
                        8 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(2);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $id, $helpAttributes, $colAttributes, $helpContent, $expected, $expectedCol, $expectedErrors, $expectedHelp, $indent): string {
                    self::assertSame('div', $element);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($helpAttributes + ['id' => $id . 'Help'], $attribs),
                        default => self::assertSame($colAttributes, $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($helpContent, $content),
                        default => self::assertSame(
                            PHP_EOL . $expected . $expectedErrors . PHP_EOL . $indent . '    ' . $expectedHelp . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedHelp,
                        default => $expectedCol,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($labelTranslated, 0)
            ->willReturn($labelTranslatedEscaped);

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain, null)
            ->willReturn($labelTranslated);

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedCol, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderVerticalForm3(): void
    {
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $messages               = ['x' => 'y'];
        $type                   = 'radio';
        $class                  = 'test-class';
        $indent                 = '<!-- -->  ';
        $expected               = '<hidden></hidden>';
        $expectedErrors         = '<errors></errors>';
        $renderErrors           = true;
        $required               = true;
        $showRequiredMark       = false;
        $layout                 = Form::LAYOUT_VERTICAL;
        $helpContent            = 'help';
        $helpAttributes         = ['a' => 'b'];
        $expectedHelp           = '<help></help>';
        $id                     = 'test-id';
        $aria                   = 'aria-described';
        $form                   = null;
        $colAttributes          = ['e' => 'f'];
        $labelAttributes        = ['g' => 'h'];
        $legendAttributes       = ['i' => 'j', 'class' => 'legend-class'];
        $expectedLegend         = '<legend></legend>';
        $expectedCol1           = '<col1></col1>';
        $expectedCol2           = '<col2></col2>';
        $textDomain             = 'text-domain';
        $disableEscape          = false;

        $element = $this->createMock(Radio::class);
        $matcher = self::exactly(14);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes, $colAttributes, $labelAttributes, $legendAttributes): bool | array | string | null {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1, 5, 7, 9, 14 => self::assertSame('form', $option, (string) $invocation),
                        2 => self::assertSame('show-required-mark', $option, (string) $invocation),
                        3 => self::assertSame('layout', $option, (string) $invocation),
                        4 => self::assertSame('col_attributes', $option, (string) $invocation),
                        6 => self::assertSame('label_attributes', $option, (string) $invocation),
                        8 => self::assertSame('legend_attributes', $option, (string) $invocation),
                        13 => self::assertSame('help_attributes', $option, (string) $invocation),
                        10 => self::assertSame('floating', $option, (string) $invocation),
                        default => self::assertSame('help_content', $option, (string) $invocation),
                    };

                    return match ($invocation) {
                        1, 5, 7, 9, 14 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        4 => $colAttributes,
                        6 => $labelAttributes,
                        8 => $legendAttributes,
                        13 => $helpAttributes,
                        10 => false,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        5, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('id', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(10);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        4, 6, 7, 9 => self::assertSame('id', $key),
                        5, 8 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        4, 6, 7, 9 => $id,
                        5 => $aria,
                        8 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $id, $legendAttributes, $helpAttributes, $colAttributes, $labelTranslatedEscaped, $helpContent, $expected, $expectedCol1, $expectedCol2, $expectedErrors, $expectedLegend, $expectedHelp, $indent): string {
                    $invocation = $matcher->numberOfInvocations();

                    match ($invocation) {
                        1 => self::assertSame('legend', $element, (string) $invocation),
                        4 => self::assertSame('fieldset', $element, (string) $invocation),
                        default => self::assertSame('div', $element, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame(
                            array_merge($legendAttributes, ['class' => 'form-label legend-class']),
                            $attribs,
                            (string) $invocation,
                        ),
                        2 => self::assertSame(
                            $helpAttributes + ['id' => $id . 'Help'],
                            $attribs,
                            (string) $invocation,
                        ),
                        3 => self::assertSame(
                            ['class' => 'form-control required'],
                            $attribs,
                            (string) $invocation,
                        ),
                        default => self::assertSame($colAttributes, $attribs, (string) $invocation),
                    };

                    match ($invocation) {
                        1 => self::assertSame($labelTranslatedEscaped, $content, (string) $invocation),
                        2 => self::assertSame($helpContent, $content, (string) $invocation),
                        3 => self::assertSame(
                            PHP_EOL . $expected . PHP_EOL . $indent . '    ',
                            $content,
                            (string) $invocation,
                        ),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedCol1 . $expectedErrors . PHP_EOL . $indent . '    ' . $expectedHelp . PHP_EOL . $indent,
                            $content,
                            (string) $invocation,
                        ),
                    };

                    return match ($invocation) {
                        1 => $expectedLegend,
                        2 => $expectedHelp,
                        3 => $expectedCol1,
                        default => $expectedCol2,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($labelTranslated, 0)
            ->willReturn($labelTranslatedEscaped);

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain, null)
            ->willReturn($labelTranslated);

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedCol2, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderVerticalForm4(): void
    {
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $messages               = ['x' => 'y'];
        $type                   = 'radio';
        $class                  = 'test-class';
        $indent                 = '<!-- -->  ';
        $expected               = '<hidden></hidden>';
        $expectedErrors         = '<errors></errors>';
        $renderErrors           = true;
        $required               = true;
        $showRequiredMark       = false;
        $layout                 = Form::LAYOUT_VERTICAL;
        $helpContent            = 'help';
        $helpAttributes         = ['a' => 'b'];
        $expectedHelp           = '<help></help>';
        $id                     = 'test-id';
        $aria                   = 'aria-described';
        $form                   = null;
        $colAttributes          = ['e' => 'f'];
        $labelAttributes        = ['g' => 'h'];
        $expectedLegend         = '<legend></legend>';
        $expectedCol            = '<col1></col1>';
        $textDomain             = 'text-domain';
        $floating               = false;
        $labelPosition          = \Laminas\Form\View\Helper\FormRow::LABEL_APPEND;
        $disableEscape          = false;

        $element = $this->createMock(Text::class);
        $matcher = self::exactly(12);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes, $colAttributes, $labelAttributes, $floating): bool | array | string | null {
                    match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 12 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('col_attributes', $option),
                        6 => self::assertSame('label_attributes', $option),
                        8 => self::assertSame('floating', $option),
                        11 => self::assertSame('help_attributes', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 12 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        4 => $colAttributes,
                        6 => $labelAttributes,
                        8 => $floating,
                        11 => $helpAttributes,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        5, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('id', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        4, 6, 7, 9 => self::assertSame('id', $key),
                        5, 8 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        4, 6, 7, 9 => $id,
                        5 => $aria,
                        8 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);
        $element->expects(self::once())
            ->method('hasLabelOption')
            ->with('label_position')
            ->willReturn(true);
        $matcher = self::exactly(2);
        $element->expects($matcher)
            ->method('getLabelOption')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $disableEscape, $labelPosition): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('disable_html_escape', $key),
                        default => self::assertSame('label_position', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $disableEscape,
                         default => $labelPosition,
                    };
                },
            );

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '    ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $id, $labelAttributes, $helpAttributes, $colAttributes, $labelTranslatedEscaped, $helpContent, $expected, $expectedCol, $expectedErrors, $expectedHelp, $expectedLegend, $indent): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('label', $element),
                        default => self::assertSame('div', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            $labelAttributes + ['class' => 'form-label', 'for' => $id],
                            $attribs,
                        ),
                        2 => self::assertSame($helpAttributes + ['id' => $id . 'Help'], $attribs),
                        default => self::assertSame($colAttributes, $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($labelTranslatedEscaped, $content),
                        2 => self::assertSame($helpContent, $content),
                        default => self::assertSame(
                            PHP_EOL . $expected . PHP_EOL . $indent . '    ' . $expectedLegend . $expectedErrors . PHP_EOL . $indent . '    ' . $expectedHelp . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedLegend,
                        2 => $expectedHelp,
                        default => $expectedCol,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($labelTranslated, 0)
            ->willReturn($labelTranslatedEscaped);

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain, null)
            ->willReturn($labelTranslated);

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedCol, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function testRenderHorizontalForm3(): void
    {
        $label                  = 'test-label';
        $labelTranslated        = 'test-label-translated';
        $labelTranslatedEscaped = 'test-label-translated-escaped';
        $messages               = ['x' => 'y'];
        $type                   = 'radio';
        $class                  = 'test-class';
        $indent                 = '<!-- -->  ';
        $expected               = '<hidden></hidden>';
        $expectedErrors         = '<errors></errors>';
        $renderErrors           = true;
        $required               = true;
        $showRequiredMark       = false;
        $layout                 = Form::LAYOUT_HORIZONTAL;
        $helpContent            = 'help';
        $helpAttributes         = ['a' => 'b'];
        $expectedHelp           = '<help></help>';
        $id                     = 'test-id';
        $aria                   = 'aria-described';
        $form                   = null;
        $rowAttributes          = ['c' => 'd'];
        $colAttributes          = ['e' => 'f'];
        $labelAttributes        = ['g' => 'h'];
        $labelColAttributes     = ['i' => 'j'];
        $expectedLegend         = '<legend></legend>';
        $expectedCol            = '<col1></col1>';
        $expectedRow            = '<row></row>';
        $textDomain             = 'text-domain';
        $disableEscape          = false;

        $element = $this->createMock(Text::class);
        $matcher = self::exactly(15);
        $element->expects($matcher)
            ->method('getOption')
            ->willReturnCallback(
                static function (string $option) use ($matcher, $form, $showRequiredMark, $layout, $helpContent, $helpAttributes, $rowAttributes, $colAttributes, $labelAttributes, $labelColAttributes): bool | array | string | null {
                    match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 9, 11, 15 => self::assertSame('form', $option),
                        2 => self::assertSame('show-required-mark', $option),
                        3 => self::assertSame('layout', $option),
                        4 => self::assertSame('row_attributes', $option),
                        6 => self::assertSame('col_attributes', $option),
                        8 => self::assertSame('label_attributes', $option),
                        10 => self::assertSame('label_col_attributes', $option),
                        14 => self::assertSame('help_attributes', $option),
                        default => self::assertSame('help_content', $option),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1, 5, 7, 9, 11, 15 => $form,
                        2 => $showRequiredMark,
                        3 => $layout,
                        4 => $rowAttributes,
                        6 => $colAttributes,
                        8 => $labelAttributes,
                        10 => $labelColAttributes,
                        14 => $helpAttributes,
                        default => $helpContent,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getName')
            ->willReturn('x');
        $matcher = self::exactly(7);
        $element->expects($matcher)
            ->method('hasAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('required', $key),
                        2 => self::assertSame('class', $key),
                        5, 7 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('id', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );
        $matcher = self::exactly(3);
        $element->expects($matcher)
            ->method('setAttribute')
            ->willReturnCallback(
                static function (string $key, mixed $value) use ($matcher, $class, $id, $aria): void {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('class', $key),
                        default => self::assertSame('aria-describedby', $key),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($class . ' is-invalid', $value),
                        2 => self::assertSame($aria . ' ' . $id . 'Feedback', $value),
                        default => self::assertSame(
                            $aria . ' ' . $id . 'Feedback ' . $id . 'Help',
                            $value,
                        ),
                    };
                },
            );
        $matcher = self::exactly(9);
        $element->expects($matcher)
            ->method('getAttribute')
            ->willReturnCallback(
                static function (string $key) use ($matcher, $type, $class, $required, $id, $aria): string | bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('type', $key),
                        2 => self::assertSame('class', $key),
                        4, 6, 7, 9 => self::assertSame('id', $key),
                        5, 8 => self::assertSame('aria-describedby', $key),
                        default => self::assertSame('required', $key),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $type,
                        2 => $class,
                        4, 6, 7, 9 => $id,
                        5 => $aria,
                        8 => $aria . ' ' . $id . 'Feedback',
                        default => $required,
                    };
                },
            );
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn($disableEscape);

        $formElement = $this->createMock(FormElementInterface::class);
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent . '        ');
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->createMock(FormElementErrorsInterface::class);
        $formElementErrors->expects(self::once())
            ->method('setIndent')
            ->with($indent . '        ');
        $formElementErrors->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expectedErrors);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher     = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                static function (string $element, array $attribs, string $content) use ($matcher, $id, $labelAttributes, $labelColAttributes, $rowAttributes, $helpAttributes, $colAttributes, $labelTranslatedEscaped, $helpContent, $expected, $expectedCol, $expectedErrors, $expectedHelp, $expectedLegend, $expectedRow, $indent): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame('label', $element),
                        default => self::assertSame('div', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(
                            $labelColAttributes + $labelAttributes + ['class' => 'col-form-label', 'for' => $id],
                            $attribs,
                        ),
                        2 => self::assertSame($helpAttributes + ['id' => $id . 'Help'], $attribs),
                        3 => self::assertSame($colAttributes, $attribs),
                        default => self::assertSame($rowAttributes + ['class' => 'row'], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($labelTranslatedEscaped, $content),
                        2 => self::assertSame($helpContent, $content),
                        3 => self::assertSame(
                            PHP_EOL . $expected . $expectedErrors . PHP_EOL . $indent . '        ' . $expectedHelp . PHP_EOL . $indent . '    ',
                            $content,
                        ),
                        default => self::assertSame(
                            PHP_EOL . $indent . '    ' . $expectedLegend . PHP_EOL . $indent . '    ' . $expectedCol . PHP_EOL . $indent,
                            $content,
                        ),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $expectedLegend,
                        2 => $expectedHelp,
                        3 => $expectedCol,
                        default => $expectedRow,
                    };
                },
            );

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($labelTranslated, 0)
            ->willReturn($labelTranslatedEscaped);

        $renderer = $this->createMock(PartialRendererInterface::class);
        $renderer->expects(self::never())
            ->method('render');

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($label, $textDomain, null)
            ->willReturn($labelTranslated);

        $helper = new FormRow(
            $formElement,
            $formElementErrors,
            $htmlElement,
            $escapeHtml,
            $renderer,
            $translator,
        );

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($indent . $expectedRow, $helper->render($element));
    }
}
