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

use Laminas\Form\Element\Checkbox as CheckboxElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormInput;
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasViewHelper\Helper\HtmlElement;
use Traversable;

use function array_key_exists;
use function array_merge;
use function explode;
use function implode;
use function iterator_to_array;
use function method_exists;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormCheckbox extends FormInput
{
    use FormTrait;
    use LabelPositionTrait;
    use UseHiddenElementTrait;

    private ?Translate $translate;
    private EscapeHtml $escaper;
    private HtmlElement $htmlElement;
    private FormLabel $formLabel;

    public function __construct(
        FormLabel $formLabel,
        HtmlElement $htmlElement,
        EscapeHtml $escaper,
        ?Translate $translator = null
    ) {
        $this->escaper     = $escaper;
        $this->htmlElement = $htmlElement;
        $this->translate   = $translator;
        $this->formLabel   = $formLabel;
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof CheckboxElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\Checkbox',
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

        $label = $element->getLabel();

        if (!empty($label) && null !== $this->translate) {
            $label = ($this->translate)($label, $this->getTranslatorTextDomain());
        }

        if (!empty($label) && (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape'))) {
            $label = ($this->escaper)($label);
        }

        $id = $this->getId($element);
        if (null === $id) {
            throw new Exception\DomainException(
                sprintf(
                    '%s expects the Element provided to have either a name or an id present; neither found',
                    __METHOD__
                )
            );
        }

        $groupClasses = ['form-check'];
        $labelClasses = ['form-check-label'];
        $inputClasses = ['form-check-input'];

        if (Form::LAYOUT_INLINE === $element->getOption('layout')) {
            $groupClasses[] = 'form-check-inline';
        }

        $labelAttributes = [];

        if ($element instanceof LabelAwareInterface) {
            $labelAttributes = $element->getLabelAttributes();
        }

        $labelAttributes = array_merge($labelAttributes, ['for' => $id]);

        if (array_key_exists('class', $labelAttributes)) {
            $labelClasses = array_merge($labelClasses, explode(' ', $labelAttributes['class']));
        }

        $labelAttributes['class'] = trim(implode(' ', $labelClasses));

        $attributes = $element->getAttributes();

        if ($attributes instanceof Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        $attributes['name']  = $name;
        $attributes['type']  = $this->getInputType();
        $attributes['value'] = $element->getCheckedValue();
        $closingBracket      = $this->getInlineClosingBracket();

        if ($element->isChecked()) {
            $attributes['checked'] = true;
        }

        if (array_key_exists('class', $attributes)) {
            $inputClasses = array_merge($inputClasses, explode(' ', $attributes['class']));
        }

        $attributes['class'] = trim(implode(' ', $inputClasses));

        $indent = $this->getIndent();

        if (
            array_key_exists('id', $attributes)
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
            '' !== $label && !array_key_exists('id', $attributes)
            || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
        ) {
            $label = '<span>' . $label . '</span>';
        }

        $rendered = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        $rendered = $indent . $this->getWhitespace(4) . $rendered;

        $hidden = '';

        // Render hidden element
        $useHiddenElement = method_exists($element, 'useHiddenElement') && $element->useHiddenElement()
            ? $element->useHiddenElement()
            : $this->useHiddenElement;

        if ($useHiddenElement) {
            $hidden = $this->renderHiddenElement($element, $attributes);
            $hidden = $indent . $this->getWhitespace(4) . $hidden . PHP_EOL;
        }

        $labelPosition = $this->getLabelPosition();

        switch ($labelPosition) {
            case BaseFormRow::LABEL_PREPEND:
                $rendered = $labelOpen . $label . PHP_EOL . $indent . $this->getWhitespace(4) . $rendered . $labelClose;
                break;
            case BaseFormRow::LABEL_APPEND:
            default:
                $rendered = $labelOpen . $rendered . PHP_EOL . $indent . $this->getWhitespace(4) . $label . $labelClose;
                break;
        }

        return $indent . $this->htmlElement->toHtml('div', ['class' => $groupClasses], PHP_EOL . $hidden . $rendered . PHP_EOL . $indent);
    }

    /**
     * Return input type
     */
    protected function getInputType(): string
    {
        return 'checkbox';
    }

    /**
     * Render a hidden element for empty/unchecked value
     *
     * @param array<string, bool> $attributes
     */
    private function renderHiddenElement(CheckboxElement $element, array $attributes): string
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
