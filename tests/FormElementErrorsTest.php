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

use Laminas\Form\Element\Text;
use Laminas\Form\Exception\DomainException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormElementErrors;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;
use function sprintf;

use const PHP_EOL;

final class FormElementErrorsTest extends TestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testRenderWithoutMessages(): void
    {
        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
     * @throws InvalidArgumentException
     */
    public function testRenderWithMessages(): void
    {
        $message          = 'too long';
        $messageEscaped   = 'too long, but escaped';
        $listEntryMessage = sprintf('<li>%s</li>', $messageEscaped);
        $listMessage      = sprintf('<ul>%s</ul>', $listEntryMessage);
        $divMessage       = sprintf('<div>%s</div>', $listMessage);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message)
            ->willReturn($messageEscaped);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::exactly(3))
            ->method('toHtml')
            ->withConsecutive(['li', [], $messageEscaped], ['ul', [], '        ' . $listEntryMessage . PHP_EOL . '    '], ['div', ['class' => 'invalid-feedback'], '    ' . $listMessage])
            ->willReturnOnConsecutiveCalls($listEntryMessage, $listMessage, $divMessage);

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
     * @throws InvalidArgumentException
     */
    public function testRenderWithEmptyMessages(): void
    {
        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::never())
            ->method('toHtml');

        $helper = new FormElementErrors($htmlElement, $escapeHtml, null);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
     * @throws InvalidArgumentException
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

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$message1Translated], [$message2Translated])
            ->willReturnOnConsecutiveCalls($message1TranslatedEscaped, $message2TranslatedEscaped);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::exactly(4))
            ->method('toHtml')
            ->withConsecutive(['li', [], $message1TranslatedEscaped], ['li', [], $message2TranslatedEscaped], ['ul', [], '        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    '], ['div', ['class' => 'invalid-feedback'], '    ' . $listMessage])
            ->willReturnOnConsecutiveCalls($listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage);

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$message1, $textDomain], [$message2, $textDomain])
            ->willReturn($message1Translated, $message2Translated);

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
     * @throws InvalidArgumentException
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

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message1Translated)
            ->willReturn($message1TranslatedEscaped);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::exactly(4))
            ->method('toHtml')
            ->withConsecutive(['li', [], $message1TranslatedEscaped], ['li', [], $message2Translated], ['ul', [], '        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    '], ['div', ['class' => 'invalid-feedback'], '    ' . $listMessage])
            ->willReturnOnConsecutiveCalls($listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage);

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$message1, $textDomain], [$message2, $textDomain])
            ->willReturn($message1Translated, $message2Translated);

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
     * @throws InvalidArgumentException
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

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message1Translated)
            ->willReturn($message1TranslatedEscaped);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::exactly(4))
            ->method('toHtml')
            ->withConsecutive(['li', [], $message1TranslatedEscaped], ['li', [], $message2Translated], ['ul', [], '        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    '], ['div', ['class' => 'invalid-feedback'], '    ' . $listMessage])
            ->willReturnOnConsecutiveCalls($listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage);

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$message1, $textDomain], [$message2, $textDomain])
            ->willReturn($message1Translated, $message2Translated);

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
     * @throws InvalidArgumentException
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

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($message1Translated)
            ->willReturn($message1TranslatedEscaped);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::exactly(4))
            ->method('toHtml')
            ->withConsecutive(['li', [], $message1TranslatedEscaped], ['li', [], $message2Translated], ['ul', [], '        ' . $listEntryMessage1 . PHP_EOL . '        ' . $listEntryMessage2 . PHP_EOL . '    '], ['div', ['class' => 'invalid-feedback'], '    ' . $listMessage])
            ->willReturnOnConsecutiveCalls($listEntryMessage1, $listEntryMessage2, $listMessage, $divMessage);

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$message1, $textDomain], [$message2, $textDomain])
            ->willReturn($message1Translated, $message2Translated);

        $helper = new FormElementErrors($htmlElement, $escapeHtml, $translator);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
}
