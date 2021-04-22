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

use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\Form\View\Helper\FormHidden;
use Laminas\I18n\View\Helper\Translate;
use Laminas\Stdlib\ArrayUtils;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;

use function array_key_exists;
use function array_merge;
use function explode;
use function implode;
use function is_array;
use function is_scalar;
use function method_exists;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormSelect extends AbstractHelper
{
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
    private FormHidden $formHidden;
    private PartialRendererInterface $renderer;
    private ?Translate $translate;
    private EscapeHtml $escaper;

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

    public function __construct(
        PartialRendererInterface $renderer,
        EscapeHtml $escaper,
        FormHidden $formHidden,
        ?Translate $translator = null
    ) {
        $this->renderer   = $renderer;
        $this->formHidden = $formHidden;
        $this->escaper    = $escaper;
        $this->translate  = $translator;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return FormSelect|string
     */
    public function __invoke(?ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form <select> element from the provided $element
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof SelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\Select',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if (empty($name) && 0 !== $name) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $options     = $element->getValueOptions();
        $emptyOption = $element->getEmptyOption();

        if (null !== $emptyOption) {
            $options = ['' => $emptyOption] + $options;
        }

        $attributes = $element->getAttributes();
        $value      = $this->validateMultiValue($element->getValue(), $attributes);

        $attributes['name'] = $name;
        if (array_key_exists('multiple', $attributes) && $attributes['multiple']) {
            $attributes['name'] .= '[]';
        }

        $this->validTagAttributes = $this->validSelectAttributes;

        $classes = ['form-select'];

        if (array_key_exists('class', $attributes)) {
            $classes = array_merge($classes, explode(' ', $attributes['class']));
        }

        $attributes['class'] = trim(implode(' ', $classes));

        $rendered = $this->renderer->render(
            'elements::select',
            [
                'attributes' => $attributes,
                'options' => $options,
                'value' => $value,
            ]
        );

        // Render hidden element
        $useHiddenElement = method_exists($element, 'useHiddenElement')
            && method_exists($element, 'getUnselectedValue')
            && $element->useHiddenElement();

        if ($useHiddenElement) {
            $rendered = $this->renderHiddenElement($element) . PHP_EOL . $rendered;
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
     */
    public function renderOptions(array $options, array $selectedOptions = []): string
    {
        $optionStrings = [];

        foreach ($options as $key => $optionSpec) {
            $optionStrings[] = $this->renderOption($key, $optionSpec, $selectedOptions);
        }

        return implode('', $optionStrings);
    }

    /**
     * @param int|string                   $key
     * @param array<string, string>|string $optionSpec
     * @param array<int|string, string>    $selectedOptions
     */
    public function renderOption($key, $optionSpec, array $selectedOptions = []): string
    {
        $template = '<option %s>%s</option>';

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
            return $this->renderOptgroup($optionSpec, $selectedOptions);
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

        if (null !== $this->translate) {
            $label = ($this->translate)($label, $this->getTranslatorTextDomain());
        }

        $attributes = [
            'value' => $value,
            'selected' => $selected,
            'disabled' => $disabled,
        ];

        if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes'])) {
            $attributes = array_merge($attributes, $optionSpec['attributes']);
        }

        $this->validTagAttributes = $this->validOptionAttributes;

        return sprintf(
            $template,
            $this->createAttributesString($attributes),
            ($this->escaper)($label)
        );
    }

    /**
     * Render an optgroup
     *
     * See {@link renderOptions()} for the options specification. Basically,
     * an optgroup is simply an option that has an additional "options" key
     * with an array following the specification for renderOptions().
     *
     * @param array<string, string>     $optgroup
     * @param array<int|string, string> $selectedOptions
     */
    public function renderOptgroup(array $optgroup, array $selectedOptions = []): string
    {
        $template = '<optgroup%s>%s</optgroup>';

        $options = [];
        if (isset($optgroup['options']) && is_array($optgroup['options'])) {
            $options = $optgroup['options'];
            unset($optgroup['options']);
        }

        $this->validTagAttributes = $this->validOptgroupAttributes;
        $attributes               = $this->createAttributesString($optgroup);
        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        return sprintf(
            $template,
            $attributes,
            $this->renderOptions($options, $selectedOptions)
        );
    }

    /**
     * Ensure that the value is set appropriately
     *
     * If the element's value attribute is an array, but there is no multiple
     * attribute, or that attribute does not evaluate to true, then we have
     * a domain issue -- you cannot have multiple options selected unless the
     * multiple attribute is present and enabled.
     *
     * @param mixed               $value
     * @param array<string, bool> $attributes
     *
     * @return array<int|string, mixed>
     *
     * @throws Exception\DomainException
     */
    private function validateMultiValue($value, array $attributes): array
    {
        if (null === $value) {
            return [];
        }

        if (!is_array($value)) {
            return [$value];
        }

        if (!isset($attributes['multiple']) || !$attributes['multiple']) {
            throw new Exception\DomainException(sprintf(
                '%s does not allow specifying multiple selected values when the element does not have a multiple '
                . 'attribute set to a boolean true',
                self::class
            ));
        }

        return $value;
    }

    private function renderHiddenElement(ElementInterface $element): string
    {
        $hiddenElement = new Hidden($element->getName());
        $hiddenElement->setValue($element->getUnselectedValue());

        return ($this->formHidden)($hiddenElement);
    }
}
