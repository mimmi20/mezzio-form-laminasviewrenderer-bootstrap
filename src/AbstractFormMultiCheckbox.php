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
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use Traversable;

use function array_filter;
use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function implode;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function iterator_to_array;
use function method_exists;
use function sprintf;
use function trim;

use const ARRAY_FILTER_USE_KEY;
use const PHP_EOL;

abstract class AbstractFormMultiCheckbox extends FormInput
{
    use LabelPositionTrait;
    use UseHiddenElementTrait;

    public const LABEL_APPEND  = 'append';
    public const LABEL_PREPEND = 'prepend';

    private FormLabelInterface $formLabel;
    private ?Translate $translate;
    private HtmlElementInterface $htmlElement;

    /**
     * The attributes applied to option label
     *
     * @var array<string, bool|string>
     */
    private array $labelAttributes = [];

    /**
     * Separator for checkbox elements
     */
    private string $separator = '';

    public function __construct(EscapeHtml $escapeHtml, EscapeHtmlAttr $escapeHtmlAttr, Doctype $doctype, FormLabelInterface $formLabel, HtmlElementInterface $htmlElement, ?Translate $translator = null)
    {
        parent::__construct($escapeHtml, $escapeHtmlAttr, $doctype);

        $this->formLabel   = $formLabel;
        $this->htmlElement = $htmlElement;
        $this->escapeHtml  = $escapeHtml;
        $this->translate   = $translator;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function __invoke(?ElementInterface $element = null, ?string $labelPosition = null)
    {
        if (null === $element) {
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
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof MultiCheckboxElement) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type %s',
                    __METHOD__,
                    MultiCheckboxElement::class
                )
            );
        }

        $name       = static::getName($element);
        $options    = $element->getValueOptions();
        $attributes = $element->getAttributes();

        if ($attributes instanceof Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();
        $selectedOptions    = (array) $element->getValue();

        $rendered = $this->renderOptions($element, $options, $selectedOptions, $attributes);

        // Render hidden element
        $useHiddenElement = method_exists($element, 'useHiddenElement') && $element->useHiddenElement()
            ? $element->useHiddenElement()
            : $this->useHiddenElement;

        if ($useHiddenElement) {
            $indent   = $this->getIndent();
            $rendered = $indent . $this->getWhitespace(4) . $this->renderHiddenElement($element, $attributes) . PHP_EOL . $rendered;
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
    public function getLabelAttributes(): array
    {
        return $this->labelAttributes;
    }

    /**
     * Set separator string for checkbox elements
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

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
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    private function renderOptions(
        MultiCheckboxElement $element,
        array $options,
        array $selectedOptions,
        array $attributes
    ): string {
        if ($element->hasLabelOption('label_position')) {
            $labelPosition = $element->getLabelOption('label_position');
        } else {
            $labelPosition = $this->getLabelPosition();
        }

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
        $indent         = $this->getIndent();

        $groupClasses = ['form-check'];

        if (Form::LAYOUT_INLINE === $element->getOption('layout')) {
            $groupClasses[] = 'form-check-inline';
        }

        foreach ($options as $key => $optionSpec) {
            ++$count;
            if (1 < $count && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value           = '';
            $label           = '';
            $inputAttributes = $attributes;

            /** @var array<string, array<string, string>|bool|string> $labelAttributes */
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

            $labelClasses = [];
            $inputClasses = [];

            if (array_key_exists('class', $labelAttributes) && is_string($labelAttributes['class'])) {
                $labelClasses = array_merge($labelClasses, explode(' ', $labelAttributes['class']));
            }

            if (array_key_exists('class', $inputAttributes) && is_string($inputAttributes['class'])) {
                $inputClasses = array_merge($inputClasses, explode(' ', $inputAttributes['class']));
            }

            if (array_key_exists('label_attributes', $optionSpec) && is_array($optionSpec['label_attributes'])) {
                if (array_key_exists('class', $optionSpec['label_attributes']) && is_string($optionSpec['label_attributes']['class'])) {
                    $labelClasses = array_merge($labelClasses, explode(' ', $optionSpec['label_attributes']['class']));

                    unset($optionSpec['label_attributes']['class']);
                }

                assert(is_array($optionSpec['label_attributes']));

                $labelAttributes = array_merge($labelAttributes, $optionSpec['label_attributes']);
            }

            if (array_key_exists('attributes', $optionSpec) && is_array($optionSpec['attributes'])) {
                if (array_key_exists('class', $optionSpec['attributes']) && is_string($optionSpec['attributes']['class'])) {
                    $inputClasses = array_merge($inputClasses, explode(' ', $optionSpec['attributes']['class']));

                    unset($optionSpec['attributes']['class']);
                }

                assert(is_array($optionSpec['attributes']));

                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }

            $labelAttributes['class'] = trim(implode(' ', array_unique($labelClasses)));
            $inputAttributes['class'] = trim(implode(' ', array_unique($inputClasses)));

            if (in_array($value, $selectedOptions, true)) {
                $selected = true;
            }

            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;

            if ($disabled) {
                $inputAttributes['aria-disabled'] = 'true';
            }

            $inputClasses = ['form-check-input'];

            if (array_key_exists('class', $inputAttributes)) {
                assert(is_string($inputAttributes['class']));
                $inputClasses = array_merge($inputClasses, explode(' ', $inputAttributes['class']));
            }

            $inputAttributes['class'] = trim(implode(' ', array_unique($inputClasses)));

            if (!is_array($labelAttributes)) {
                $labelAttributes = [];
            }

            $labelClasses = ['form-check-label'];

            if (array_key_exists('class', $labelAttributes) && is_string($labelAttributes['class'])) {
                $labelClasses = array_merge($labelClasses, explode(' ', $labelAttributes['class']));
            }

            $labelAttributes['class'] = trim(implode(' ', array_unique($labelClasses)));

            if (array_key_exists('id', $inputAttributes)) {
                $labelAttributes['for'] = $inputAttributes['id'];
            }

            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );

            assert(is_string($label));

            if (null !== $this->translate) {
                $label = ($this->translate)(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = ($this->escapeHtml)($label);
            }

            /** @var array<string, bool|string> $filteredAttributes */
            $filteredAttributes = array_filter(
                $labelAttributes,
                static fn ($key): bool => is_string($key),
                ARRAY_FILTER_USE_KEY
            );

            if (
                array_key_exists('id', $inputAttributes)
                && ($element instanceof LabelAwareInterface && !$element->getLabelOption('always_wrap'))
            ) {
                $labelOpen  = '';
                $labelClose = '';
                $label      = $indent . $this->getWhitespace(8) . $this->formLabel->openTag($filteredAttributes) . $label . $this->formLabel->closeTag();
                $input      = $indent . $this->getWhitespace(8) . $input;
            } else {
                $labelOpen  = $indent . $this->getWhitespace(4) . $this->formLabel->openTag($filteredAttributes) . PHP_EOL;
                $labelClose = PHP_EOL . $indent . $this->getWhitespace(4) . $this->formLabel->closeTag();
                $input      = $indent . $this->getWhitespace(8) . $input;
            }

            if (
                '' !== $label && !array_key_exists('id', $inputAttributes)
                || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
            ) {
                $label = '<span>' . $label . '</span>';

                if ('' !== $labelClose) {
                    $label = $indent . $this->getWhitespace(8) . $label;
                }
            }

            switch ($labelPosition) {
                case BaseFormRow::LABEL_PREPEND:
                    $markup = $labelOpen . $label . PHP_EOL . $input . $labelClose;
                    break;
                case BaseFormRow::LABEL_APPEND:
                default:
                    $markup = $labelOpen . $input . PHP_EOL . $label . $labelClose;
                    break;
            }

            $combinedMarkup[] = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('div', ['class' => $groupClasses], PHP_EOL . $markup . PHP_EOL . $indent . $this->getWhitespace(4));
        }

        return implode(PHP_EOL, $combinedMarkup);
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
