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

use ArrayObject;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormHiddenInterface;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelect;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function assert;
use function sprintf;

use const PHP_EOL;

final class FormSelectTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderWithWrongElement(): void
    {
        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
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
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelect::render',
                SelectElement::class
            )
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
        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
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
            ->method('getEmptyOption');
        $element->expects(self::never())
            ->method('getUnselectedValue');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelect::render'
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testRenderWithNameWithoutValue(): void
    {
        $name               = 'test-name';
        $id                 = 'test-id';
        $value2             = 'def';
        $value2Escaped      = 'def-escaped';
        $value3             = 'abc';
        $class              = 'test-class';
        $ariaLabel          = 'test';
        $valueOptions       = [$value3 => $value2];
        $attributes         = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id];
        $emptyOption        = '0';
        $emptyOptionEscaped = '0e';
        $expected           = sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" name="%s">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionEscaped) . PHP_EOL .
            sprintf('    <option value="%s">%s</option>', $value3, $value2Escaped) . PHP_EOL .
            '</select>';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$emptyOption], [$value2])
            ->willReturnOnConsecutiveCalls($emptyOptionEscaped, $value2Escaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
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
            ->willReturn(null);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::never())
            ->method('getUnselectedValue');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testRenderWithNameWithStringValue(): void
    {
        $name               = 'test-name';
        $id                 = 'test-id';
        $value1             = 'xyz';
        $value2             = 'def';
        $value2Escaped      = 'def-escaped';
        $value3             = 'abc';
        $class              = 'test-class';
        $ariaLabel          = 'test';
        $valueOptions       = [$value3 => $value2];
        $attributes         = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id];
        $emptyOption        = '0';
        $emptyOptionEscaped = '0e';
        $expected           = sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" name="%s">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionEscaped) . PHP_EOL .
            sprintf('    <option value="%s">%s</option>', $value3, $value2Escaped) . PHP_EOL .
            '</select>';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$emptyOption], [$value2])
            ->willReturnOnConsecutiveCalls($emptyOptionEscaped, $value2Escaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn($value1);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::never())
            ->method('getUnselectedValue');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderWithNameWithArrayValue(): void
    {
        $name         = 'test-name';
        $id           = 'test-id';
        $value2       = 'def';
        $value3       = 'abc';
        $class        = 'test-class';
        $ariaLabel    = 'test';
        $valueOptions = [$value3 => $value2];
        $attributes   = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id];
        $emptyOption  = '0';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn([$value3]);
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
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::never())
            ->method('getUnselectedValue');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s does not allow specifying multiple selected values when the element does not have a multiple attribute set to a boolean true',
                FormSelect::class
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testRenderWithNameWithArrayMultipleValue(): void
    {
        $name               = 'test-name';
        $id                 = 'test-id';
        $value1             = 'xyz';
        $value2             = 'def';
        $value2Escaped      = 'def-escaped';
        $value3             = 'abc';
        $class              = 'test-class';
        $ariaLabel          = 'test';
        $valueOptions       = [$value3 => $value2];
        $attributes         = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id, 'multiple' => true];
        $emptyOption        = '0';
        $emptyOptionEscaped = '0e';
        $expected           = sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" multiple="multiple" name="%s&#x5B;&#x5D;">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionEscaped) . PHP_EOL .
            sprintf('    <option value="%s" selected="selected">%s</option>', $value3, $value2Escaped) . PHP_EOL .
            '</select>';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$emptyOption], [$value2])
            ->willReturnOnConsecutiveCalls($emptyOptionEscaped, $value2Escaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn([$value1, $value3]);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::never())
            ->method('getUnselectedValue');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testRenderMultipleOptions1(): void
    {
        $name          = 'test-name';
        $id            = 'test-id';
        $value1        = 'xyz';
        $label1        = 'def1';
        $label1Escaped = 'def-escaped';
        $value2        = '1';
        $label2        = '...2';
        $label2Escaped = '...2-escaped';
        $label3        = 'group3';
        $value4        = '2';
        $label4        = 'Choose...4';
        $label4Escaped = 'Choose...4-escaped';
        $value5        = '3';
        $label5        = '...5';
        $label5Escaped = '...5-escaped';
        $label6        = 'group6';
        $value7        = '4';
        $label7        = 'Choose...7';
        $label7Escaped = 'Choose...7-escaped';
        $value8        = '5';
        $label8        = '...8';
        $label8Escaped = '...8-escaped';

        $class              = 'test-class';
        $ariaLabel          = 'test';
        $valueOptions       = [
            [
                'value' => $value1,
                'label' => $label1,
                'attributes' => ['selected' => true],
            ],
            [
                'value' => $value2,
                'label' => $label2,
            ],
            [
                'label' => $label3,
                'options' => [
                    [
                        'value' => $value4,
                        'label' => $label4,
                    ],
                    [
                        'value' => $value5,
                        'label' => $label5,
                    ],
                    [
                        'label' => $label6,
                        'options' => [
                            [
                                'value' => $value7,
                                'label' => $label7,
                            ],
                            [
                                'value' => $value8,
                                'label' => $label8,
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $attributes         = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id, 'multiple' => true];
        $emptyOption        = '0';
        $emptyOptionEscaped = '0e';
        $expected           = sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" multiple="multiple" name="%s&#x5B;&#x5D;">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionEscaped) . PHP_EOL .
            sprintf('    <option value="%s" selected="selected">%s</option>', $value1, $label1Escaped) . PHP_EOL .
            sprintf('    <option value="%s">%s</option>', $value2, $label2Escaped) . PHP_EOL .
            sprintf('    <optgroup label="%s">', $label3) . PHP_EOL .
            sprintf('        <option value="%s">%s</option>', $value4, $label4Escaped) . PHP_EOL .
            sprintf('        <option value="%s">%s</option>', $value5, $label5Escaped) . PHP_EOL .
            sprintf('        <optgroup label="%s">', $label6) . PHP_EOL .
            sprintf('            <option value="%s" selected="selected">%s</option>', $value7, $label7Escaped) . PHP_EOL .
            sprintf('            <option value="%s" selected="selected" disabled="disabled">%s</option>', $value8, $label8Escaped) . PHP_EOL .
            '        </optgroup>' . PHP_EOL .
            '    </optgroup>' . PHP_EOL .
            '</select>';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(7))
            ->method('__invoke')
            ->withConsecutive([$emptyOption], [$label1], [$label2], [$label4], [$label5], [$label7], [$label8])
            ->willReturnOnConsecutiveCalls($emptyOptionEscaped, $label1Escaped, $label2Escaped, $label4Escaped, $label5Escaped, $label7Escaped, $label8Escaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn([$value7, $value8]);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::never())
            ->method('getUnselectedValue');

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testRenderMultipleOptions2(): void
    {
        $name                    = 'test-name';
        $id                      = 'test-id';
        $value1                  = 'xyz';
        $label1                  = 'def1';
        $label1Translated        = 'def1-translated';
        $label1TranslatedEscaped = 'def1-translated-escaped';
        $value2                  = '1';
        $label2                  = '...2';
        $label2Translated        = '...2-translated';
        $label2TranslatedEscaped = '...2-translated-escaped';
        $label3                  = 'group3';
        $value4                  = '2';
        $label4                  = 'Choose...4';
        $label4Translated        = 'Choose...4-translated';
        $label4TranslatedEscaped = 'Choose...4-translated-escaped';
        $value5                  = '3';
        $label5                  = '...5';
        $label5Translated        = '...5-translated';
        $label5TranslatedEscaped = '...5-translated-escaped';
        $label6                  = 'group6';
        $value7                  = '4';
        $label7                  = 'Choose...7';
        $label7Translated        = 'Choose...7-translated';
        $label7TranslatedEscaped = 'Choose...7-translated-escaped';
        $value8                  = '5';
        $label8                  = '...8';
        $label8Translated        = '...8-translated';

        $class                        = 'test-class';
        $ariaLabel                    = 'test';
        $valueOptions                 = [
            [
                'value' => $value1,
                'label' => $label1,
                'attributes' => ['selected' => true],
            ],
            [
                'value' => $value2,
                'label' => $label2,
            ],
            [
                'label' => $label3,
                'options' => [
                    [
                        'value' => $value4,
                        'label' => $label4,
                    ],
                    [
                        'value' => $value5,
                        'label' => $label5,
                        'selected' => true,
                    ],
                    [
                        'label' => $label6,
                        'options' => [
                            [
                                'value' => $value7,
                                'label' => $label7,
                            ],
                            [
                                'value' => $value8,
                                'label' => $label8,
                                'disabled' => true,
                                'disable_html_escape' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $attributes                   = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id, 'multiple' => true];
        $emptyOption                  = '0';
        $emptyOptionTranslated        = '0t';
        $emptyOptionTranslatedEscaped = '0te';

        $expected   = sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" multiple="multiple" name="%s&#x5B;&#x5D;">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionTranslatedEscaped) . PHP_EOL .
            sprintf('    <option value="%s" selected="selected">%s</option>', $value1, $label1TranslatedEscaped) . PHP_EOL .
            sprintf('    <option value="%s">%s</option>', $value2, $label2TranslatedEscaped) . PHP_EOL .
            sprintf('    <optgroup label="%s">', $label3) . PHP_EOL .
            sprintf('        <option value="%s">%s</option>', $value4, $label4TranslatedEscaped) . PHP_EOL .
            sprintf('        <option value="%s" selected="selected">%s</option>', $value5, $label5TranslatedEscaped) . PHP_EOL .
            sprintf('        <optgroup label="%s">', $label6) . PHP_EOL .
            sprintf('            <option value="%s">%s</option>', $value7, $label7TranslatedEscaped) . PHP_EOL .
            sprintf('            <option value="%s" disabled="disabled">%s</option>', $value8, $label8Translated) . PHP_EOL .
            '        </optgroup>' . PHP_EOL .
            '    </optgroup>' . PHP_EOL .
            '</select>';
        $textDomain = 'test-domain';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(6))
            ->method('__invoke')
            ->withConsecutive([$emptyOptionTranslated], [$label1Translated], [$label2Translated], [$label4Translated], [$label5Translated], [$label7Translated])
            ->willReturnOnConsecutiveCalls($emptyOptionTranslatedEscaped, $label1TranslatedEscaped, $label2TranslatedEscaped, $label4TranslatedEscaped, $label5TranslatedEscaped, $label7TranslatedEscaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::never())
            ->method('render');

        $translator = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects(self::exactly(7))
            ->method('__invoke')
            ->withConsecutive([$emptyOption, $textDomain], [$label1, $textDomain], [$label2, $textDomain], [$label4, $textDomain], [$label5, $textDomain], [$label7, $textDomain], [$label8, $textDomain])
            ->willReturnOnConsecutiveCalls($emptyOptionTranslated, $label1Translated, $label2Translated, $label4Translated, $label5Translated, $label7Translated, $label8Translated);

        $helper = new FormSelect($escapeHtml, $formHidden, $translator);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn([]);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(false);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::never())
            ->method('getUnselectedValue');

        $helper->setTranslatorTextDomain($textDomain);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testRenderWithHiddenElement(): void
    {
        $name               = 'test-name';
        $id                 = 'test-id';
        $value1             = 'xyz';
        $value2             = 'def';
        $value2Escaped      = 'def-escaped';
        $value3             = 'abc';
        $class              = 'test-class';
        $ariaLabel          = 'test';
        $valueOptions       = [$value3 => $value2];
        $attributes         = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id, 'multiple' => true];
        $emptyOption        = '0';
        $emptyOptionEscaped = '0e';
        $unselectedValue    = 'u';
        $expected           = sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $unselectedValue) . PHP_EOL .
            sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" multiple="multiple" name="%s&#x5B;&#x5D;">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionEscaped) . PHP_EOL .
            sprintf('    <option value="%s" selected="selected">%s</option>', $value3, $value2Escaped) . PHP_EOL .
            '</select>';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$emptyOption], [$value2])
            ->willReturnOnConsecutiveCalls($emptyOptionEscaped, $value2Escaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $unselectedValue));

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn([$value1, $value3]);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::once())
            ->method('getUnselectedValue')
            ->willReturn($unselectedValue);

        self::assertSame($expected, $helper->render($element));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInvokeWithHiddenElement1(): void
    {
        $name               = 'test-name';
        $id                 = 'test-id';
        $value1             = 'xyz';
        $value2             = 'def';
        $value2Escaped      = 'def-escaped';
        $value3             = 'abc';
        $class              = 'test-class';
        $ariaLabel          = 'test';
        $valueOptions       = [$value3 => $value2];
        $attributes         = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id, 'multiple' => true];
        $emptyOption        = '0';
        $emptyOptionEscaped = '0e';
        $unselectedValue    = 'u';
        $expected           = sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $unselectedValue) . PHP_EOL .
            sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" multiple="multiple" name="%s&#x5B;&#x5D;">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionEscaped) . PHP_EOL .
            sprintf('    <option value="%s" selected="selected">%s</option>', $value3, $value2Escaped) . PHP_EOL .
            '</select>';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$emptyOption], [$value2])
            ->willReturnOnConsecutiveCalls($emptyOptionEscaped, $value2Escaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $unselectedValue));

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn([$value1, $value3]);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::once())
            ->method('getUnselectedValue')
            ->willReturn($unselectedValue);

        $helperObject = $helper();

        assert($helperObject instanceof FormSelect);

        self::assertSame($expected, $helperObject->render($element));
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInvokeWithHiddenElement2(): void
    {
        $name               = 'test-name';
        $id                 = 'test-id';
        $value1             = 'xyz';
        $value2             = 'def';
        $value2Escaped      = 'def-escaped';
        $value3             = 'abc';
        $class              = 'test-class';
        $ariaLabel          = 'test';
        $valueOptions       = [$value3 => $value2];
        $attributes         = ['class' => $class, 'aria-label' => $ariaLabel, 'id' => $id, 'multiple' => true];
        $emptyOption        = '0';
        $emptyOptionEscaped = '0e';
        $unselectedValue    = 'u';
        $expected           = sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $unselectedValue) . PHP_EOL .
            sprintf('<select class="form-select&#x20;%s" aria-label="%s" id="%s" multiple="multiple" name="%s&#x5B;&#x5D;">', $class, $ariaLabel, $id, $name) . PHP_EOL .
            sprintf('    <option value="">%s</option>', $emptyOptionEscaped) . PHP_EOL .
            sprintf('    <option value="%s" selected="selected">%s</option>', $value3, $value2Escaped) . PHP_EOL .
            '</select>';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$emptyOption], [$value2])
            ->willReturnOnConsecutiveCalls($emptyOptionEscaped, $value2Escaped);

        $formHidden = $this->getMockBuilder(FormHiddenInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formHidden->expects(self::once())
            ->method('render')
            ->with(new IsInstanceOf(Hidden::class))
            ->willReturn(sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $unselectedValue));

        $helper = new FormSelect($escapeHtml, $formHidden, null);

        $element = $this->getMockBuilder(SelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::exactly(2))
            ->method('getName')
            ->willReturn($name);
        $element->expects(self::once())
            ->method('getValueOptions')
            ->willReturn($valueOptions);
        $element->expects(self::once())
            ->method('getAttributes')
            ->willReturn(new ArrayObject($attributes));
        $element->expects(self::once())
            ->method('getValue')
            ->willReturn([$value1, $value3]);
        $element->expects(self::once())
            ->method('useHiddenElement')
            ->willReturn(true);
        $element->expects(self::never())
            ->method('getLabelAttributes');
        $element->expects(self::never())
            ->method('getOption');
        $element->expects(self::never())
            ->method('getLabelOption');
        $element->expects(self::never())
            ->method('hasLabelOption');
        $element->expects(self::once())
            ->method('getEmptyOption')
            ->willReturn($emptyOption);
        $element->expects(self::once())
            ->method('getUnselectedValue')
            ->willReturn($unselectedValue);

        self::assertSame($expected, $helper($element));
    }
}
