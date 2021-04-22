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
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;

use function array_key_exists;
use function array_merge;
use function explode;
use function implode;
use function sprintf;
use function trim;

final class FormCheckbox extends FormInput
{
    private PartialRendererInterface $renderer;
    private ?Translate $translate;
    private EscapeHtml $escaper;

    public function __construct(PartialRendererInterface $renderer, EscapeHtml $escaper, ?Translate $translator = null)
    {
        $this->renderer  = $renderer;
        $this->escaper   = $escaper;
        $this->translate = $translator;
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

        $attributes          = $element->getAttributes();
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

        $rendered = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        $hidden = '';

        if ($element->useHiddenElement()) {
            $hiddenAttributes = [
                'disabled' => $attributes['disabled'] ?? null,
                'name' => $attributes['name'],
                'value' => $element->getUncheckedValue(),
            ];

            $hidden = sprintf(
                '<input type="hidden" %s%s',
                $this->createAttributesString($hiddenAttributes),
                $closingBracket
            );
        }

        return $this->renderer->render(
            'elements::checkbox',
            [
                'attributes' => $attributes,
                'useHidden' => $element->useHiddenElement(),
                'hidden' => $hidden,
                'labelAttributes' => $labelAttributes,
                'label' => $label,
                'element' => $rendered,
                'groupClasses' => $groupClasses,
            ]
        );
    }

    /**
     * Return input type
     */
    protected function getInputType(): string
    {
        return 'checkbox';
    }
}
