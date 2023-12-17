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

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\Element\Text;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormParagraph;
use Mimmi20\Form\Paragraph\Element\ParagraphInterface as ParagraphElement;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;
use function sprintf;

final class FormParagraphTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testRenderWithWrongElement(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $helper = new FormParagraph($escapeHtml, null);

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
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormParagraph::render',
                ParagraphElement::class
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     *
     */
    public function testRenderEmptyString(): void
    {
        $class      = 'test-class';
        $ariaLabel  = 'test';
        $attributes = ['class' => $class, 'aria-label' => $ariaLabel];
        $expected   = sprintf('<p aria-label="%s" class="%s"></p>', $ariaLabel, $class);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $helper = new FormParagraph($escapeHtml, null);

        $element = $this->createMock(ParagraphElement::class);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::once())
            ->method('getText')
            ->willReturn('');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     *
     */
    public function testRenderText(): void
    {
        $text        = 'test-text';
        $textEscaped = 'test-text-escaped';
        $class       = 'test-class';
        $ariaLabel   = 'test';
        $attributes  = ['class' => $class, 'aria-label' => $ariaLabel];
        $expected    = sprintf('<p aria-label="%s" class="%s">%s</p>', $ariaLabel, $class, $textEscaped);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($text)
            ->willReturn($textEscaped);

        $helper = new FormParagraph($escapeHtml, null);

        $element = $this->createMock(ParagraphElement::class);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::once())
            ->method('getText')
            ->willReturn($text);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     *
     */
    public function testRenderTextWithIndent(): void
    {
        $text        = 'test-text';
        $textEscaped = 'test-text-escaped';
        $class       = 'test-class';
        $ariaLabel   = 'test';
        $attributes  = ['class' => $class, 'aria-label' => $ariaLabel];
        $indent      = '    ';

        $expected = $indent . sprintf('<p aria-label="%s" class="%s">%s</p>', $ariaLabel, $class, $textEscaped);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($text)
            ->willReturn($textEscaped);

        $helper = new FormParagraph($escapeHtml, null);

        $element = $this->createMock(ParagraphElement::class);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::once())
            ->method('getText')
            ->willReturn($text);

        $helper->setIndent($indent);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     *
     */
    public function testRenderTextWithTranslator(): void
    {
        $textDomain           = 'test-domain';
        $text                 = 'test-text';
        $textTranlated        = 'test-text-translated';
        $textTranlatedEscaped = 'test-text-translated-escaped';
        $class                = 'test-class';
        $ariaLabel            = 'test';
        $attributes           = ['class' => $class, 'aria-label' => $ariaLabel];

        $expected = sprintf('<p aria-label="%s" class="%s">%s</p>', $ariaLabel, $class, $textTranlatedEscaped);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($textTranlated)
            ->willReturn($textTranlatedEscaped);

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($text, $textDomain)
            ->willReturn($textTranlated);

        $helper = new FormParagraph($escapeHtml, $translator);

        $element = $this->createMock(ParagraphElement::class);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::once())
            ->method('getText')
            ->willReturn($text);

        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     *
     */
    public function testInvokeTextWithTranslator1(): void
    {
        $textDomain           = 'test-domain';
        $text                 = 'test-text';
        $textTranlated        = 'test-text-translated';
        $textTranlatedEscaped = 'test-text-translated-escaped';
        $class                = 'test-class';
        $ariaLabel            = 'test';
        $attributes           = ['class' => $class, 'aria-label' => $ariaLabel];

        $expected = sprintf('<p aria-label="%s" class="%s">%s</p>', $ariaLabel, $class, $textTranlatedEscaped);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($textTranlated)
            ->willReturn($textTranlatedEscaped);

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($text, $textDomain)
            ->willReturn($textTranlated);

        $helper = new FormParagraph($escapeHtml, $translator);

        $element = $this->createMock(ParagraphElement::class);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::once())
            ->method('getText')
            ->willReturn($text);

        $helper->setTranslatorTextDomain($textDomain);

        $helperObject = $helper();

        assert($helperObject instanceof FormParagraph);

        self::assertSame($expected, $helperObject->render($element));
    }

    /**
     * @throws Exception
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     */
    public function testInvokeTextWithTranslator2(): void
    {
        $textDomain           = 'test-domain';
        $text                 = 'test-text';
        $textTranlated        = 'test-text-translated';
        $textTranlatedEscaped = 'test-text-translated-escaped';
        $class                = 'test-class';
        $ariaLabel            = 'test';
        $attributes           = ['class' => $class, 'aria-label' => $ariaLabel];

        $expected = sprintf('<p aria-label="%s" class="%s">%s</p>', $ariaLabel, $class, $textTranlatedEscaped);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($textTranlated)
            ->willReturn($textTranlatedEscaped);

        $translator = $this->createMock(Translate::class);
        $translator->expects(self::once())
            ->method('__invoke')
            ->with($text, $textDomain)
            ->willReturn($textTranlated);

        $helper = new FormParagraph($escapeHtml, $translator);

        $element = $this->createMock(ParagraphElement::class);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);
        $element->expects(self::never())
            ->method('getValue');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::once())
            ->method('getText')
            ->willReturn($text);

        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper($element));
    }

    /**
     * @throws Exception
     *
     */
    public function testSetGetIndent1(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $helper = new FormParagraph($escapeHtml, null);

        self::assertSame($helper, $helper->setIndent(4));
        self::assertSame('    ', $helper->getIndent());
    }

    /**
     * @throws Exception
     *
     */
    public function testSetGetIndent2(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $helper = new FormParagraph($escapeHtml, null);

        self::assertSame($helper, $helper->setIndent('  '));
        self::assertSame('  ', $helper->getIndent());
    }
}
