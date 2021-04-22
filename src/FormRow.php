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

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\MonthSelect;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Radio;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\FormInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;

use function array_key_exists;
use function array_merge;
use function assert;
use function explode;
use function get_class;
use function gettype;
use function implode;
use function is_array;
use function is_object;
use function sprintf;
use function trim;

final class FormRow extends BaseFormRow
{
    private FormElement $formElement;
    private FormElementErrors $formElementErrors;
    private EscapeHtml $escapeHtml;
    private PartialRendererInterface $renderer;
    private ?Translate $translate;

    public function __construct(
        FormElement $formElement,
        FormElementErrors $formElementErrors,
        EscapeHtml $escapeHtml,
        PartialRendererInterface $renderer,
        ?Translate $translator = null
    ) {
        $this->formElement       = $formElement;
        $this->formElementErrors = $formElementErrors;
        $this->escapeHtml        = $escapeHtml;
        $this->renderer          = $renderer;
        $this->translate         = $translator;
    }

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param string|null $labelPosition
     *
     * @throws Exception\DomainException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function render(ElementInterface $element, $labelPosition = null): string
    {
        $form = $element->getOption('form');
        assert(
            $form instanceof FormInterface || null === $form,
            sprintf(
                '$form should be an Instance of %s or null, but was %s',
                FormInterface::class,
                is_object($form) ? get_class($form) : gettype($form)
            )
        );

        if (
            null !== $form
            && !$element->getAttribute('required')
            && $form->getInputFilter()->get($element->getName())->isRequired()
        ) {
            $element->setAttribute('required', true);
        }

        $label = $element->getLabel() ?? '';

        if (null === $labelPosition) {
            $labelPosition = $this->labelPosition;
        }

        if ('' !== $label) {
            // Translate the label
            if (null !== $this->translate) {
                $label = ($this->translate)($label, $this->getTranslatorTextDomain());
            }
        }

        // Does this element have errors ?
        if ($element->getMessages()) {
            $classAttributes  = $element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '';
            $classAttributes .= 'is-invalid';

            $element->setAttribute('class', $classAttributes);
        }

        if ($this->partial) {
            $vars = [
                'element' => $element,
                'label' => $label,
                'labelAttributes' => $this->labelAttributes,
                'labelPosition' => $labelPosition,
                'renderErrors' => $this->renderErrors,
            ];

            return $this->renderer->render($this->partial, $vars);
        }

        $elementErrors = '';

        if ($this->renderErrors) {
            $elementErrors = $this->formElementErrors->render($element);

            if ($elementErrors) {
                $ariaDesc = $element->hasAttribute('aria-describedby') ? $element->getAttribute('aria-describedby') . ' ' : '';

                $ariaDesc .= $element->getAttribute('id') . 'Feedback';

                $element->setAttribute('aria-describedby', $ariaDesc);
            }
        }

        $elementString = $this->formElement->render($element);

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');

        if ('hidden' === $type) {
            return $elementString . $elementErrors;
        }

        if ('' !== $label && (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape'))) {
            $label = ($this->escapeHtml)($label);
        }

        if (Form::LAYOUT_HORIZONTAL === $element->getOption('layout')) {
            return $this->renderHorizontalRow($element, $label, $form);
        }

        if ('' !== $label) {
            return $this->renderVerticalRow($element, $label, $labelPosition);
        }

        $markup = $elementString;

        if ($this->renderErrors) {
            $markup .= $elementErrors;
        }

        return $markup;
    }

    private function renderHorizontalRow(
        ElementInterface $element,
        string $label,
        ?FormInterface $form
    ): string {
        $rowClasses   = ['row'];
        $labelClasses = ['col-form-label'];
        $colClasses   = [];

        $rowAttributes   = $element->getOption('row_attributes') ?? [];
        $colAttributes   = $element->getOption('col_attributes') ?? [];
        $labelAttributes = $element->getOption('label_col_attributes') ?? [];

        if (null !== $form) {
            $formRowAttributes   = $form->getOption('row_attributes') ?? [];
            $formColAttributes   = $form->getOption('col_attributes') ?? [];
            $formLabelAttributes = $form->getOption('label_col_attributes') ?? [];

            if (array_key_exists('class', $formRowAttributes)) {
                $rowClasses = array_merge($rowClasses, explode(' ', $formRowAttributes['class']));

                unset($formRowAttributes['class']);
            }

            if (array_key_exists('class', $formColAttributes)) {
                $colClasses = array_merge($colClasses, explode(' ', $formColAttributes['class']));

                unset($formColAttributes['class']);
            }

            if (array_key_exists('class', $formLabelAttributes)) {
                $labelClasses = array_merge($labelClasses, explode(' ', $formLabelAttributes['class']));

                unset($formLabelAttributes['class']);
            }

            $rowAttributes   = array_merge($formRowAttributes, $rowAttributes);
            $colAttributes   = array_merge($formColAttributes, $colAttributes);
            $labelAttributes = array_merge($formLabelAttributes, $labelAttributes);
        }

        assert(is_array($rowAttributes));
        assert(is_array($colAttributes));
        assert(is_array($labelAttributes));

        if (array_key_exists('class', $rowAttributes)) {
            $rowClasses = array_merge($rowClasses, explode(' ', $rowAttributes['class']));
        }

        if (array_key_exists('class', $colAttributes)) {
            $colClasses = array_merge($colClasses, explode(' ', $colAttributes['class']));
        }

        if (array_key_exists('class', $labelAttributes)) {
            $labelClasses = array_merge($labelClasses, explode(' ', $labelAttributes['class']));
        }

        $rowAttributes['class']   = trim(implode(' ', $rowClasses));
        $colAttributes['class']   = trim(implode(' ', $colClasses));
        $labelAttributes['class'] = trim(implode(' ', $labelClasses));

        // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
        // labels. The semantic way is to group them inside a fieldset
        if (
            $element instanceof MultiCheckbox
            || $element instanceof Radio
            || $element instanceof MonthSelect
            || $element instanceof Captcha
        ) {
            return $this->renderer->render(
                'horizontal::fieldset-row',
                [
                    'rowAttributes' => $rowAttributes,
                    'labelAttributes' => $labelAttributes,
                    'colAttributes' => $colAttributes,
                    'label' => $label,
                    'element' => $element,
                    'renderErrors' => $this->renderErrors,
                ]
            );
        }

        if ($element instanceof Button || $element instanceof Checkbox) {
            // Button element is a special case, because label is always rendered inside it
            return $this->renderer->render(
                'horizontal::button-row',
                [
                    'rowAttributes' => $rowAttributes,
                    'labelAttributes' => $labelAttributes,
                    'colAttributes' => $colAttributes,
                    'label' => $label,
                    'element' => $element,
                    'renderErrors' => $this->renderErrors,
                ]
            );
        }

        if ($element->hasAttribute('id')) {
            $labelAttributes['for'] = $element->getAttribute('id');
        }

        return $this->renderer->render(
            'horizontal::row',
            [
                'rowAttributes' => $rowAttributes,
                'labelAttributes' => $labelAttributes,
                'colAttributes' => $colAttributes,
                'label' => $label,
                'element' => $element,
                'renderErrors' => $this->renderErrors,
            ]
        );
    }

    private function renderVerticalRow(
        ElementInterface $element,
        string $label,
        ?string $labelPosition = null
    ): string {
        $labelClasses = ['form-label'];
        $colClasses   = [];

        $colAttributes   = $element->getOption('col_attributes') ?? [];
        $labelAttributes = $element->getOption('label_attributes') ?? [];

        assert(is_array($colAttributes));
        assert(is_array($labelAttributes));

        if (array_key_exists('class', $colAttributes)) {
            $colClasses[] = $colAttributes['class'];
        }

        if (array_key_exists('class', $labelAttributes)) {
            $labelClasses[] = $labelAttributes['class'];
        }

        $colAttributes['class']   = trim(implode(' ', $colClasses));
        $labelAttributes['class'] = trim(implode(' ', $labelClasses));

        if ($element->hasAttribute('id')) {
            $labelAttributes['for'] = $element->getAttribute('id');
        }

        // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
        // labels. The semantic way is to group them inside a fieldset
        if (
            $element instanceof MultiCheckbox
            || $element instanceof Radio
            || $element instanceof MonthSelect
            || $element instanceof Captcha
        ) {
            return $this->renderer->render(
                'vertical::fieldset-row',
                [
                    'labelAttributes' => $labelAttributes,
                    'colAttributes' => $colAttributes,
                    'label' => $label,
                    'element' => $element,
                    'renderErrors' => $this->renderErrors,
                ]
            );
        }

        if ($element instanceof Button) {
            // Button element is a special case, because label is always rendered inside it
            return $this->renderer->render(
                'vertical::button-row',
                [
                    'labelAttributes' => $labelAttributes,
                    'colAttributes' => $colAttributes,
                    'label' => $label,
                    'element' => $element,
                    'renderErrors' => $this->renderErrors,
                ]
            );
        }

        if ($element instanceof LabelAwareInterface) {
            $floating = $element->getOption('floating');

            if ($floating) {
                $labelPosition = self::LABEL_APPEND;
            } elseif ($element->getLabelOption('label_position')) {
                $labelPosition = $element->getLabelOption('label_position');
            } else {
                $labelPosition = self::LABEL_PREPEND;
            }
        }

        return $this->renderer->render(
            'vertical::row',
            [
                'labelAttributes' => $labelAttributes,
                'colAttributes' => $colAttributes,
                'label' => $label,
                'element' => $element,
                'labelPosition' => $labelPosition,
                'renderErrors' => $this->renderErrors,
            ]
        );
    }
}
