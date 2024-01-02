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

use IntlDateFormatter;
use Laminas\Form\Element\MonthSelect as MonthSelectElement;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\ExtensionNotLoadedException;
use Laminas\Form\Exception\InvalidArgumentException;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormMonthSelect;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelectInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;
use function date;
use function sprintf;

use const PHP_EOL;

final class FormMonthSelectTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderWithWrongElement(): void
    {
        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(Text::class);
        $element->expects(self::never())
            ->method('getName');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element is of type %s',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormMonthSelect::render',
                MonthSelectElement::class,
            ),
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderWithoutName(): void
    {
        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('shouldRenderDelimiters');
        $element->expects(self::never())
            ->method('getMinYear');
        $element->expects(self::never())
            ->method('getMaxYear');
        $element->expects(self::never())
            ->method('getMonthElement');
        $element->expects(self::never())
            ->method('getYearElement');
        $element->expects(self::never())
            ->method('shouldCreateEmptyOption');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormMonthSelect::render',
            ),
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testInvokeWithoutName1(): void
    {
        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('shouldRenderDelimiters');
        $element->expects(self::never())
            ->method('getMinYear');
        $element->expects(self::never())
            ->method('getMaxYear');
        $element->expects(self::never())
            ->method('getMonthElement');
        $element->expects(self::never())
            ->method('getYearElement');
        $element->expects(self::never())
            ->method('shouldCreateEmptyOption');

        $helperObject = $helper();

        assert($helperObject instanceof FormMonthSelect);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormMonthSelect::render',
            ),
        );
        $this->expectExceptionCode(0);

        $helperObject->render($element);
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testInvokeWithoutName2(): void
    {
        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('shouldRenderDelimiters');
        $element->expects(self::never())
            ->method('getMinYear');
        $element->expects(self::never())
            ->method('getMaxYear');
        $element->expects(self::never())
            ->method('getMonthElement');
        $element->expects(self::never())
            ->method('getYearElement');
        $element->expects(self::never())
            ->method('shouldCreateEmptyOption');

        $locale = 'de_DE';

        try {
            $helper($element, IntlDateFormatter::FULL, $locale);
            self::fail('expecting throwing an exception');
        } catch (DomainException) {
            self::assertSame(IntlDateFormatter::LONG, $helper->getDateType());
            self::assertSame($locale, $helper->getLocale());
        }
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRender1(): void
    {
        $name                    = 'test-name';
        $renderDelimiters        = true;
        $shouldCreateEmptyOption = true;
        $minYear                 = (int) date('Y') - 2;
        $maxYear                 = (int) date('Y') + 2;
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedMonth . PHP_EOL . ' ' . PHP_EOL . $renderedYear . PHP_EOL;

        $monthElement = $this->createMock(Select::class);
        $monthElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => [
                        'value' => '01',
                        'label' => 'Januar',
                    ],
                    '02' => [
                        'value' => '02',
                        'label' => 'Februar',
                    ],
                    '03' => [
                        'value' => '03',
                        'label' => 'März',
                    ],
                    '04' => [
                        'value' => '04',
                        'label' => 'April',
                    ],
                    '05' => [
                        'value' => '05',
                        'label' => 'Mai',
                    ],
                    '06' => [
                        'value' => '06',
                        'label' => 'Juni',
                    ],
                    '07' => [
                        'value' => '07',
                        'label' => 'Juli',
                    ],
                    '08' => [
                        'value' => '08',
                        'label' => 'August',
                    ],
                    '09' => [
                        'value' => '09',
                        'label' => 'September',
                    ],
                    '10' => [
                        'value' => '10',
                        'label' => 'Oktober',
                    ],
                    '11' => [
                        'value' => '11',
                        'label' => 'November',
                    ],
                    '12' => [
                        'value' => '12',
                        'label' => 'Dezember',
                    ],
                ],
            )
            ->willReturnSelf();
        $monthElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

        $yearElement = $this->createMock(Select::class);
        $years       = [];

        for ($i = $maxYear; $i >= $minYear; --$i) {
            $years[$i] = ['value' => (string) $i, 'label' => (string) $i];
        }

        $yearElement->expects(self::once())
            ->method('setValueOptions')
            ->with($years)
            ->willReturnSelf();
        $yearElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $matcher = self::exactly(2);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $monthElement, $yearElement, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('shouldRenderDelimiters')
            ->willReturn($renderDelimiters);
        $element->expects(self::once())
            ->method('getMinYear')
            ->willReturn($minYear);
        $element->expects(self::once())
            ->method('getMaxYear')
            ->willReturn($maxYear);
        $element->expects(self::once())
            ->method('getMonthElement')
            ->willReturn($monthElement);
        $element->expects(self::once())
            ->method('getYearElement')
            ->willReturn($yearElement);
        $element->expects(self::once())
            ->method('shouldCreateEmptyOption')
            ->willReturn($shouldCreateEmptyOption);

        self::assertSame($excpected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRender2(): void
    {
        $name                    = 'test-name';
        $renderDelimiters        = false;
        $shouldCreateEmptyOption = true;
        $minYear                 = (int) date('Y') - 2;
        $maxYear                 = (int) date('Y') + 2;
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedMonth . PHP_EOL . $renderedYear . PHP_EOL;

        $monthElement = $this->createMock(Select::class);
        $monthElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => [
                        'value' => '01',
                        'label' => 'Januar',
                    ],
                    '02' => [
                        'value' => '02',
                        'label' => 'Februar',
                    ],
                    '03' => [
                        'value' => '03',
                        'label' => 'März',
                    ],
                    '04' => [
                        'value' => '04',
                        'label' => 'April',
                    ],
                    '05' => [
                        'value' => '05',
                        'label' => 'Mai',
                    ],
                    '06' => [
                        'value' => '06',
                        'label' => 'Juni',
                    ],
                    '07' => [
                        'value' => '07',
                        'label' => 'Juli',
                    ],
                    '08' => [
                        'value' => '08',
                        'label' => 'August',
                    ],
                    '09' => [
                        'value' => '09',
                        'label' => 'September',
                    ],
                    '10' => [
                        'value' => '10',
                        'label' => 'Oktober',
                    ],
                    '11' => [
                        'value' => '11',
                        'label' => 'November',
                    ],
                    '12' => [
                        'value' => '12',
                        'label' => 'Dezember',
                    ],
                ],
            )
            ->willReturnSelf();
        $monthElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

        $yearElement = $this->createMock(Select::class);
        $years       = [];

        for ($i = $maxYear; $i >= $minYear; --$i) {
            $years[$i] = ['value' => (string) $i, 'label' => (string) $i];
        }

        $yearElement->expects(self::once())
            ->method('setValueOptions')
            ->with($years)
            ->willReturnSelf();
        $yearElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $matcher = self::exactly(2);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $monthElement, $yearElement, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('shouldRenderDelimiters')
            ->willReturn($renderDelimiters);
        $element->expects(self::once())
            ->method('getMinYear')
            ->willReturn($minYear);
        $element->expects(self::once())
            ->method('getMaxYear')
            ->willReturn($maxYear);
        $element->expects(self::once())
            ->method('getMonthElement')
            ->willReturn($monthElement);
        $element->expects(self::once())
            ->method('getYearElement')
            ->willReturn($yearElement);
        $element->expects(self::once())
            ->method('shouldCreateEmptyOption')
            ->willReturn($shouldCreateEmptyOption);

        self::assertSame($excpected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRender3(): void
    {
        $name                    = 'test-name';
        $renderDelimiters        = true;
        $shouldCreateEmptyOption = false;
        $minYear                 = (int) date('Y') - 2;
        $maxYear                 = (int) date('Y') + 2;
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedMonth . PHP_EOL . ' ' . PHP_EOL . $renderedYear . PHP_EOL;

        $monthElement = $this->createMock(Select::class);
        $monthElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => [
                        'value' => '01',
                        'label' => 'Januar',
                    ],
                    '02' => [
                        'value' => '02',
                        'label' => 'Februar',
                    ],
                    '03' => [
                        'value' => '03',
                        'label' => 'März',
                    ],
                    '04' => [
                        'value' => '04',
                        'label' => 'April',
                    ],
                    '05' => [
                        'value' => '05',
                        'label' => 'Mai',
                    ],
                    '06' => [
                        'value' => '06',
                        'label' => 'Juni',
                    ],
                    '07' => [
                        'value' => '07',
                        'label' => 'Juli',
                    ],
                    '08' => [
                        'value' => '08',
                        'label' => 'August',
                    ],
                    '09' => [
                        'value' => '09',
                        'label' => 'September',
                    ],
                    '10' => [
                        'value' => '10',
                        'label' => 'Oktober',
                    ],
                    '11' => [
                        'value' => '11',
                        'label' => 'November',
                    ],
                    '12' => [
                        'value' => '12',
                        'label' => 'Dezember',
                    ],
                ],
            )
            ->willReturnSelf();
        $monthElement->expects(self::never())
            ->method('setEmptyOption');

        $yearElement = $this->createMock(Select::class);
        $years       = [];

        for ($i = $maxYear; $i >= $minYear; --$i) {
            $years[$i] = ['value' => (string) $i, 'label' => (string) $i];
        }

        $yearElement->expects(self::once())
            ->method('setValueOptions')
            ->with($years)
            ->willReturnSelf();
        $yearElement->expects(self::never())
            ->method('setEmptyOption');

        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $matcher = self::exactly(2);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $monthElement, $yearElement, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('shouldRenderDelimiters')
            ->willReturn($renderDelimiters);
        $element->expects(self::once())
            ->method('getMinYear')
            ->willReturn($minYear);
        $element->expects(self::once())
            ->method('getMaxYear')
            ->willReturn($maxYear);
        $element->expects(self::once())
            ->method('getMonthElement')
            ->willReturn($monthElement);
        $element->expects(self::once())
            ->method('getYearElement')
            ->willReturn($yearElement);
        $element->expects(self::once())
            ->method('shouldCreateEmptyOption')
            ->willReturn($shouldCreateEmptyOption);

        self::assertSame($excpected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRender4(): void
    {
        $name                    = 'test-name';
        $renderDelimiters        = false;
        $shouldCreateEmptyOption = false;
        $minYear                 = (int) date('Y') - 2;
        $maxYear                 = (int) date('Y') + 2;
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedMonth . PHP_EOL . $renderedYear . PHP_EOL;

        $monthElement = $this->createMock(Select::class);
        $monthElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => [
                        'value' => '01',
                        'label' => 'Januar',
                    ],
                    '02' => [
                        'value' => '02',
                        'label' => 'Februar',
                    ],
                    '03' => [
                        'value' => '03',
                        'label' => 'März',
                    ],
                    '04' => [
                        'value' => '04',
                        'label' => 'April',
                    ],
                    '05' => [
                        'value' => '05',
                        'label' => 'Mai',
                    ],
                    '06' => [
                        'value' => '06',
                        'label' => 'Juni',
                    ],
                    '07' => [
                        'value' => '07',
                        'label' => 'Juli',
                    ],
                    '08' => [
                        'value' => '08',
                        'label' => 'August',
                    ],
                    '09' => [
                        'value' => '09',
                        'label' => 'September',
                    ],
                    '10' => [
                        'value' => '10',
                        'label' => 'Oktober',
                    ],
                    '11' => [
                        'value' => '11',
                        'label' => 'November',
                    ],
                    '12' => [
                        'value' => '12',
                        'label' => 'Dezember',
                    ],
                ],
            )
            ->willReturnSelf();
        $monthElement->expects(self::never())
            ->method('setEmptyOption');

        $yearElement = $this->createMock(Select::class);
        $years       = [];

        for ($i = $maxYear; $i >= $minYear; --$i) {
            $years[$i] = ['value' => (string) $i, 'label' => (string) $i];
        }

        $yearElement->expects(self::once())
            ->method('setValueOptions')
            ->with($years)
            ->willReturnSelf();
        $yearElement->expects(self::never())
            ->method('setEmptyOption');

        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $matcher = self::exactly(2);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $monthElement, $yearElement, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('shouldRenderDelimiters')
            ->willReturn($renderDelimiters);
        $element->expects(self::once())
            ->method('getMinYear')
            ->willReturn($minYear);
        $element->expects(self::once())
            ->method('getMaxYear')
            ->willReturn($maxYear);
        $element->expects(self::once())
            ->method('getMonthElement')
            ->willReturn($monthElement);
        $element->expects(self::once())
            ->method('getYearElement')
            ->willReturn($yearElement);
        $element->expects(self::once())
            ->method('shouldCreateEmptyOption')
            ->willReturn($shouldCreateEmptyOption);

        self::assertSame($excpected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRender5(): void
    {
        $name                    = 'test-name';
        $renderDelimiters        = false;
        $shouldCreateEmptyOption = false;
        $minYear                 = (int) date('Y') - 2;
        $maxYear                 = (int) date('Y') + 2;
        $indent                  = '    ';
        $renderedMonth           = $indent . '<select name="month"></select>';
        $renderedYear            = $indent . '<select name="year"></select>';

        $excpected = $indent . PHP_EOL . $renderedMonth . PHP_EOL . $renderedYear . PHP_EOL . $indent;

        $monthElement = $this->createMock(Select::class);
        $monthElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => [
                        'value' => '01',
                        'label' => 'Januar',
                    ],
                    '02' => [
                        'value' => '02',
                        'label' => 'Februar',
                    ],
                    '03' => [
                        'value' => '03',
                        'label' => 'März',
                    ],
                    '04' => [
                        'value' => '04',
                        'label' => 'April',
                    ],
                    '05' => [
                        'value' => '05',
                        'label' => 'Mai',
                    ],
                    '06' => [
                        'value' => '06',
                        'label' => 'Juni',
                    ],
                    '07' => [
                        'value' => '07',
                        'label' => 'Juli',
                    ],
                    '08' => [
                        'value' => '08',
                        'label' => 'August',
                    ],
                    '09' => [
                        'value' => '09',
                        'label' => 'September',
                    ],
                    '10' => [
                        'value' => '10',
                        'label' => 'Oktober',
                    ],
                    '11' => [
                        'value' => '11',
                        'label' => 'November',
                    ],
                    '12' => [
                        'value' => '12',
                        'label' => 'Dezember',
                    ],
                ],
            )
            ->willReturnSelf();
        $monthElement->expects(self::never())
            ->method('setEmptyOption');

        $yearElement = $this->createMock(Select::class);
        $years       = [];

        for ($i = $maxYear; $i >= $minYear; --$i) {
            $years[$i] = ['value' => (string) $i, 'label' => (string) $i];
        }

        $yearElement->expects(self::once())
            ->method('setValueOptions')
            ->with($years)
            ->willReturnSelf();
        $yearElement->expects(self::never())
            ->method('setEmptyOption');

        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $matcher = self::exactly(2);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $monthElement, $yearElement, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('shouldRenderDelimiters')
            ->willReturn($renderDelimiters);
        $element->expects(self::once())
            ->method('getMinYear')
            ->willReturn($minYear);
        $element->expects(self::once())
            ->method('getMaxYear')
            ->willReturn($maxYear);
        $element->expects(self::once())
            ->method('getMonthElement')
            ->willReturn($monthElement);
        $element->expects(self::once())
            ->method('getYearElement')
            ->willReturn($yearElement);
        $element->expects(self::once())
            ->method('shouldCreateEmptyOption')
            ->willReturn($shouldCreateEmptyOption);

        $helper->setIndent($indent);

        self::assertSame($excpected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRender6(): void
    {
        $name                    = 'test-name';
        $renderDelimiters        = true;
        $shouldCreateEmptyOption = true;
        $minYear                 = (int) date('Y') - 2;
        $maxYear                 = (int) date('Y') + 2;
        $indent                  = '    ';
        $renderedMonth           = $indent . '<select name="month"></select>';
        $renderedYear            = $indent . '<select name="year"></select>';

        $excpected = $indent . PHP_EOL . $renderedMonth . PHP_EOL . $indent . ' ' . PHP_EOL . $renderedYear . PHP_EOL . $indent;

        $monthElement = $this->createMock(Select::class);
        $monthElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => [
                        'value' => '01',
                        'label' => 'Januar',
                    ],
                    '02' => [
                        'value' => '02',
                        'label' => 'Februar',
                    ],
                    '03' => [
                        'value' => '03',
                        'label' => 'März',
                    ],
                    '04' => [
                        'value' => '04',
                        'label' => 'April',
                    ],
                    '05' => [
                        'value' => '05',
                        'label' => 'Mai',
                    ],
                    '06' => [
                        'value' => '06',
                        'label' => 'Juni',
                    ],
                    '07' => [
                        'value' => '07',
                        'label' => 'Juli',
                    ],
                    '08' => [
                        'value' => '08',
                        'label' => 'August',
                    ],
                    '09' => [
                        'value' => '09',
                        'label' => 'September',
                    ],
                    '10' => [
                        'value' => '10',
                        'label' => 'Oktober',
                    ],
                    '11' => [
                        'value' => '11',
                        'label' => 'November',
                    ],
                    '12' => [
                        'value' => '12',
                        'label' => 'Dezember',
                    ],
                ],
            )
            ->willReturnSelf();
        $monthElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

        $yearElement = $this->createMock(Select::class);
        $years       = [];

        for ($i = $maxYear; $i >= $minYear; --$i) {
            $years[$i] = ['value' => (string) $i, 'label' => (string) $i];
        }

        $yearElement->expects(self::once())
            ->method('setValueOptions')
            ->with($years)
            ->willReturnSelf();
        $yearElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::once())
            ->method('setIndent')
            ->with($indent);
        $matcher = self::exactly(2);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $monthElement, $yearElement, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormMonthSelect($selectHelper);

        $element = $this->createMock(MonthSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('shouldRenderDelimiters')
            ->willReturn($renderDelimiters);
        $element->expects(self::once())
            ->method('getMinYear')
            ->willReturn($minYear);
        $element->expects(self::once())
            ->method('getMaxYear')
            ->willReturn($maxYear);
        $element->expects(self::once())
            ->method('getMonthElement')
            ->willReturn($monthElement);
        $element->expects(self::once())
            ->method('getYearElement')
            ->willReturn($yearElement);
        $element->expects(self::once())
            ->method('shouldCreateEmptyOption')
            ->willReturn($shouldCreateEmptyOption);

        $helper->setIndent($indent);

        self::assertSame($excpected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws ExtensionNotLoadedException
     */
    public function testSetGetIndent1(): void
    {
        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormMonthSelect($selectHelper);

        self::assertSame($helper, $helper->setIndent(4));
        self::assertSame('    ', $helper->getIndent());
    }

    /**
     * @throws Exception
     * @throws ExtensionNotLoadedException
     */
    public function testSetGetIndent2(): void
    {
        $selectHelper = $this->createMock(FormSelectInterface::class);
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormMonthSelect($selectHelper);

        self::assertSame($helper, $helper->setIndent('  '));
        self::assertSame('  ', $helper->getIndent());
    }
}
