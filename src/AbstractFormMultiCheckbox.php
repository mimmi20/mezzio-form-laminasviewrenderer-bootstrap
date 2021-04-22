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

namespace Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormInput;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;

use function array_key_exists;
use function array_merge;
use function implode;
use function in_array;
use function is_array;
use function is_scalar;
use function mb_strtolower;
use function method_exists;
use function sprintf;
use function trim;

use const PHP_EOL;

abstract class AbstractFormMultiCheckbox extends FormInput
{
    public const LABEL_APPEND  = 'append';
    public const LABEL_PREPEND = 'prepend';

    private FormLabel $formLabel;
    private ?Translate $translate;
    private EscapeHtml $escaper;

    /**
     * The attributes applied to option label
     *
     * @var array<string, bool|string>
     */
    private array $labelAttributes = [];

    /**
     * Where will be label rendered?
     */
    private string $labelPosition = self::LABEL_APPEND;

    /**
     * Separator for checkbox elements
     */
    private string $separator = '';

    /**
     * Prefixing the element with a hidden element for the unset value?
     */
    private bool $useHiddenElement = false;

    /**
     * The unchecked value used when "UseHiddenElement" is turned on
     */
    private string $uncheckedValue = '';

    public function __construct(FormLabel $formLabel, EscapeHtml $escaper, ?Translate $translator = null)
    {
        $this->formLabel = $formLabel;
        $this->escaper   = $escaper;
        $this->translate = $translator;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return FormMultiCheckbox|string
     */
    public function __invoke(?ElementInterface $element = null, ?string $labelPosition = null)
    {
        if (!$element) {
            return $this;
        }

        if (null !== $labelPosition) {
            $this->setLabelPosition($labelPosition);
        }

        return $this->render($element);
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws Exception\InvalidArgumentException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof MultiCheckboxElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\MultiCheckbox',
                __METHOD__
            ));
        }

        $name = static::getName($element);

        $options = $element->getValueOptions();

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();
        $selectedOptions    = (array) $element->getValue();

        $rendered = $this->renderOptions($element, $options, $selectedOptions, $attributes);

        // Render hidden element
        $useHiddenElement = method_exists($element, 'useHiddenElement') && $element->useHiddenElement()
            ? $element->useHiddenElement()
            : $this->useHiddenElement;

        if ($useHiddenElement) {
            $rendered = $this->renderHiddenElement($element, $attributes) . $rendered;
        }

        return $rendered;
    }

    /**
     * Sets the attributes applied to option label.
     *
     * @param array<string, bool|string> $attributes
     */
    public function setLabelAttributes(array $attributes): self
    {
        $this->labelAttributes = $attributes;

        return $this;
    }

    /**
     * Returns the attributes applied to each option label.
     *
     * @return array<string, bool|string>
     */
    public function getLabelAttributes(): ?array
    {
        return $this->labelAttributes;
    }

    /**
     * Set value for labelPosition
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setLabelPosition(string $labelPosition): self
    {
        $labelPosition = mb_strtolower($labelPosition);
        if (!in_array($labelPosition, [self::LABEL_APPEND, self::LABEL_PREPEND], true)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either %s::LABEL_APPEND or %s::LABEL_PREPEND; received "%s"',
                __METHOD__,
                self::class,
                self::class,
                (string) $labelPosition
            ));
        }

        $this->labelPosition = $labelPosition;

        return $this;
    }

    /**
     * Get position of label
     */
    public function getLabelPosition(): string
    {
        return $this->labelPosition;
    }

    /**
     * Set separator string for checkbox elements
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = (string) $separator;

        return $this;
    }

    /**
     * Get separator for checkbox elements
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Sets the option for prefixing the element with a hidden element
     * for the unset value.
     */
    public function setUseHiddenElement(bool $useHiddenElement): self
    {
        $this->useHiddenElement = (bool) $useHiddenElement;

        return $this;
    }

    /**
     * Returns the option for prefixing the element with a hidden element
     * for the unset value.
     */
    public function getUseHiddenElement(): bool
    {
        return $this->useHiddenElement;
    }

    /**
     * Sets the unchecked value used when "UseHiddenElement" is turned on.
     */
    public function setUncheckedValue(string $value): self
    {
        $this->uncheckedValue = $value;

        return $this;
    }

    /**
     * Returns the unchecked value used when "UseHiddenElement" is turned on.
     */
    public function getUncheckedValue(): string
    {
        return $this->uncheckedValue;
    }

    /**
     * Return input type
     */
    abstract protected function getInputType(): string;

    /**
     * Get element name
     *
     * @throws Exception\DomainException
     */
    abstract protected static function getName(ElementInterface $element): string;

    /**
     * Render options
     *
     * @param array<int|string, array<string, string>|int|string> $options
     * @param array<int|string, string>                           $selectedOptions
     * @param array<string, bool|string>                          $attributes
     */
    private function renderOptions(
        MultiCheckboxElement $element,
        array $options,
        array $selectedOptions,
        array $attributes
    ): string {
        $labelPosition         = $this->getLabelPosition();
        $globalLabelAttributes = [];
        $closingBracket        = $this->getInlineClosingBracket();

        if ($element instanceof LabelAwareInterface) {
            $globalLabelAttributes = $element->getLabelAttributes();
        }

        if (empty($globalLabelAttributes)) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = [];
        $count          = 0;

        foreach ($options as $key => $optionSpec) {
            ++$count;
            if (1 < $count && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value           = '';
            $label           = '';
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
            $selected        = isset($inputAttributes['selected'])
                && 'radio' !== $inputAttributes['type']
                && $inputAttributes['selected'];
            $disabled        = isset($inputAttributes['disabled']) && $inputAttributes['disabled'];

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key,
                ];
            }

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }

            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }

            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }

            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }

            if (isset($optionSpec['label_attributes'])) {
                $labelAttributes = isset($labelAttributes)
                    ? array_merge($labelAttributes, $optionSpec['label_attributes'])
                    : $optionSpec['label_attributes'];
            }

            if (isset($optionSpec['attributes'])) {
                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }

            if (in_array($value, $selectedOptions, true)) {
                $selected = true;
            }

            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;

            if (!array_key_exists('class', $inputAttributes)) {
                $inputAttributes['class'] = '';
            }

            $inputAttributes['class'] = trim('form-check-input ' . $inputAttributes['class']);

            if (!is_array($labelAttributes)) {
                $labelAttributes = [];
            }

            if (!array_key_exists('class', $labelAttributes)) {
                $labelAttributes['class'] = '';
            }

            $labelAttributes['class'] = trim('form-check-label ' . $labelAttributes['class']);

            if (array_key_exists('id', $inputAttributes)) {
                $labelAttributes['for'] = $inputAttributes['id'];
            }

            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );

            if (null !== $this->translate) {
                $label = ($this->translate)(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = ($this->escaper)($label);
            }

            if (
                array_key_exists('id', $inputAttributes)
                && ($element instanceof LabelAwareInterface && !$element->getLabelOption('always_wrap'))
            ) {
                $labelOpen  = '';
                $labelClose = '';
                $label      = $this->formLabel->openTag($labelAttributes) . $label . $this->formLabel->closeTag();
            } else {
                $labelOpen  = $this->formLabel->openTag($labelAttributes) . PHP_EOL;
                $labelClose = $this->formLabel->closeTag() . PHP_EOL;
            }

            if (
                '' !== $label && !array_key_exists('id', $inputAttributes)
                || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
            ) {
                $label = '<span>' . $label . '</span>';
            }

            $markup = sprintf(
                '<div class="form-check%s">',
                Form::LAYOUT_INLINE === $element->getOption('layout') ? ' form-check-inline' : ''
            );

            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $markup .= $labelOpen . $label . $input . $labelClose;
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup .= $labelOpen . $input . $label . $labelClose;
                    break;
            }

            $markup .= '</div>';

            $combinedMarkup[] = $markup;
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }

    /**
     * Render a hidden element for empty/unchecked value
     *
     * @param array<string, bool> $attributes
     */
    private function renderHiddenElement(MultiCheckboxElement $element, array $attributes): string
    {
        $closingBracket = $this->getInlineClosingBracket();

        $uncheckedValue = $element->getUncheckedValue()
            ?: $this->uncheckedValue;

        $hiddenAttributes = [
            'name' => $element->getName(),
            'value' => $uncheckedValue,
            'disabled' => $attributes['disabled'] ?? null,
        ];

        return sprintf(
            '<input type="hidden" %s%s',
            $this->createAttributesString($hiddenAttributes),
            $closingBracket
        );
    }
}
