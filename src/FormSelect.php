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

namespace Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\Stdlib\ArrayUtils;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Helper\EscapeHtml;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function gettype;
use function implode;
use function is_array;
use function is_scalar;
use function is_string;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormSelect extends AbstractHelper implements FormSelectInterface
{
    use FormTrait;

    /**
     * Attributes valid for the current tag
     *
     * Will vary based on whether a select, option, or optgroup is being rendered
     *
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $validTagAttributes;

    /**
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $translatableAttributes = ['label' => true];

    /**
     * Attributes valid for select
     *
     * @var array<string, bool>
     */
    private array $validSelectAttributes = [
        'name' => true,
        'autocomplete' => true,
        'autofocus' => true,
        'disabled' => true,
        'form' => true,
        'multiple' => true,
        'required' => true,
        'size' => true,
    ];

    /**
     * Attributes valid for options
     *
     * @var array<string, bool>
     */
    private array $validOptionAttributes = [
        'disabled' => true,
        'selected' => true,
        'label' => true,
        'value' => true,
    ];

    /**
     * Attributes valid for option groups
     *
     * @var array<string, bool>
     */
    private array $validOptgroupAttributes = [
        'disabled' => true,
        'label' => true,
    ];

    /** @throws void */
    public function __construct(
        private readonly EscapeHtml $escaper,
        private readonly FormHiddenInterface $formHidden,
        private readonly Translate | null $translate = null,
    ) {
        // nothing to do
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function __invoke(ElementInterface | null $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form <select> element from the provided $element
     *
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof SelectElement) {
            throw new \Laminas\Form\Exception\InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type %s',
                    __METHOD__,
                    SelectElement::class,
                ),
            );
        }

        $name = $element->getName();

        if (empty($name)) {
            throw new DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__,
                ),
            );
        }

        $options     = $element->getValueOptions();
        $emptyOption = $element->getEmptyOption();

        if ($emptyOption !== null) {
            $options = ['' => $emptyOption] + $options;
        }

        $attributes = $element->getAttributes();
        $value      = $this->validateMultiValue($element->getValue(), $attributes);

        $attributes['name'] = $name;

        if (array_key_exists('multiple', $attributes) && $attributes['multiple']) {
            $attributes['name'] .= '[]';
        }

        $classes = ['form-select'];

        if (array_key_exists('class', $attributes)) {
            $classes = array_merge($classes, explode(' ', (string) $attributes['class']));
        }

        $attributes['class'] = trim(implode(' ', array_unique($classes)));

        $indent        = $this->getIndent();
        $optionContent = [];

        foreach ($options as $key => $option) {
            $optionContent[] = $this->renderOption($key, $option, $value, 1);
        }

        $this->validTagAttributes = $this->validSelectAttributes;

        $rendered = sprintf(
            '<select %s>%s</select>',
            $this->createAttributesString($attributes),
            PHP_EOL . implode(PHP_EOL, $optionContent) . PHP_EOL . $indent,
        );

        $rendered = $indent . $rendered;

        // Render hidden element
        $useHiddenElement = $element->useHiddenElement();

        if ($useHiddenElement) {
            $rendered = $indent . $this->renderHiddenElement($element) . PHP_EOL . $rendered;
        }

        return $rendered;
    }

    /**
     * Render an array of options
     *
     * Individual options should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'selected' => $booleanFlag,
     * )
     * </code>
     *
     * @param array<int|string, array<string, string>|string> $options
     * @param array<int|string, string>                       $selectedOptions Option values that should be marked as selected
     * @phpstan-param array<int|string, array{options?: array<mixed>, value?: string, label?: string, selected?: bool, disabled?: bool, disable_html_escape?: bool, attributes?: array<string, string>}|string> $options
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function renderOptions(array $options, array $selectedOptions, int $level): string
    {
        $optionStrings = [];

        foreach ($options as $key => $optionSpec) {
            $optionStrings[] = $this->renderOption($key, $optionSpec, $selectedOptions, $level);
        }

        return implode(PHP_EOL, $optionStrings);
    }

    /**
     * @param array<string, string>|string $optionSpec
     * @param array<int|string, string>    $selectedOptions
     * @phpstan-param array{options?: array<mixed>, value?: string, label?: string, selected?: bool, disabled?: bool, disable_html_escape?: bool, attributes?: array<string, string>}|string $optionSpec
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function renderOption(
        int | string $key,
        array | string $optionSpec,
        array $selectedOptions,
        int $level,
    ): string {
        $value    = '';
        $label    = '';
        $selected = false;
        $disabled = false;

        if (is_scalar($optionSpec)) {
            $optionSpec = [
                'label' => $optionSpec,
                'value' => $key,
            ];
        }

        if (isset($optionSpec['options']) && is_array($optionSpec['options'])) {
            return $this->renderOptgroup($optionSpec, $selectedOptions, $level);
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

        if (ArrayUtils::inArray($value, $selectedOptions)) {
            $selected = true;
        }

        assert(
            is_string($label),
            sprintf(
                '$label should be a string, but was %s',
                gettype($label),
            ),
        );

        if ($label !== '') {
            if ($this->translate !== null) {
                $label = ($this->translate)($label, $this->getTranslatorTextDomain());
            }

            if (!isset($optionSpec['disable_html_escape'])) {
                $label = ($this->escaper)($label);

                assert(is_string($label));
            }
        }

        $attributes = [
            'value' => $value,
            'selected' => $selected ? true : null,
            'disabled' => $disabled ? true : null,
        ];

        if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes'])) {
            $attributes = [...$attributes, ...$optionSpec['attributes']];
        }

        $this->validTagAttributes = $this->validOptionAttributes;

        $attributesString = $this->createAttributesString($attributes);

        if (!empty($attributesString)) {
            $attributesString = ' ' . $attributesString;
        }

        $content = sprintf('<option%s>%s</option>', $attributesString, $label);
        $indent  = $this->getIndent();

        return $indent . $this->getWhitespace($level * 4) . $content;
    }

    /**
     * Render an optgroup
     *
     * See {@link renderOptions()} for the options specification. Basically,
     * an optgroup is simply an option that has an additional "options" key
     * with an array following the specification for renderOptions().
     *
     * @param array<string, array<mixed>|bool|int|string> $optgroup
     * @param array<int|string, string>                   $selectedOptions
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function renderOptgroup(array $optgroup, array $selectedOptions, int $level): string
    {
        $options = [];

        if (array_key_exists('options', $optgroup)) {
            if (is_array($optgroup['options'])) {
                /** @phpstan-var array<int|string, array{options?: array<mixed>, value?: string, label?: string, selected?: bool, disabled?: bool, disable_html_escape?: bool, attributes?: array<string, string>}|string> $options */
                $options = $optgroup['options'];
            }

            unset($optgroup['options']);
        }

        $this->validTagAttributes = $this->validOptgroupAttributes;
        $attributes               = $this->createAttributesString($optgroup);

        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        $indent  = $this->getIndent();
        $indent .= $this->getWhitespace($level * 4);
        $content = sprintf(
            '<optgroup%s>%s</optgroup>',
            $attributes,
            PHP_EOL . $this->renderOptions($options, $selectedOptions, $level + 1) . PHP_EOL . $indent,
        );

        return $indent . $content;
    }

    /**
     * Ensure that the value is set appropriately
     *
     * If the element's value attribute is an array, but there is no multiple
     * attribute, or that attribute does not evaluate to true, then we have
     * a domain issue -- you cannot have multiple options selected unless the
     * multiple attribute is present and enabled.
     *
     * @param array<string, scalar|null> $attributes
     *
     * @return array<int|string, string>
     *
     * @throws DomainException
     */
    private function validateMultiValue(mixed $value, array $attributes): array
    {
        if ($value === null) {
            return [];
        }

        if (is_string($value)) {
            return [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        if (!array_key_exists('multiple', $attributes) || !$attributes['multiple']) {
            throw new DomainException(
                sprintf(
                    '%s does not allow specifying multiple selected values when the element does not have a multiple attribute set to a boolean true',
                    self::class,
                ),
            );
        }

        return $value;
    }

    /**
     * @throws DomainException
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     */
    private function renderHiddenElement(SelectElement $element): string
    {
        $hiddenElement = new Hidden($element->getName());
        $hiddenElement->setValue($element->getUnselectedValue());

        return $this->formHidden->render($hiddenElement);
    }
}
