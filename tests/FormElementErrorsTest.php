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
use Laminas\View\Exception\DomainException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\Escaper\AbstractHelper;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrors;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;
use function sprintf;

use const PHP_EOL;

final class FormElementErrorsTest extends TestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithoutMessages(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn([]);
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::never())
            ->method('getLabelOption');

        self::assertSame('', $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithMessages(): void
    {
        $message          = 'too long';
        $messageEscaped   = 'too long, but escaped';
        $listEntryMessage = sprintf('<li>%s</li>', $messageEscaped);
        $listMessage      = sprintf('<ul>%s</ul>', $listEntryMessage);
        $divMessage       = sprintf('<div>%s</div>', $listMessage);

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message)
            ->willReturn($messageEscaped);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher = self::exactly(3);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                function (string $element, array $attribs, string $content) use ($matcher, $messageEscaped, $listEntryMessage, $listMessage, $divMessage): string
                {
                    match ($matcher->numberOfInvocations()) {
                        3 => self::assertSame('div', $element),
                        2 => self::assertSame('ul', $element),
                        default => self::assertSame('li', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        3 => self::assertSame(['class' => 'invalid-feedback'], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($messageEscaped, $content),
                        2 => self::assertSame('        ' . $listEntryMessage . PHP_EOL . '    ', $content),
                        default => self::assertSame('    ' . $listMessage, $content),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $listEntryMessage,
                        2 => $listMessage,
                        default => $divMessage,
                    };
                }
            );

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn(['x1' => $message, 'x2' => '']);
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $element->expects(self::once())
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn(false);

        self::assertSame($divMessage, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithEmptyMessages(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn(['', '']);
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::never())
            ->method('hasAttribute');
        $element->expects(self::never())
            ->method('getLabelOption');

        self::assertSame('', $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithMessagesAndTranslator(): void
    {
        $message1                  = 'too long';
        $message1Translated        = 'too long, but translated';
        $message1TranslatedEscaped = 'too long, but translated and escaped';
        $message2                  = 'too short';
        $message2Translated        = 'too short, but translated';
        $message2TranslatedEscaped = 'too short, but translated and escaped';
        $listEntryMessage1         = sprintf('<li>%s</li>', $message1TranslatedEscaped);
        $listEntryMessage2         = sprintf('<li>%s</li>', $message2TranslatedEscaped);
        $listMessage               = sprintf('<ul>%s%s</ul>', $listEntryMessage1, $listEntryMessage2);
        $divMessage                = sprintf('<div>%s</div>', $listMessage);
        $textDomain                = 'test-domain';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $matcher = self::exactly(2);
        $escapeHtml->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                function(string $value, int $recurse = AbstractHelper::RECURSE_NONE) use ($matcher, $message1Translated, $message2Translated, $message1TranslatedEscaped, $message2TranslatedEscaped): string
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1Translated, $value),
                        default => self::assertSame($message2Translated, $value),
                    };

                    self::assertSame(0, $recurse);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $message1TranslatedEscaped,
                        default => $message2TranslatedEscaped,
                    };
                }
            );

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                function (string $element, array $attribs, string $content) use ($matcher, $message1TranslatedEscaped, $message2TranslatedEscaped, $listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage): string
                {
                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame('div', $element),
                        3 => self::assertSame('ul', $element),
                        default => self::assertSame('li', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame(['class' => 'invalid-feedback'], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1TranslatedEscaped, $content),
                        2 => self::assertSame($message2TranslatedEscaped, $content),
                        3 => self::assertSame('        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    ', $content),
                        default => self::assertSame('    ' . $listMessage, $content),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $listEntryMessage1,
                        2 => $listEntryMessage2,
                        3 => $listMessage,
                        default => $divMessage,
                    };
                }
            );

        $translator = $this->createMock(Translate::class);
        $matcher = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                function (string $message, ?string $textDomainParam = null, ?string $locale = null) use ($matcher, $message1, $message2, $textDomain, $message1Translated, $message2Translated): string
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1, $message),
                        default => self::assertSame($message2, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $message1Translated,
                        default => $message2Translated,
                    };
                }
            );

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn(['x1' => $message1, 'x2' => '', 'x3' => [$message2, '']]);
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $element->expects(self::exactly(2))
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturn(false);

        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($divMessage, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testRenderWithMessagesAndTranslatorWithoutEscape(): void
    {
        $message1                  = 'too long';
        $message1Translated        = 'too long, but translated';
        $message1TranslatedEscaped = 'too long, but translated and escaped';
        $message2                  = 'too short';
        $message2Translated        = 'too short, but translated';
        $listEntryMessage1         = sprintf('<li>%s</li>', $message1TranslatedEscaped);
        $listEntryMessage2         = sprintf('<li>%s</li>', $message2Translated);
        $listMessage               = sprintf('<ul>%s%s</ul>', $listEntryMessage1, $listEntryMessage2);
        $divMessage                = sprintf('<div>%s</div>', $listMessage);
        $textDomain                = 'test-domain';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message1Translated)
            ->willReturn($message1TranslatedEscaped);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                function (string $element, array $attribs, string $content) use ($matcher, $message1TranslatedEscaped, $message2Translated, $listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage): string
                {
                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame('div', $element),
                        3 => self::assertSame('ul', $element),
                        default => self::assertSame('li', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame(['class' => 'invalid-feedback'], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1TranslatedEscaped, $content),
                        2 => self::assertSame($message2Translated, $content),
                        3 => self::assertSame('        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    ', $content),
                        default => self::assertSame('    ' . $listMessage, $content),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $listEntryMessage1,
                        2 => $listEntryMessage2,
                        3 => $listMessage,
                        default => $divMessage,
                    };
                }
            );

        $translator = $this->createMock(Translate::class);
        $matcher = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                function (string $message, ?string $textDomainParam = null, ?string $locale = null) use ($matcher, $message1, $message2, $textDomain, $message1Translated, $message2Translated): string
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1, $message),
                        default => self::assertSame($message2, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $message1Translated,
                        default => $message2Translated,
                    };
                }
            );

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn(['x1' => $message1, 'x2' => '', 'x3' => [$message2, '']]);
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $element->expects(self::exactly(2))
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturnOnConsecutiveCalls(false, true);

        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($divMessage, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws DomainException
     */
    public function testInvokeWithMessagesAndTranslatorWithoutEscape1(): void
    {
        $message1                  = 'too long';
        $message1Translated        = 'too long, but translated';
        $message1TranslatedEscaped = 'too long, but translated and escaped';
        $message2                  = 'too short';
        $message2Translated        = 'too short, but translated';
        $listEntryMessage1         = sprintf('<li>%s</li>', $message1TranslatedEscaped);
        $listEntryMessage2         = sprintf('<li>%s</li>', $message2Translated);
        $listMessage               = sprintf('<ul>%s%s</ul>', $listEntryMessage1, $listEntryMessage2);
        $divMessage                = sprintf('<div>%s</div>', $listMessage);
        $textDomain                = 'test-domain';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message1Translated)
            ->willReturn($message1TranslatedEscaped);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                function (string $element, array $attribs, string $content) use ($matcher, $message1TranslatedEscaped, $message2Translated, $listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage): string
                {
                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame('div', $element),
                        3 => self::assertSame('ul', $element),
                        default => self::assertSame('li', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame(['class' => 'invalid-feedback'], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1TranslatedEscaped, $content),
                        2 => self::assertSame($message2Translated, $content),
                        3 => self::assertSame('        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    ', $content),
                        default => self::assertSame('    ' . $listMessage, $content),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $listEntryMessage1,
                        2 => $listEntryMessage2,
                        3 => $listMessage,
                        default => $divMessage,
                    };
                }
            );

        $translator = $this->createMock(Translate::class);
        $matcher = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                function (string $message, ?string $textDomainParam = null, ?string $locale = null) use ($matcher, $message1, $message2, $textDomain, $message1Translated, $message2Translated): string
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1, $message),
                        default => self::assertSame($message2, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $message1Translated,
                        default => $message2Translated,
                    };
                }
            );

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn(['x1' => $message1, 'x2' => '', 'x3' => [$message2, '']]);
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $element->expects(self::exactly(2))
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturnOnConsecutiveCalls(false, true);

        $helper->setTranslatorTextDomain($textDomain);

        $helperObject = $helper();

        assert($helperObject instanceof FormElementErrors);

        self::assertSame($divMessage, $helperObject->render($element));
    }

    /**
     * @throws Exception
     * @throws \Laminas\View\Exception\DomainException
     */
    public function testInvokeWithMessagesAndTranslatorWithoutEscape2(): void
    {
        $message1                  = 'too long';
        $message1Translated        = 'too long, but translated';
        $message1TranslatedEscaped = 'too long, but translated and escaped';
        $message2                  = 'too short';
        $message2Translated        = 'too short, but translated';
        $listEntryMessage1         = sprintf('<li>%s</li>', $message1TranslatedEscaped);
        $listEntryMessage2         = sprintf('<li>%s</li>', $message2Translated);
        $listMessage               = sprintf('<ul>%s%s</ul>', $listEntryMessage1, $listEntryMessage2);
        $divMessage                = sprintf('<div>%s</div>', $listMessage);
        $textDomain                = 'test-domain';

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message1Translated)
            ->willReturn($message1TranslatedEscaped);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                function (string $element, array $attribs, string $content) use ($matcher, $message1TranslatedEscaped, $message2Translated, $listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage): string
                {
                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame('div', $element),
                        3 => self::assertSame('ul', $element),
                        default => self::assertSame('li', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame(['class' => 'invalid-feedback'], $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1TranslatedEscaped, $content),
                        2 => self::assertSame($message2Translated, $content),
                        3 => self::assertSame('        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    ', $content),
                        default => self::assertSame('    ' . $listMessage, $content),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $listEntryMessage1,
                        2 => $listEntryMessage2,
                        3 => $listMessage,
                        default => $divMessage,
                    };
                }
            );

        $translator = $this->createMock(Translate::class);
        $matcher = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                function (string $message, ?string $textDomainParam = null, ?string $locale = null) use ($matcher, $message1, $message2, $textDomain, $message1Translated, $message2Translated): string
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1, $message),
                        default => self::assertSame($message2, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $message1Translated,
                        default => $message2Translated,
                    };
                }
            );

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn(['x1' => $message1, 'x2' => '', 'x3' => [$message2, '']]);
        $element->expects(self::never())
            ->method('getAttribute');
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(false);
        $element->expects(self::exactly(2))
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturnOnConsecutiveCalls(false, true);

        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($divMessage, $helper($element));
    }

    /**
     * @throws Exception
     */
    public function testSetGetAttributes(): void
    {
        $attributes = ['class' => 'xyz', 'data-message' => 'void'];

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        self::assertSame($helper, $helper->setAttributes($attributes));
        self::assertSame($attributes, $helper->getAttributes());
    }

    /**
     * @throws Exception
     * @throws \Laminas\View\Exception\DomainException
     */
    public function testInvokeWithMessagesAndTranslatorWithoutEscape3(): void
    {
        $id                        = 'test-id';
        $message1                  = 'too long';
        $message1Translated        = 'too long, but translated';
        $message1TranslatedEscaped = 'too long, but translated and escaped';
        $message2                  = 'too short';
        $message2Translated        = 'too short, but translated';
        $listEntryMessage1         = sprintf('<li>%s</li>', $message1TranslatedEscaped);
        $listEntryMessage2         = sprintf('<li>%s</li>', $message2Translated);
        $listMessage               = sprintf('<ul>%s%s</ul>', $listEntryMessage1, $listEntryMessage2);
        $divMessage                = sprintf('<div>%s</div>', $listMessage);
        $textDomain                = 'test-domain';
        $attributes                = ['class' => 'xyz', 'data-message' => 'void'];

        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message1Translated)
            ->willReturn($message1TranslatedEscaped);

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $matcher = self::exactly(4);
        $htmlElement->expects($matcher)
            ->method('toHtml')
            ->willReturnCallback(
                function (string $element, array $attribs, string $content) use ($matcher, $attributes, $message1TranslatedEscaped, $message2Translated, $listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage): string
                {
                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame('div', $element),
                        3 => self::assertSame('ul', $element),
                        default => self::assertSame('li', $element),
                    };

                    match ($matcher->numberOfInvocations()) {
                        4 => self::assertSame(['class' => 'invalid-feedback', 'id' => 'test-idFeedback'], $attribs),
                        3 => self::assertSame($attributes, $attribs),
                        default => self::assertSame([], $attribs),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1TranslatedEscaped, $content),
                        2 => self::assertSame($message2Translated, $content),
                        3 => self::assertSame('        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    ', $content),
                        default => self::assertSame('    ' . $listMessage, $content),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $listEntryMessage1,
                        2 => $listEntryMessage2,
                        3 => $listMessage,
                        default => $divMessage,
                    };
                }
            );

        $translator = $this->createMock(Translate::class);
        $matcher = self::exactly(2);
        $translator->expects($matcher)
            ->method('__invoke')
            ->willReturnCallback(
                function (string $message, ?string $textDomainParam = null, ?string $locale = null) use ($matcher, $message1, $message2, $textDomain, $message1Translated, $message2Translated): string
                {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($message1, $message),
                        default => self::assertSame($message2, $message),
                    };

                    self::assertSame($textDomain, $textDomainParam);
                    self::assertNull($locale);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $message1Translated,
                        default => $message2Translated,
                    };
                }
            );

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->createMock(Text::class);
        $element->expects(self::once())
            ->method('getMessages')
            ->willReturn(['x1' => $message1, 'x2' => '', 'x3' => [$message2, '']]);
        $element->expects(self::once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($id);
        $element->expects(self::once())
            ->method('hasAttribute')
            ->with('id')
            ->willReturn(true);
        $element->expects(self::exactly(2))
            ->method('getLabelOption')
            ->with('disable_html_escape')
            ->willReturnOnConsecutiveCalls(false, true);

        $helper->setTranslatorTextDomain($textDomain);
        $helper->setAttributes($attributes);

        self::assertSame($divMessage, $helper($element));
    }

    /**
     * @throws Exception
     */
    public function testSetGetInden1(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        self::assertSame($helper, $helper->setIndent(4));
        self::assertSame('    ', $helper->getIndent());
    }

    /**
     * @throws Exception
     */
    public function testSetGetInden2(): void
    {
        $escapeHtml = $this->createMock(EscapeHtml::class);
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->createMock(HtmlElementInterface::class);
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        self::assertSame($helper, $helper->setIndent('  '));
        self::assertSame('  ', $helper->getIndent());
    }
}
