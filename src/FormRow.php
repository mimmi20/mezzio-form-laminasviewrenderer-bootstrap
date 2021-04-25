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
use Laminas\Form\Element\Submit;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\FormInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasViewHelper\Helper\HtmlElement;
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function get_class;
use function gettype;
use function implode;
use function is_array;
use function is_object;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormRow extends BaseFormRow
{
    use FormTrait;

    private FormElement $formElement;
    private FormElementErrors $formElementErrors;
    private EscapeHtml $escapeHtml;
    private PartialRendererInterface $renderer;
    private HtmlElement $htmlElement;
    private ?Translate $translate;

    public function __construct(
        FormElement $formElement,
        FormElementErrors $formElementErrors,
        HtmlElement $htmlElement,
        EscapeHtml $escapeHtml,
        PartialRendererInterface $renderer,
        ?Translate $translator = null
    ) {
        $this->formElement       = $formElement;
        $this->formElementErrors = $formElementErrors;
        $this->htmlElement       = $htmlElement;
        $this->escapeHtml        = $escapeHtml;
        $this->renderer          = $renderer;
        $this->translate         = $translator;
    }

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param string|null $labelPosition
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws Exception\DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
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
            && $form->getInputFilter()->has($element->getName())
            && $form->getInputFilter()->get($element->getName())->isRequired()
        ) {
            $element->setAttribute('required', true);
        }

        $label = $element->getLabel() ?? '';

        if (null === $labelPosition) {
            $labelPosition = $this->getLabelPosition();
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

        $indent = $this->getIndent();

        if ($this->partial) {
            $vars = [
                'element' => $element,
                'label' => $label,
                'labelAttributes' => $this->labelAttributes,
                'labelPosition' => $labelPosition,
                'renderErrors' => $this->renderErrors,
                'indent' => $indent,
            ];

            return $this->renderer->render($this->partial, $vars);
        }

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');

        if ('hidden' === $type) {
            $this->formElement->setIndent($indent);
            $markup = $this->formElement->render($element);

            if ($this->renderErrors) {
                $markup .= $this->renderFormErrors($element, $indent . $this->getWhitespace(4));
            }

            return $markup;
        }

        if ('' !== $label && (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape'))) {
            $label = ($this->escapeHtml)($label);
        }

        if ($element->getOption('show-required-mark')) {
            $label .= $element->getOption('field-required-mark');
        }

        if (Form::LAYOUT_HORIZONTAL === $element->getOption('layout')) {
            return $this->renderHorizontalRow($element, $label, $form);
        }

        if ('' !== $label) {
            return $this->renderVerticalRow($element, $label, $labelPosition);
        }

        $this->formElement->setIndent($indent . $this->getWhitespace(4));
        $markup = $this->formElement->render($element);

        if ($this->renderErrors) {
            $markup .= $this->renderFormErrors($element, $indent . $this->getWhitespace(4));
        }

        return $markup;
    }

    /**
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws Exception\DomainException
     */
    private function renderHorizontalRow(
        ElementInterface $element,
        string $label,
        ?FormInterface $form
    ): string {
        $rowClasses   = ['row'];
        $labelClasses = ['col-form-label'];
        $colClasses   = [];

        $rowAttributes      = $element->getOption('row_attributes') ?? [];
        $colAttributes      = $element->getOption('col_attributes') ?? [];
        $labelAttributes    = $element->getOption('label_attributes') ?? [];
        $labelColAttributes = $element->getOption('label_col_attributes') ?? [];

        if (array_key_exists('class', $labelColAttributes)) {
            $labelClasses = array_merge($labelClasses, explode(' ', $labelColAttributes['class']));

            unset($labelColAttributes['class']);
        }

        $labelAttributes = array_merge($labelColAttributes, $labelAttributes);

        if (null !== $form) {
            $formRowAttributes      = $form->getOption('row_attributes') ?? [];
            $formColAttributes      = $form->getOption('col_attributes') ?? [];
            $formLabelColAttributes = $form->getOption('label_col_attributes') ?? [];

            if (array_key_exists('class', $formRowAttributes)) {
                $rowClasses = array_merge($rowClasses, explode(' ', $formRowAttributes['class']));

                unset($formRowAttributes['class']);
            }

            if (array_key_exists('class', $formColAttributes)) {
                $colClasses = array_merge($colClasses, explode(' ', $formColAttributes['class']));

                unset($formColAttributes['class']);
            }

            if (array_key_exists('class', $formLabelColAttributes)) {
                $labelClasses = array_merge($labelClasses, explode(' ', $formLabelColAttributes['class']));

                unset($formLabelColAttributes['class']);
            }

            $rowAttributes   = array_merge($formRowAttributes, $rowAttributes);
            $colAttributes   = array_merge($formColAttributes, $colAttributes);
            $labelAttributes = array_merge($formLabelColAttributes, $labelAttributes);
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

        $rowAttributes['class']   = trim(implode(' ', array_unique($rowClasses)));
        $colAttributes['class']   = trim(implode(' ', array_unique($colClasses)));
        $labelAttributes['class'] = trim(implode(' ', array_unique($labelClasses)));

        $indent = $this->getIndent();

        // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
        // labels. The semantic way is to group them inside a fieldset
        if (
            $element instanceof Radio
            || $element instanceof MultiCheckbox
            || $element instanceof MonthSelect
            || $element instanceof Captcha
        ) {
            $legend = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('legend', $labelAttributes, $label) . PHP_EOL;

            $this->formElement->setIndent($indent . $this->getWhitespace(4));
            $elementString = $this->formElement->render($element);

            if ($this->renderErrors) {
                $elementString .= $this->renderFormErrors($element, $indent . $this->getWhitespace(4));
            }

            $outerDiv = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('div', $colAttributes, PHP_EOL . $elementString . PHP_EOL . $indent . $this->getWhitespace(4));

            return $indent . $this->htmlElement->toHtml('fieldset', $rowAttributes, PHP_EOL . $legend . $outerDiv . PHP_EOL . $indent);
        }

        if ($element instanceof Button || $element instanceof Submit || $element instanceof Checkbox) {
            // Button element is a special case, because label is always rendered inside it
            $this->formElement->setIndent($indent . $this->getWhitespace(8));
            $elementString = $this->formElement->render($element);

            if ($this->renderErrors) {
                $elementString .= $this->renderFormErrors($element, $indent . $this->getWhitespace(8));
            }

            $outerDiv = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('div', $colAttributes, PHP_EOL . $elementString . PHP_EOL . $indent . $this->getWhitespace(4));

            return $indent . $this->htmlElement->toHtml('div', $rowAttributes, PHP_EOL . $outerDiv . PHP_EOL . $indent);
        }

        if ($element->hasAttribute('id')) {
            $labelAttributes['for'] = $element->getAttribute('id');
        }

        $legend = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('label', $labelAttributes, $label) . PHP_EOL;

        $this->formElement->setIndent($indent . $this->getWhitespace(8));
        $elementString = $this->formElement->render($element);

        if ($this->renderErrors) {
            $elementString .= $this->renderFormErrors($element, $indent . $this->getWhitespace(8));
        }

        $outerDiv = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('div', $colAttributes, PHP_EOL . $elementString . PHP_EOL . $indent . $this->getWhitespace(4));

        return $indent . $this->htmlElement->toHtml('div', $rowAttributes, PHP_EOL . $legend . $outerDiv . PHP_EOL . $indent);
    }

    /**
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws Exception\DomainException
     */
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
            $labelClasses = array_merge($labelClasses, explode(' ', $labelAttributes['class']));
        }

        $colAttributes['class']   = trim(implode(' ', array_unique($colClasses)));
        $labelAttributes['class'] = trim(implode(' ', array_unique($labelClasses)));

        if ($element->hasAttribute('id')) {
            $labelAttributes['for'] = $element->getAttribute('id');
        }

        $indent = $this->getIndent();

        // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
        // labels. The semantic way is to group them inside a fieldset
        if (
            $element instanceof Radio
            || $element instanceof MultiCheckbox
            || $element instanceof MonthSelect
            || $element instanceof Captcha
        ) {
            $legend = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('label', $labelAttributes, $label) . PHP_EOL;

            $this->formElement->setIndent($indent . $this->getWhitespace(4));
            $elementString = $indent . $this->getWhitespace(4) . $this->formElement->render($element);

            if ($this->renderErrors) {
                $elementString .= $this->renderFormErrors($element, $indent . $this->getWhitespace(8));
            }

            return $indent . $this->htmlElement->toHtml('fieldset', $colAttributes, PHP_EOL . $legend . $elementString . PHP_EOL . $indent);
        }

        if ($element instanceof Button || $element instanceof Submit || $element instanceof Checkbox) {
            // Button element is a special case, because label is always rendered inside it
            $this->formElement->setIndent($indent . $this->getWhitespace(4));
            $elementString = $this->formElement->render($element);

            if ($this->renderErrors) {
                $elementString .= $this->renderFormErrors($element, $indent . $this->getWhitespace(4));
            }

            return $indent . $this->htmlElement->toHtml('div', $colAttributes, PHP_EOL . $elementString . PHP_EOL . $indent);
        }

        if ($element instanceof LabelAwareInterface) {
            $floating = $element->getOption('floating');

            if ($floating) {
                $labelPosition = BaseFormRow::LABEL_APPEND;
            } elseif ($element->getLabelOption('label_position')) {
                $labelPosition = $element->getLabelOption('label_position');
            } else {
                $labelPosition = BaseFormRow::LABEL_PREPEND;
            }
        }

        $legend = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('label', $labelAttributes, $label) . PHP_EOL;

        $this->formElement->setIndent($indent . $this->getWhitespace(4));
        $elementString = $this->formElement->render($element);

        switch ($labelPosition) {
            case BaseFormRow::LABEL_PREPEND:
                $rendered = $legend . $elementString;
                break;
            case BaseFormRow::LABEL_APPEND:
            default:
                $rendered = $elementString . $legend;
                break;
        }

        if ($this->renderErrors) {
            $rendered .= $this->renderFormErrors($element, $indent . $this->getWhitespace(4));
        }

        return $indent . $this->htmlElement->toHtml('div', $colAttributes, PHP_EOL . $rendered . PHP_EOL . $indent);
    }

    /**
     * @throws Exception\DomainException
     */
    private function renderFormErrors(ElementInterface $element, string $indent): string
    {
        $this->formElementErrors->setIndent($indent);
        $elementErrors = $this->formElementErrors->render($element);

        if ($elementErrors) {
            $ariaDesc = $element->hasAttribute('aria-describedby') ? $element->getAttribute('aria-describedby') . ' ' : '';

            $ariaDesc .= $element->getAttribute('id') . 'Feedback';

            $element->setAttribute('aria-describedby', $ariaDesc);
        }

        return $elementErrors;
    }
}
