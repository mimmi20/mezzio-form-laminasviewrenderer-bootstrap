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

use AssertionError;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrorsInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormRow;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class FormRowTest extends TestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     */
    public function testRenderWithWrongFormOption(): void
    {
        $formElement = $this->getMockBuilder(FormElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->getMockBuilder(FormElementErrorsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects(self::never())
            ->method('render');

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $element = $this->getMockBuilder(ElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::never())
            ->method('getLabel');
        $element->expects(self::never())
            ->method('getMessages');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('$form should be an Instance of Laminas\Form\FormInterface or null, but was boolean');

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     */
    public function testRenderHiddenWithoutFormOptionAndLabel(): void
    {
        $label        = '';
        $messages     = [];
        $type         = 'hidden';
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $renderErrors = false;

        $element = $this->getMockBuilder(ElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::never())
            ->method('hasAttribute');
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

        $formElement = $this->getMockBuilder(FormElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElement->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $formElement->expects(self::once())
            ->method('render')
            ->with($element)
            ->willReturn($expected);

        $formElementErrors = $this->getMockBuilder(FormElementErrorsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     */
    public function testRenderPartialWithoutFormOptionAndLabel(): void
    {
        $label        = '';
        $messages     = [];
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->getMockBuilder(ElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::never())
            ->method('setAttribute');
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->getMockBuilder(FormElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->getMockBuilder(FormElementErrorsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
                ]
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
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     */
    public function testRenderPartialWithoutFormOptionAndLabel2(): void
    {
        $label        = '';
        $messages     = ['x' => 'y'];
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;

        $element = $this->getMockBuilder(ElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('class')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', 'is-invalid');
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->getMockBuilder(FormElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->getMockBuilder(FormElementErrorsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
                ]
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
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     */
    public function testRenderPartialWithoutFormOptionAndLabel3(): void
    {
        $label        = '';
        $messages     = ['x' => 'y'];
        $indent       = '<!-- -->  ';
        $expected     = '<hidden></hidden>';
        $partial      = 'test-partial';
        $renderErrors = false;
        $class        = 'test-class';

        $element = $this->getMockBuilder(ElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getOption')
            ->with('form')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('getName');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('class')
            ->willReturn(true);
        $element->expects(self::once())
            ->method('setAttribute')
            ->with('class', $class . ' is-invalid');
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('class')
            ->willReturn($class);
        $element->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn($messages);

        $formElement = $this->getMockBuilder(FormElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElement->expects(self::never())
            ->method('setIndent');
        $formElement->expects(self::never())
            ->method('render');

        $formElementErrors = $this->getMockBuilder(FormElementErrorsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElementErrors->expects(self::never())
            ->method('setIndent');
        $formElementErrors->expects(self::never())
            ->method('render');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $renderer = $this->getMockBuilder(PartialRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
                ]
            )
            ->willReturn($expected);

        $helper = new FormRow($formElement, $formElementErrors, $htmlElement, $escapeHtml, $renderer);

        $helper->setIndent($indent);
        $helper->setRenderErrors($renderErrors);
        $helper->setPartial($partial);

        self::assertSame($expected, $helper->render($element));
    }
}
