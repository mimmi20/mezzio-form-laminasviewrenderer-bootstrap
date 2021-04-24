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
use Mezzio\LaminasViewHelper\Helper\HtmlElement;
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;
use Traversable;

use function array_key_exists;
use function array_merge;
use function assert;
use function explode;
use function implode;
use function is_array;
use function is_scalar;
use function is_string;
use function iterator_to_array;
use function method_exists;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormSelect extends AbstractHelper
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
    private FormHidden $formHidden;
    private PartialRendererInterface $renderer;
    private ?Translate $translate;
    private EscapeHtml $escaper;
    private HtmlElement $htmlElement;

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
        HtmlElement $htmlElement,
        PartialRendererInterface $renderer,
        EscapeHtml $escaper,
        FormHidden $formHidden,
        ?Translate $translator = null
    ) {
        $this->renderer    = $renderer;
        $this->htmlElement = $htmlElement;
        $this->formHidden  = $formHidden;
        $this->escaper     = $escaper;
        $this->translate   = $translator;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return FormSelect|string
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
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

        if ($attributes instanceof Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        $value = $this->validateMultiValue($element->getValue(), $attributes);

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

        $indent        = $this->getIndent();
        $optionContent = [];

        foreach ($options as $key => $option) {
            $optionContent[] = $this->renderOption($key, $option, $value, 1);
        }

        $rendered = $indent . $this->htmlElement->toHtml('select', $attributes, PHP_EOL . implode(PHP_EOL, $optionContent) . PHP_EOL . $indent);

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
    public function renderOptions(array $options, array $selectedOptions = [], int $level = 0): string
    {
        $optionStrings = [];

        foreach ($options as $key => $optionSpec) {
            $optionStrings[] = $this->renderOption($key, $optionSpec, $selectedOptions, $level);
        }

        return implode(PHP_EOL, $optionStrings);
    }

    /**
     * @param int|string                   $key
     * @param array<string, string>|string $optionSpec
     * @param array<int|string, string>    $selectedOptions
     */
    public function renderOption($key, $optionSpec, array $selectedOptions = [], int $level = 0): string
    {
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

        assert(is_string($label));

        if ('' !== $label && null !== $this->translate) {
            $label = ($this->translate)($label, $this->getTranslatorTextDomain());
        }

        if ('' !== $label && isset($optionSpec['disable_html_escape'])) {
            $label = ($this->escaper)($label);
        }

        $attributes = [
            'value' => $value,
            'selected' => $selected ? true : null,
            'disabled' => $disabled ? true : null,
        ];

        if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes'])) {
            $attributes = array_merge($attributes, $optionSpec['attributes']);
        }

        $this->validTagAttributes = $this->validOptionAttributes;

        $content = $this->htmlElement->toHtml('option', $attributes, $label);
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
     * @param array<string, int|string> $optgroup
     * @param array<int|string, string> $selectedOptions
     */
    public function renderOptgroup(array $optgroup, array $selectedOptions = [], int $level = 0): string
    {
        $options = [];
        if (isset($optgroup['options']) && is_array($optgroup['options'])) {
            $options = $optgroup['options'];
            unset($optgroup['options']);
        }

        $this->validTagAttributes = $this->validOptgroupAttributes;

        return $this->htmlElement->toHtml(
            'optgroup',
            $optgroup,
            $this->renderOptions($options, $selectedOptions, $level + 1)
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

    /**
     * @throws Exception\DomainException
     * @throws Exception\InvalidArgumentException
     */
    private function renderHiddenElement(SelectElement $element): string
    {
        $hiddenElement = new Hidden($element->getName());
        $hiddenElement->setValue($element->getUnselectedValue());

        return $this->formHidden->render($hiddenElement);
    }
}
