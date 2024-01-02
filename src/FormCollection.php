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

use Laminas\Form\Element\Collection as CollectionElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function get_debug_type;
use function implode;
use function is_array;
use function is_string;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormCollection extends AbstractHelper implements FormCollectionInterface
{
    use FormTrait;

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     */
    private bool $shouldWrap = true;

    /** @throws void */
    public function __construct(
        private readonly FormRowInterface $formRow,
        private readonly EscapeHtml $escapeHtml,
        private readonly HtmlElementInterface $htmlElement,
        private readonly Translate | null $translate = null,
    ) {
        // nothing to do
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function __invoke(ElementInterface | null $element = null, bool $wrap = true)
    {
        if (!$element) {
            return $this;
        }

        $this->setShouldWrap($wrap);

        return $this->render($element);
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof FieldsetInterface) {
            throw new \Laminas\Form\Exception\InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type %s',
                    __METHOD__,
                    FieldsetInterface::class,
                ),
            );
        }

        $markup         = '';
        $templateMarkup = '';
        $indent         = $this->getIndent();
        $baseIndent     = $indent;
        $asCard         = $element->getOption('as-card');

        if ($this->shouldWrap && $asCard) {
            $indent .= $this->getWhitespace(8);
        }

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = $this->renderTemplate($element, $indent);
        }

        $form     = $element->getOption('form');
        $layout   = $element->getOption('layout');
        $floating = $element->getOption('floating');

        foreach ($element->getIterator() as $elementOrFieldset) {
            assert($elementOrFieldset instanceof ElementInterface);

            if ($form !== null && !$elementOrFieldset->getOption('form')) {
                $elementOrFieldset->setOption('form', $form);
            }

            if ($layout !== null && !$elementOrFieldset->getOption('layout')) {
                $elementOrFieldset->setOption('layout', $layout);
            }

            if ($floating) {
                $elementOrFieldset->setOption('floating', true);
            }

            if (
                $element->getOption('show-required-mark')
                && $element->getOption('field-required-mark')
            ) {
                $elementOrFieldset->setOption('show-required-mark', true);
                $elementOrFieldset->setOption(
                    'field-required-mark',
                    $element->getOption('field-required-mark'),
                );
            }

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $this->setIndent($indent . $this->getWhitespace(4));

                $markup .= $this->render($elementOrFieldset) . PHP_EOL;

                $this->setIndent($indent);
            } else {
                $elementOrFieldset->setOption('fieldset', $element);

                $this->formRow->setIndent($indent . $this->getWhitespace(4));
                $markup .= $this->formRow->render($elementOrFieldset) . PHP_EOL;
            }
        }

        if (!$this->shouldWrap) {
            return $markup . $templateMarkup;
        }

        // Every collection is wrapped by a fieldset if needed
        $attributes = $element->getAttributes();

        unset($attributes['name']);

        $label  = $element->getLabel();
        $legend = '';

        if (!empty($label)) {
            if ($this->translate !== null) {
                $label = ($this->translate)(
                    $label,
                    $this->getTranslatorTextDomain(),
                );
            }

            if (
                !$element instanceof LabelAwareInterface
                || !$element->getLabelOption('disable_html_escape')
            ) {
                $label = ($this->escapeHtml)($label);

                assert(is_string($label));
            }

            if (
                $label !== '' && !$element->hasAttribute('id')
                || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
            ) {
                $label = '<span>' . $label . '</span>';
            }

            $labelClasses    = [];
            $labelAttributes = $element->getOption('label_attributes') ?? [];

            assert(is_array($labelAttributes));

            if (array_key_exists('class', $labelAttributes)) {
                $labelClasses = explode(' ', (string) $labelAttributes['class']);
            }

            $labelAttributes['class'] = trim(implode(' ', array_unique($labelClasses)));

            if ($asCard) {
                $labelAttributes = $this->mergeFormAttributes(
                    $element,
                    'col_attributes',
                    ['card-title'],
                    $labelAttributes,
                );
            }

            $legend = PHP_EOL . $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml(
                'legend',
                $labelAttributes,
                $label,
            );
        }

        if ($asCard) {
            $classes = ['card-body'];

            if (array_key_exists('class', $attributes) && is_string($attributes['class'])) {
                $classes = array_merge(
                    $classes,
                    explode(' ', $attributes['class']),
                );
            }

            $attributes['class'] = trim(implode(' ', array_unique($classes)));
        }

        $markup = $baseIndent . $this->htmlElement->toHtml(
            'fieldset',
            $attributes,
            $legend . PHP_EOL . $markup . $templateMarkup . $indent,
        );

        if ($asCard) {
            $markup = PHP_EOL . $baseIndent . $this->getWhitespace(4) . $this->htmlElement->toHtml(
                'div',
                $this->mergeAttributes($element, 'card_attributes', ['card']),
                PHP_EOL . $baseIndent . $this->getWhitespace(
                    4,
                ) . $markup . PHP_EOL . $baseIndent . $this->getWhitespace(
                    4,
                ),
            );

            $markup = $baseIndent . $this->htmlElement->toHtml(
                'div',
                $this->mergeAttributes($element, 'col_attributes', []),
                $markup . PHP_EOL . $baseIndent,
            );
        }

        return $markup;
    }

    /**
     * Only render a template
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     */
    public function renderTemplate(CollectionElement $collection, string $indent): string
    {
        $elementOrFieldset = $collection->getTemplateElement();

        if (!$elementOrFieldset instanceof ElementInterface) {
            return '';
        }

        if ($elementOrFieldset instanceof FieldsetInterface) {
            $this->setIndent($indent . $this->getWhitespace(4));

            $templateMarkup = $this->render($elementOrFieldset) . PHP_EOL;

            $this->setIndent($indent);
        } else {
            $this->formRow->setIndent($indent . $this->getWhitespace(4));
            $templateMarkup = $this->formRow->render($elementOrFieldset) . PHP_EOL;
        }

        $templateAttrbutes = $collection->getOption('template_attributes') ?? [];

        assert(is_array($templateAttrbutes));

        return $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml(
            'template',
            $templateAttrbutes,
            $templateMarkup . $indent,
        ) . PHP_EOL;
    }

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @throws void
     */
    public function setShouldWrap(bool $wrap): self
    {
        $this->shouldWrap = $wrap;

        return $this;
    }

    /**
     * Get wrapped
     *
     * @throws void
     */
    public function shouldWrap(): bool
    {
        return $this->shouldWrap;
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

        if ($classes) {
            $attributes['class'] = implode(' ', array_unique($classes));
        }

        return $attributes;
    }

    /**
     * @param array<int, string>    $classes
     * @param array<string, string> $attributes
     *
     * @return array<string, string>
     *
     * @throws void
     */
    private function mergeFormAttributes(
        ElementInterface $element,
        string $optionName,
        array $classes = [],
        array $attributes = [],
    ): array {
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
                $classes = array_merge(explode(' ', (string) $formAttributes['class']), $classes);

                unset($formAttributes['class']);
            }

            $attributes = [...$formAttributes, ...$attributes];
        }

        if ($classes) {
            $attributes['class'] = implode(' ', array_unique($classes));
        }

        return $attributes;
    }
}
