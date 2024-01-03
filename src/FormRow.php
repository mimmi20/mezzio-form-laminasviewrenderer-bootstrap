<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\MonthSelect;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Submit;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Fieldset;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormRow as BaseFormRow;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\InputFilter\InputInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Helper\EscapeHtml;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Mimmi20\LaminasView\Helper\PartialRenderer\Helper\PartialRendererInterface;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function get_debug_type;
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function sprintf;
use function str_contains;
use function str_replace;
use function trim;

use const PHP_EOL;

/** @SuppressWarnings(PHPMD.ExcessiveClassComplexity) */
final class FormRow extends BaseFormRow implements FormRowInterface
{
    use FormTrait;

    /** @throws void */
    public function __construct(
        private readonly FormElementInterface $formElement,
        private readonly FormElementErrorsInterface $formElementErrors,
        private readonly HtmlElementInterface $htmlElement,
        private readonly EscapeHtml $escapeHtml,
        private readonly PartialRendererInterface $renderer,
        private readonly Translate | null $translate = null,
    ) {
        // nothing to do
    }

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param string|null $labelPosition
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Laminas\View\Exception\RuntimeException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function render(ElementInterface $element, $labelPosition = null): string
    {
        if (!$element->hasAttribute('required')) {
            $elementName = $element->getName();

            if ($elementName !== null) {
                $form = $element->getOption('form');
                assert(
                    $form instanceof FormInterface || $form === null,
                    sprintf(
                        '$form should be an Instance of %s or null, but was %s',
                        FormInterface::class,
                        get_debug_type($form),
                    ),
                );

                if ($form !== null) {
                    $filter = $this->getInputFilter(
                        elementName: $elementName,
                        inputFilter: $form->getInputFilter(),
                        element: $element,
                    );

                    if ($filter instanceof InputInterface && $filter->isRequired()) {
                        $element->setAttribute('required', true);
                    }
                }
            }
        }

        $label = $element->getLabel() ?? '';

        if ($labelPosition === null) {
            $labelPosition = $this->getLabelPosition();
        }

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');

        // Translate the label
        if ($label !== '' && $this->translate !== null && $type !== 'hidden') {
            $label = ($this->translate)($label, $this->getTranslatorTextDomain());
        }

        // Does this element have errors ?
        if ($element->getMessages()) {
            $classAttributes  = $element->hasAttribute('class')
                ? $element->getAttribute('class') . ' '
                : '';
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

        if ($type === 'hidden') {
            $this->formElement->setIndent($indent);
            $markup = $this->formElement->render($element);

            if ($this->renderErrors) {
                $markup .= $this->renderFormErrors($element, $indent . $this->getWhitespace(4));
            }

            return $markup;
        }

        if (
            $label !== ''
            && (
                !$element instanceof LabelAwareInterface
                || !$element->getLabelOption('disable_html_escape')
            )
        ) {
            $label = ($this->escapeHtml)($label);

            assert(is_string($label));
        }

        if ($element->getAttribute('required') && $element->getOption('show-required-mark')) {
            $label .= $element->getOption('field-required-mark');
        }

        if ($element->getOption('layout') === Form::LAYOUT_HORIZONTAL) {
            return $this->renderHorizontalRow($element, $label);
        }

        return $this->renderVerticalRow($element, $label, $labelPosition);
    }

    /**
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     */
    private function renderHorizontalRow(ElementInterface $element, string $label): string
    {
        $rowAttributes      = $this->mergeAttributes($element, 'row_attributes', ['row']);
        $colAttributes      = $this->mergeAttributes($element, 'col_attributes', []);
        $labelColAttributes = $this->mergeAttributes(
            $element,
            'label_col_attributes',
            ['col-form-label'],
        );

        $indent = $this->getIndent();

        // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
        // labels. The semantic way is to group them inside a fieldset
        if (
            $element instanceof MultiCheckbox
            || $element instanceof MonthSelect
            || $element instanceof Captcha
        ) {
            $baseIndent = $indent;
            $lf1Indent  = $indent . $this->getWhitespace(4);
            $lf2Indent  = $lf1Indent . $this->getWhitespace(4);
            $lf3Indent  = $lf2Indent . $this->getWhitespace(4);

            $legend = $lf1Indent . $this->htmlElement->toHtml(
                'legend',
                $labelColAttributes,
                $label,
            ) . PHP_EOL;

            $errorContent   = '';
            $helpContent    = '';
            $messageContent = '';

            if ($this->renderErrors) {
                $errorContent = $this->renderFormErrors($element, $lf2Indent);
            }

            if ($element->getOption('messages')) {
                $messageContent = $this->renderMessages($element, $lf2Indent);
            }

            if ($element->getOption('help_content')) {
                $helpContent = $this->renderFormHelp($element, $lf1Indent);
            }

            $this->formElement->setIndent($lf3Indent);
            $elementString = $this->formElement->render($element);

            $controlClasses = ['card', 'has-validation'];

            if ($element->getAttribute('required')) {
                $controlClasses[] = 'required';
            }

            $elementString = $lf3Indent . $this->htmlElement->toHtml(
                'div',
                ['class' => 'card-body'],
                PHP_EOL . $elementString . PHP_EOL . $lf3Indent,
            );

            $elementString = $lf2Indent . $this->htmlElement->toHtml(
                'div',
                ['class' => implode(' ', $controlClasses)],
                PHP_EOL . $elementString . PHP_EOL . $lf2Indent,
            );

            $elementString .= $errorContent . $messageContent;

            $outerDiv = $lf1Indent . $this->htmlElement->toHtml(
                'div',
                $colAttributes,
                PHP_EOL . $elementString . PHP_EOL . $lf1Indent,
            );

            return $baseIndent . $this->htmlElement->toHtml(
                'fieldset',
                $rowAttributes,
                PHP_EOL . $legend . $outerDiv . $helpContent . PHP_EOL . $baseIndent,
            );
        }

        if ($element instanceof Checkbox) {
            // this is a special case, because label is always rendered inside it
            $errorContent   = '';
            $helpContent    = '';
            $messageContent = '';
            $baseIndent     = $indent;
            $lf1Indent      = $indent . $this->getWhitespace(4);
            $lf2Indent      = $lf1Indent . $this->getWhitespace(4);
            $lf3Indent      = $lf2Indent . $this->getWhitespace(4);
            $lf4Indent      = $lf3Indent . $this->getWhitespace(4);

            $legend = $lf1Indent . $this->htmlElement->toHtml('div', $labelColAttributes, '') . PHP_EOL;

            if ($this->renderErrors) {
                $errorContent = $this->renderFormErrors($element, $lf2Indent);
            }

            if ($element->getOption('messages')) {
                $messageContent = $this->renderMessages($element, $lf2Indent);
            }

            if ($element->getOption('help_content')) {
                $helpContent = $this->renderFormHelp($element, $lf1Indent);
            }

            $this->formElement->setIndent($lf4Indent);
            $elementString = $this->formElement->render($element);

            $controlClasses = ['card', 'has-validation'];

            if ($element->getAttribute('required')) {
                $controlClasses[] = 'required';
            }

            $elementString = $lf3Indent . $this->htmlElement->toHtml(
                'div',
                ['class' => 'card-body'],
                PHP_EOL . $elementString . PHP_EOL . $lf3Indent,
            );

            $elementString = $lf2Indent . $this->htmlElement->toHtml(
                'div',
                ['class' => implode(' ', $controlClasses)],
                PHP_EOL . $elementString . PHP_EOL . $lf2Indent,
            );

            $elementString .= $errorContent . $messageContent;

            $outerDiv = $lf1Indent . $this->htmlElement->toHtml(
                'div',
                $colAttributes,
                PHP_EOL . $elementString . PHP_EOL . $lf1Indent,
            );

            return $baseIndent . $this->htmlElement->toHtml(
                'div',
                $rowAttributes,
                PHP_EOL . $legend . $outerDiv . $helpContent . PHP_EOL . $baseIndent,
            );
        }

        $type = $element->getAttribute('type');

        if (
            $element instanceof Button
            || $element instanceof Submit
            || $element instanceof Fieldset
            || in_array($type, ['button', 'submit', 'reset'], true)
        ) {
            // this is a special case, because label is always rendered inside it
            $baseIndent = $indent;
            $lf1Indent  = $indent . $this->getWhitespace(4);
            $lf2Indent  = $lf1Indent . $this->getWhitespace(4);

            $this->formElement->setIndent($lf2Indent);
            $elementString = $this->formElement->render($element);

            $outerDiv = $lf1Indent . $this->htmlElement->toHtml(
                'div',
                $colAttributes,
                PHP_EOL . $elementString . PHP_EOL . $lf1Indent,
            );

            return $baseIndent . $this->htmlElement->toHtml(
                'div',
                $rowAttributes,
                PHP_EOL . $outerDiv . PHP_EOL . $baseIndent,
            );
        }

        if ($element->hasAttribute('id')) {
            $id = $element->getAttribute('id');

            assert(is_string($id));

            $labelColAttributes['for'] = $id;
        }

        $errorContent   = '';
        $helpContent    = '';
        $messageContent = '';
        $baseIndent     = $indent;
        $lf1Indent      = $indent . $this->getWhitespace(4);
        $lf2Indent      = $lf1Indent . $this->getWhitespace(4);

        $legend = $lf1Indent . $this->htmlElement->toHtml('label', $labelColAttributes, $label);

        if ($this->renderErrors) {
            $errorContent = $this->renderFormErrors($element, $lf2Indent);
        }

        if ($element->getOption('messages')) {
            $messageContent = $this->renderMessages($element, $lf2Indent);
        }

        if ($element->getOption('help_content')) {
            $helpContent = $this->renderFormHelp($element, $lf1Indent);
        }

        $this->formElement->setIndent($lf2Indent);
        $elementString  = $this->formElement->render($element);
        $elementString .= $errorContent . $messageContent;

        $outerDiv = $lf1Indent . $this->htmlElement->toHtml(
            'div',
            $colAttributes,
            PHP_EOL . $elementString . PHP_EOL . $lf1Indent,
        );

        return $baseIndent . $this->htmlElement->toHtml(
            'div',
            $rowAttributes,
            PHP_EOL . $legend . PHP_EOL . $outerDiv . $helpContent . PHP_EOL . $baseIndent,
        );
    }

    /**
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     */
    private function renderVerticalRow(
        ElementInterface $element,
        string $label,
        string | null $labelPosition = null,
    ): string {
        $colAttributes   = $this->mergeAttributes($element, 'col_attributes', []);
        $labelAttributes = $this->mergeAttributes($element, 'label_attributes', ['form-label']);

        if ($element->hasAttribute('id')) {
            $id = $element->getAttribute('id');

            assert(is_string($id));

            $labelAttributes['for'] = $id;
        }

        $indent = $this->getIndent();

        // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
        // labels. The semantic way is to group them inside a fieldset
        if (
            $element instanceof MultiCheckbox
            || $element instanceof MonthSelect
            || $element instanceof Captcha
        ) {
            $legendClasses    = [];
            $legendAttributes = $this->mergeAttributes($element, 'legend_attributes', ['form-label']);

            if (array_key_exists('class', $legendAttributes)) {
                $legendClasses = array_merge($legendClasses, explode(' ', $legendAttributes['class']));

                unset($legendAttributes['class']);
            }

            $legendAttributes['class'] = trim(implode(' ', array_unique($legendClasses)));

            $legend = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml(
                'legend',
                $legendAttributes,
                $label,
            );

            $errorContent   = '';
            $helpContent    = '';
            $messageContent = '';
            $floating       = $element->getOption('floating');

            $baseIndent = $indent;

            if ($floating) {
                $indent .= $this->getWhitespace(4);
            }

            $lf1Indent = $indent . $this->getWhitespace(4);
            $lf2Indent = $lf1Indent . $this->getWhitespace(4);

            if ($this->renderErrors) {
                $errorContent = $this->renderFormErrors($element, $lf1Indent);
            }

            if ($element->getOption('messages')) {
                $messageContent = $this->renderMessages($element, $lf1Indent);
            }

            if ($element->getOption('help_content')) {
                $helpContent = $this->renderFormHelp($element, $floating ? $indent : $lf1Indent);
            }

            $this->formElement->setIndent($lf2Indent);
            $elementString = $this->formElement->render($element);

            $controlClasses = ['card', 'has-validation'];

            if ($element->getAttribute('required')) {
                $controlClasses[] = 'required';
            }

            $elementString = $lf2Indent . $this->htmlElement->toHtml(
                'div',
                ['class' => 'card-body'],
                PHP_EOL . $elementString . PHP_EOL . $lf2Indent,
            );

            $elementString = $this->htmlElement->toHtml(
                'div',
                ['class' => implode(' ', $controlClasses)],
                PHP_EOL . $elementString . PHP_EOL . $lf1Indent,
            );

            $elementString .= $errorContent . $messageContent;

            if ($floating) {
                $elementString = PHP_EOL . $lf1Indent . $elementString . PHP_EOL . '    ' . $legend . PHP_EOL . $indent;

                $elementString  = $indent . $this->htmlElement->toHtml(
                    'div',
                    ['class' => 'form-floating'],
                    $elementString,
                );
                $elementString .= $helpContent;
            } else {
                $elementString = $legend . PHP_EOL . $lf1Indent . $elementString . $helpContent;
            }

            return $baseIndent . $this->htmlElement->toHtml(
                'fieldset',
                $colAttributes,
                PHP_EOL . $elementString . PHP_EOL . $baseIndent,
            );
        }

        if ($element instanceof Checkbox) {
            // this is a special case, because label is always rendered inside it
            $errorContent   = '';
            $helpContent    = '';
            $messageContent = '';
            $baseIndent     = $indent;
            $lf1Indent      = $indent . $this->getWhitespace(4);
            $lf2Indent      = $lf1Indent . $this->getWhitespace(4);
            $lf3Indent      = $lf2Indent . $this->getWhitespace(4);

            if ($this->renderErrors) {
                $errorContent = $this->renderFormErrors($element, $lf1Indent);
            }

            if ($element->getOption('messages')) {
                $messageContent = $this->renderMessages($element, $lf1Indent);
            }

            if ($element->getOption('help_content')) {
                $helpContent = $this->renderFormHelp($element, $lf1Indent);
            }

            $this->formElement->setIndent($lf3Indent);
            $elementString = $this->formElement->render($element);

            $controlClasses = ['card', 'has-validation'];

            if ($element->getAttribute('required')) {
                $controlClasses[] = 'required';
            }

            $elementString = $lf2Indent . $this->htmlElement->toHtml(
                'div',
                ['class' => 'card-body'],
                PHP_EOL . $elementString . PHP_EOL . $lf2Indent,
            );

            $elementString = $this->htmlElement->toHtml(
                'div',
                ['class' => implode(' ', $controlClasses)],
                PHP_EOL . $elementString . PHP_EOL . $lf1Indent,
            );

            $elementString .= $errorContent . $messageContent;

            return $baseIndent . $this->htmlElement->toHtml(
                'div',
                $colAttributes,
                PHP_EOL . $lf1Indent . $elementString . $helpContent . PHP_EOL . $baseIndent,
            );
        }

        $type = $element->getAttribute('type');

        if (
            $element instanceof Button
            || $element instanceof Submit
            || $element instanceof Fieldset
            || in_array($type, ['button', 'submit', 'reset'], true)
        ) {
            // this is a special case, because label is always rendered inside it
            $baseIndent = $indent;
            $lf1Indent  = $indent . $this->getWhitespace(4);

            $this->formElement->setIndent($lf1Indent);
            $elementString = $this->formElement->render($element);

            return $baseIndent . $this->htmlElement->toHtml(
                'div',
                $colAttributes,
                PHP_EOL . $elementString . PHP_EOL . $baseIndent,
            );
        }

        $floating   = $element->getOption('floating');
        $baseIndent = $indent;

        if ($floating) {
            $indent .= $this->getWhitespace(4);
        }

        $lf1Indent = $indent . $this->getWhitespace(4);

        $errorContent   = '';
        $helpContent    = '';
        $messageContent = '';

        if ($this->renderErrors) {
            $errorContent = $this->renderFormErrors($element, $lf1Indent);
        }

        if ($element->getOption('messages')) {
            $messageContent = $this->renderMessages($element, $lf1Indent);
        }

        if ($element->getOption('help_content')) {
            $helpContent = $this->renderFormHelp($element, $floating ? $indent : $lf1Indent);
        }

        $this->formElement->setIndent($lf1Indent);
        $elementString  = $this->formElement->render($element);
        $elementString .= $errorContent . $messageContent;

        $rendered = $elementString;

        if ($label !== '') {
            if ($element instanceof LabelAwareInterface) {
                if ($floating) {
                    $labelPosition = BaseFormRow::LABEL_APPEND;
                } elseif ($element->hasLabelOption('label_position')) {
                    $labelPosition = $element->getLabelOption('label_position');
                } else {
                    $labelPosition = BaseFormRow::LABEL_PREPEND;
                }
            }

            $legend = $lf1Indent . $this->htmlElement->toHtml('label', $labelAttributes, $label);

            $rendered = match ($labelPosition) {
                BaseFormRow::LABEL_PREPEND => $legend . PHP_EOL . $elementString,
                default => $elementString . PHP_EOL . $legend,
            };
        }

        if ($floating) {
            $rendered = $indent . $this->htmlElement->toHtml(
                'div',
                ['class' => 'form-floating'],
                PHP_EOL . $rendered . PHP_EOL . $indent,
            );
        }

        $rendered .= $helpContent;

        return $baseIndent . $this->htmlElement->toHtml(
            'div',
            $colAttributes,
            PHP_EOL . $rendered . PHP_EOL . $baseIndent,
        );
    }

    /** @throws DomainException */
    private function renderFormErrors(ElementInterface $element, string $indent): string
    {
        $this->formElementErrors->setIndent($indent);
        $elementErrors = $this->formElementErrors->render($element);

        if ($elementErrors && $element->hasAttribute('id')) {
            $ariaDesc = $element->hasAttribute('aria-describedby')
                ? $element->getAttribute('aria-describedby') . ' '
                : '';

            $ariaDesc .= $element->getAttribute('id') . 'Feedback';

            $element->setAttribute('aria-describedby', $ariaDesc);
        }

        return $elementErrors;
    }

    /** @throws void */
    private function renderFormHelp(ElementInterface $element, string $indent): string
    {
        $helpContent = $element->getOption('help_content');
        $attributes  = $this->mergeAttributes($element, 'help_attributes', []);

        assert(is_string($helpContent));

        if ($element->hasAttribute('id')) {
            $attributes['id'] = $element->getAttribute('id') . 'Help';

            $ariaDesc = $element->hasAttribute('aria-describedby')
                ? $element->getAttribute('aria-describedby') . ' '
                : '';

            $ariaDesc .= $element->getAttribute('id') . 'Help';

            $element->setAttribute('aria-describedby', $ariaDesc);
        }

        return PHP_EOL . $indent . $this->htmlElement->toHtml('div', $attributes, $helpContent);
    }

    /** @throws void */
    private function renderMessages(ElementInterface $element, string $indent): string
    {
        $messages = $element->getOption('messages');

        if (!is_array($messages)) {
            return '';
        }

        $messageContent = '';

        foreach ($messages as $message) {
            assert(is_array($message));

            $content = $message['content'] ?? '';

            if ($content === '') {
                continue;
            }

            $attributes = $message['attributes'] ?? [];

            if (array_key_exists('id', $attributes)) {
                $ariaDesc = $element->hasAttribute('aria-describedby')
                    ? $element->getAttribute('aria-describedby') . ' '
                    : '';

                $ariaDesc .= $attributes['id'];

                $element->setAttribute('aria-describedby', $ariaDesc);
            }

            $messageContent .= PHP_EOL . $indent . $this->htmlElement->toHtml(
                'div',
                $attributes,
                $content,
            );
        }

        return $messageContent;
    }

    /**
     * @param array<int, string> $classes
     *
     * @return array<string, string>
     *
     * @throws void
     */
    private function mergeAttributes(ElementInterface $element, string $optionName, array $classes = []): array
    {
        $attributes = $element->getOption($optionName) ?? [];
        assert(is_array($attributes));

        if (array_key_exists('class', $attributes)) {
            $classes = array_merge($classes, explode(' ', (string) $attributes['class']));

            unset($attributes['class']);
        }

        $form = $element->getOption('form');
        assert(
            $form instanceof FormInterface || $form === null,
            sprintf(
                '$form should be an Instance of %s or null, but was %s',
                FormInterface::class,
                get_debug_type($form),
            ),
        );

        if ($form !== null) {
            $formAttributes = $form->getOption($optionName) ?? [];

            assert(is_array($formAttributes));

            if (array_key_exists('class', $formAttributes)) {
                $classes = array_merge($classes, explode(' ', (string) $formAttributes['class']));

                unset($formAttributes['class']);
            }

            $attributes = [...$formAttributes, ...$attributes];
        }

        if ($classes) {
            $attributes['class'] = implode(' ', array_unique($classes));
        }

        return $attributes;
    }

    /**
     * @param InputFilterInterface<TFilteredValues> $inputFilter
     *
     * @return InputFilterInterface<mixed>|InputInterface|null
     *
     * @throws void
     *
     * @template TFilteredValues
     */
    private function getInputFilter(
        string $elementName,
        InputFilterInterface $inputFilter,
        ElementInterface $element,
        int $level = 0,
    ): InputInterface | InputFilterInterface | null {
        if ($inputFilter->has($elementName)) {
            $filter = $inputFilter->get($elementName);

            if ($filter instanceof InputInterface) {
                return $filter;
            }
        }

        $fieldset = $element->getOption('fieldset');
        assert(
            $fieldset instanceof FieldsetInterface || $fieldset === null,
            sprintf(
                '$fieldset should be an Instance of %s or null, but was %s',
                FieldsetInterface::class,
                get_debug_type($fieldset),
            ),
        );

        if (!$fieldset instanceof InputFilterProviderInterface || $fieldset->getName() === null) {
            return null;
        }

        $fieldsetName         = $fieldset->getName();
        $fieldsetNameOriginal = $fieldsetName;

        if (!$inputFilter->has($fieldsetNameOriginal) && str_contains($fieldsetNameOriginal, '[')) {
            $startPos = mb_strpos($fieldsetNameOriginal, '[');
            $endPos   = mb_strpos($fieldsetNameOriginal, ']', $startPos + 1);

            if ($startPos !== false && $endPos !== false) {
                $baseFieldsetName = mb_substr($fieldsetNameOriginal, 0, $startPos);
                $fieldsetName     = mb_substr(
                    $fieldsetNameOriginal,
                    $startPos + 1,
                    $endPos - $startPos - 1,
                );

                if ($inputFilter->has($baseFieldsetName)) {
                    $baseFilter = $inputFilter->get($baseFieldsetName);

                    if ($baseFilter instanceof InputFilterInterface) {
                        return $this->getInputFilter(
                            elementName: str_replace(
                                $fieldsetNameOriginal,
                                $fieldsetName,
                                $elementName,
                            ),
                            inputFilter: $baseFilter,
                            element: $element,
                            level: $level + 1,
                        );
                    }
                }
            }
        }

        if (!$inputFilter->has($fieldsetName)) {
            return null;
        }

        $filter = $inputFilter->get($fieldsetName);

        if ($filter instanceof InputInterface) {
            return $filter;
        }

        $originalElementName = mb_substr($elementName, mb_strlen($fieldsetName) + 1, -1);

        if ($filter->has($originalElementName)) {
            $subFilter = $filter->get($originalElementName);

            if ($subFilter instanceof InputInterface) {
                return $subFilter;
            }

            return $this->getInputFilter(
                elementName: $originalElementName,
                inputFilter: $subFilter,
                element: $element,
                level: $level + 1,
            );
        }

        return null;
    }
}
