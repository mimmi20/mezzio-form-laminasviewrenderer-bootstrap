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
use Laminas\Form\Element\DateSelect as DateSelectElement;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\ExtensionNotLoadedException;
use Laminas\Form\Exception\InvalidArgumentException;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelectInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;
use function date;
use function sprintf;

use const PHP_EOL;

final class FormDateSelectTest extends TestCase
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

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(Text::class);
        $element->expects(self::never())
            ->method('getName');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element is of type %s',
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect::render',
                DateSelectElement::class,
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

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement');
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
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect::render',
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

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement');
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
                'Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect::render',
            ),
        );
        $this->expectExceptionCode(0);

        $helperObject = $helper();

        assert($helperObject instanceof FormDateSelect);

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

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);
        $element->expects(self::never())
            ->method('shouldRenderDelimiters');

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
        $renderedDay             = '<select name="day"></select>';
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedDay . PHP_EOL . '. ' . PHP_EOL . $renderedMonth . PHP_EOL . ' ' . PHP_EOL . $renderedYear . PHP_EOL;

        $dayElement = $this->createMock(Select::class);
        $dayElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => ['value' => '01', 'label' => '1'],
                    '02' => ['value' => '02', 'label' => '2'],
                    '03' => ['value' => '03', 'label' => '3'],
                    '04' => ['value' => '04', 'label' => '4'],
                    '05' => ['value' => '05', 'label' => '5'],
                    '06' => ['value' => '06', 'label' => '6'],
                    '07' => ['value' => '07', 'label' => '7'],
                    '08' => ['value' => '08', 'label' => '8'],
                    '09' => ['value' => '09', 'label' => '9'],
                    '10' => ['value' => '10', 'label' => '10'],
                    '11' => ['value' => '11', 'label' => '11'],
                    '12' => ['value' => '12', 'label' => '12'],
                    '13' => ['value' => '13', 'label' => '13'],
                    '14' => ['value' => '14', 'label' => '14'],
                    '15' => ['value' => '15', 'label' => '15'],
                    '16' => ['value' => '16', 'label' => '16'],
                    '17' => ['value' => '17', 'label' => '17'],
                    '18' => ['value' => '18', 'label' => '18'],
                    '19' => ['value' => '19', 'label' => '19'],
                    '20' => ['value' => '20', 'label' => '20'],
                    '21' => ['value' => '21', 'label' => '21'],
                    '22' => ['value' => '22', 'label' => '22'],
                    '23' => ['value' => '23', 'label' => '23'],
                    '24' => ['value' => '24', 'label' => '24'],
                    '25' => ['value' => '25', 'label' => '25'],
                    '26' => ['value' => '26', 'label' => '26'],
                    '27' => ['value' => '27', 'label' => '27'],
                    '28' => ['value' => '28', 'label' => '28'],
                    '29' => ['value' => '29', 'label' => '29'],
                    '30' => ['value' => '30', 'label' => '30'],
                    '31' => ['value' => '31', 'label' => '31'],
                ],
            )
            ->willReturnSelf();
        $dayElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

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
        $matcher = self::exactly(3);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $dayElement, $monthElement, $yearElement, $renderedDay, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($dayElement, $element),
                        2 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedDay,
                        2 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement')
            ->willReturn($dayElement);
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
        $renderedDay             = '<select name="day"></select>';
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedDay . PHP_EOL . $renderedMonth . PHP_EOL . $renderedYear . PHP_EOL;

        $dayElement = $this->createMock(Select::class);
        $dayElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => ['value' => '01', 'label' => '1'],
                    '02' => ['value' => '02', 'label' => '2'],
                    '03' => ['value' => '03', 'label' => '3'],
                    '04' => ['value' => '04', 'label' => '4'],
                    '05' => ['value' => '05', 'label' => '5'],
                    '06' => ['value' => '06', 'label' => '6'],
                    '07' => ['value' => '07', 'label' => '7'],
                    '08' => ['value' => '08', 'label' => '8'],
                    '09' => ['value' => '09', 'label' => '9'],
                    '10' => ['value' => '10', 'label' => '10'],
                    '11' => ['value' => '11', 'label' => '11'],
                    '12' => ['value' => '12', 'label' => '12'],
                    '13' => ['value' => '13', 'label' => '13'],
                    '14' => ['value' => '14', 'label' => '14'],
                    '15' => ['value' => '15', 'label' => '15'],
                    '16' => ['value' => '16', 'label' => '16'],
                    '17' => ['value' => '17', 'label' => '17'],
                    '18' => ['value' => '18', 'label' => '18'],
                    '19' => ['value' => '19', 'label' => '19'],
                    '20' => ['value' => '20', 'label' => '20'],
                    '21' => ['value' => '21', 'label' => '21'],
                    '22' => ['value' => '22', 'label' => '22'],
                    '23' => ['value' => '23', 'label' => '23'],
                    '24' => ['value' => '24', 'label' => '24'],
                    '25' => ['value' => '25', 'label' => '25'],
                    '26' => ['value' => '26', 'label' => '26'],
                    '27' => ['value' => '27', 'label' => '27'],
                    '28' => ['value' => '28', 'label' => '28'],
                    '29' => ['value' => '29', 'label' => '29'],
                    '30' => ['value' => '30', 'label' => '30'],
                    '31' => ['value' => '31', 'label' => '31'],
                ],
            )
            ->willReturnSelf();
        $dayElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

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
        $matcher = self::exactly(3);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $dayElement, $monthElement, $yearElement, $renderedDay, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($dayElement, $element),
                        2 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedDay,
                        2 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement')
            ->willReturn($dayElement);
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
        $renderedDay             = '<select name="day"></select>';
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedDay . PHP_EOL . '. ' . PHP_EOL . $renderedMonth . PHP_EOL . ' ' . PHP_EOL . $renderedYear . PHP_EOL;

        $dayElement = $this->createMock(Select::class);
        $dayElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => ['value' => '01', 'label' => '1'],
                    '02' => ['value' => '02', 'label' => '2'],
                    '03' => ['value' => '03', 'label' => '3'],
                    '04' => ['value' => '04', 'label' => '4'],
                    '05' => ['value' => '05', 'label' => '5'],
                    '06' => ['value' => '06', 'label' => '6'],
                    '07' => ['value' => '07', 'label' => '7'],
                    '08' => ['value' => '08', 'label' => '8'],
                    '09' => ['value' => '09', 'label' => '9'],
                    '10' => ['value' => '10', 'label' => '10'],
                    '11' => ['value' => '11', 'label' => '11'],
                    '12' => ['value' => '12', 'label' => '12'],
                    '13' => ['value' => '13', 'label' => '13'],
                    '14' => ['value' => '14', 'label' => '14'],
                    '15' => ['value' => '15', 'label' => '15'],
                    '16' => ['value' => '16', 'label' => '16'],
                    '17' => ['value' => '17', 'label' => '17'],
                    '18' => ['value' => '18', 'label' => '18'],
                    '19' => ['value' => '19', 'label' => '19'],
                    '20' => ['value' => '20', 'label' => '20'],
                    '21' => ['value' => '21', 'label' => '21'],
                    '22' => ['value' => '22', 'label' => '22'],
                    '23' => ['value' => '23', 'label' => '23'],
                    '24' => ['value' => '24', 'label' => '24'],
                    '25' => ['value' => '25', 'label' => '25'],
                    '26' => ['value' => '26', 'label' => '26'],
                    '27' => ['value' => '27', 'label' => '27'],
                    '28' => ['value' => '28', 'label' => '28'],
                    '29' => ['value' => '29', 'label' => '29'],
                    '30' => ['value' => '30', 'label' => '30'],
                    '31' => ['value' => '31', 'label' => '31'],
                ],
            )
            ->willReturnSelf();
        $dayElement->expects(self::never())
            ->method('setEmptyOption');

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
        $matcher = self::exactly(3);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $dayElement, $monthElement, $yearElement, $renderedDay, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($dayElement, $element),
                        2 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedDay,
                        2 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement')
            ->willReturn($dayElement);
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
        $renderedDay             = '<select name="day"></select>';
        $renderedMonth           = '<select name="month"></select>';
        $renderedYear            = '<select name="year"></select>';
        $indent                  = '';

        $excpected = PHP_EOL . $renderedDay . PHP_EOL . $renderedMonth . PHP_EOL . $renderedYear . PHP_EOL;

        $dayElement = $this->createMock(Select::class);
        $dayElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => ['value' => '01', 'label' => '1'],
                    '02' => ['value' => '02', 'label' => '2'],
                    '03' => ['value' => '03', 'label' => '3'],
                    '04' => ['value' => '04', 'label' => '4'],
                    '05' => ['value' => '05', 'label' => '5'],
                    '06' => ['value' => '06', 'label' => '6'],
                    '07' => ['value' => '07', 'label' => '7'],
                    '08' => ['value' => '08', 'label' => '8'],
                    '09' => ['value' => '09', 'label' => '9'],
                    '10' => ['value' => '10', 'label' => '10'],
                    '11' => ['value' => '11', 'label' => '11'],
                    '12' => ['value' => '12', 'label' => '12'],
                    '13' => ['value' => '13', 'label' => '13'],
                    '14' => ['value' => '14', 'label' => '14'],
                    '15' => ['value' => '15', 'label' => '15'],
                    '16' => ['value' => '16', 'label' => '16'],
                    '17' => ['value' => '17', 'label' => '17'],
                    '18' => ['value' => '18', 'label' => '18'],
                    '19' => ['value' => '19', 'label' => '19'],
                    '20' => ['value' => '20', 'label' => '20'],
                    '21' => ['value' => '21', 'label' => '21'],
                    '22' => ['value' => '22', 'label' => '22'],
                    '23' => ['value' => '23', 'label' => '23'],
                    '24' => ['value' => '24', 'label' => '24'],
                    '25' => ['value' => '25', 'label' => '25'],
                    '26' => ['value' => '26', 'label' => '26'],
                    '27' => ['value' => '27', 'label' => '27'],
                    '28' => ['value' => '28', 'label' => '28'],
                    '29' => ['value' => '29', 'label' => '29'],
                    '30' => ['value' => '30', 'label' => '30'],
                    '31' => ['value' => '31', 'label' => '31'],
                ],
            )
            ->willReturnSelf();
        $dayElement->expects(self::never())
            ->method('setEmptyOption');

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
        $matcher = self::exactly(3);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $dayElement, $monthElement, $yearElement, $renderedDay, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($dayElement, $element),
                        2 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedDay,
                        2 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement')
            ->willReturn($dayElement);
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
        $renderedDay             = $indent . '<select name="day"></select>';
        $renderedMonth           = $indent . '<select name="month"></select>';
        $renderedYear            = $indent . '<select name="year"></select>';

        $excpected = $indent . PHP_EOL . $renderedDay . PHP_EOL . $renderedMonth . PHP_EOL . $renderedYear . PHP_EOL . $indent;

        $dayElement = $this->createMock(Select::class);
        $dayElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => ['value' => '01', 'label' => '1'],
                    '02' => ['value' => '02', 'label' => '2'],
                    '03' => ['value' => '03', 'label' => '3'],
                    '04' => ['value' => '04', 'label' => '4'],
                    '05' => ['value' => '05', 'label' => '5'],
                    '06' => ['value' => '06', 'label' => '6'],
                    '07' => ['value' => '07', 'label' => '7'],
                    '08' => ['value' => '08', 'label' => '8'],
                    '09' => ['value' => '09', 'label' => '9'],
                    '10' => ['value' => '10', 'label' => '10'],
                    '11' => ['value' => '11', 'label' => '11'],
                    '12' => ['value' => '12', 'label' => '12'],
                    '13' => ['value' => '13', 'label' => '13'],
                    '14' => ['value' => '14', 'label' => '14'],
                    '15' => ['value' => '15', 'label' => '15'],
                    '16' => ['value' => '16', 'label' => '16'],
                    '17' => ['value' => '17', 'label' => '17'],
                    '18' => ['value' => '18', 'label' => '18'],
                    '19' => ['value' => '19', 'label' => '19'],
                    '20' => ['value' => '20', 'label' => '20'],
                    '21' => ['value' => '21', 'label' => '21'],
                    '22' => ['value' => '22', 'label' => '22'],
                    '23' => ['value' => '23', 'label' => '23'],
                    '24' => ['value' => '24', 'label' => '24'],
                    '25' => ['value' => '25', 'label' => '25'],
                    '26' => ['value' => '26', 'label' => '26'],
                    '27' => ['value' => '27', 'label' => '27'],
                    '28' => ['value' => '28', 'label' => '28'],
                    '29' => ['value' => '29', 'label' => '29'],
                    '30' => ['value' => '30', 'label' => '30'],
                    '31' => ['value' => '31', 'label' => '31'],
                ],
            )
            ->willReturnSelf();
        $dayElement->expects(self::never())
            ->method('setEmptyOption');

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
        $matcher = self::exactly(3);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $dayElement, $monthElement, $yearElement, $renderedDay, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($dayElement, $element),
                        2 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedDay,
                        2 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement')
            ->willReturn($dayElement);
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
        $renderedDay             = $indent . '<select name="day"></select>';
        $renderedMonth           = $indent . '<select name="month"></select>';
        $renderedYear            = $indent . '<select name="year"></select>';

        $excpected = $indent . PHP_EOL . $renderedDay . PHP_EOL . $indent . '. ' . PHP_EOL . $renderedMonth . PHP_EOL . $indent . ' ' . PHP_EOL . $renderedYear . PHP_EOL . $indent;

        $dayElement = $this->createMock(Select::class);
        $dayElement->expects(self::once())
            ->method('setValueOptions')
            ->with(
                [
                    '01' => ['value' => '01', 'label' => '1'],
                    '02' => ['value' => '02', 'label' => '2'],
                    '03' => ['value' => '03', 'label' => '3'],
                    '04' => ['value' => '04', 'label' => '4'],
                    '05' => ['value' => '05', 'label' => '5'],
                    '06' => ['value' => '06', 'label' => '6'],
                    '07' => ['value' => '07', 'label' => '7'],
                    '08' => ['value' => '08', 'label' => '8'],
                    '09' => ['value' => '09', 'label' => '9'],
                    '10' => ['value' => '10', 'label' => '10'],
                    '11' => ['value' => '11', 'label' => '11'],
                    '12' => ['value' => '12', 'label' => '12'],
                    '13' => ['value' => '13', 'label' => '13'],
                    '14' => ['value' => '14', 'label' => '14'],
                    '15' => ['value' => '15', 'label' => '15'],
                    '16' => ['value' => '16', 'label' => '16'],
                    '17' => ['value' => '17', 'label' => '17'],
                    '18' => ['value' => '18', 'label' => '18'],
                    '19' => ['value' => '19', 'label' => '19'],
                    '20' => ['value' => '20', 'label' => '20'],
                    '21' => ['value' => '21', 'label' => '21'],
                    '22' => ['value' => '22', 'label' => '22'],
                    '23' => ['value' => '23', 'label' => '23'],
                    '24' => ['value' => '24', 'label' => '24'],
                    '25' => ['value' => '25', 'label' => '25'],
                    '26' => ['value' => '26', 'label' => '26'],
                    '27' => ['value' => '27', 'label' => '27'],
                    '28' => ['value' => '28', 'label' => '28'],
                    '29' => ['value' => '29', 'label' => '29'],
                    '30' => ['value' => '30', 'label' => '30'],
                    '31' => ['value' => '31', 'label' => '31'],
                ],
            )
            ->willReturnSelf();
        $dayElement->expects(self::once())
            ->method('setEmptyOption')
            ->with('')
            ->willReturnSelf();

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
        $matcher = self::exactly(3);
        $selectHelper->expects($matcher)
            ->method('render')
            ->willReturnCallback(
                static function (ElementInterface $element) use ($matcher, $dayElement, $monthElement, $yearElement, $renderedDay, $renderedMonth, $renderedYear): string {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($dayElement, $element),
                        2 => self::assertSame($monthElement, $element),
                        default => self::assertSame($yearElement, $element),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => $renderedDay,
                        2 => $renderedMonth,
                        default => $renderedYear,
                    };
                },
            );

        $helper = new FormDateSelect($selectHelper);

        $element = $this->createMock(DateSelectElement::class);
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
            ->method('getDayElement')
            ->willReturn($dayElement);
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

        $helper = new FormDateSelect($selectHelper);

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

        $helper = new FormDateSelect($selectHelper);

        self::assertSame($helper, $helper->setIndent('  '));
        self::assertSame('  ', $helper->getIndent());
    }
}
