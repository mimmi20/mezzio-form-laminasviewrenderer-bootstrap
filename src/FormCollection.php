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

use Laminas\Form\Element\Collection as CollectionElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormCollection as BaseFormCollection;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasViewHelper\Helper\HtmlElement;
use Traversable;

use function array_key_exists;
use function array_merge;
use function assert;
use function explode;
use function implode;
use function is_array;
use function iterator_to_array;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormCollection extends BaseFormCollection
{
    use FormTrait;

    private FormRow $formRow;
    private EscapeHtml $escapeHtml;
    private HtmlElement $htmlElement;
    private ?Translate $translate;

    public function __construct(FormRow $formRow, EscapeHtml $escapeHtml, HtmlElement $htmlElement, ?Translate $translator = null)
    {
        $this->formRow     = $formRow;
        $this->escapeHtml  = $escapeHtml;
        $this->htmlElement = $htmlElement;
        $this->translate   = $translator;
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws Exception\DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws Exception\InvalidArgumentException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof FieldsetInterface) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type Laminas\Form\FieldsetInterface',
                    __METHOD__
                )
            );
        }

        $markup         = '';
        $templateMarkup = '';
        $indent         = $this->getIndent();

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = PHP_EOL . $indent . $this->getWhitespace(4) . $this->renderTemplate($element);
        }

        $layout   = $element->getOption('layout');
        $floating = $element->getOption('floating');

        foreach ($element->getIterator() as $elementOrFieldset) {
            assert($elementOrFieldset instanceof FieldsetInterface || $elementOrFieldset instanceof ElementInterface);

            $elementOrFieldset->setOption('form', $element->getOption('form'));

            if (null !== $layout && !$elementOrFieldset->getOption('layout')) {
                $elementOrFieldset->setOption('layout', $layout);
            }

            if ($floating) {
                $elementOrFieldset->setOption('floating', true);
            }

            if ($element->getOption('show-required-mark') && $element->getOption('field-required-mark')) {
                $elementOrFieldset->setOption('show-required-mark', true);
                $elementOrFieldset->setOption('field-required-mark', $element->getOption('field-required-mark'));
            }

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $this->setIndent($indent . $this->getWhitespace(4));

                $markup .= $this->render($elementOrFieldset) . PHP_EOL;

                $this->setIndent($indent);
            } else {
                $this->formRow->setIndent($indent . $this->getWhitespace(4));
                $markup .= $this->formRow->render($elementOrFieldset) . PHP_EOL;
            }
        }

        if (!$this->shouldWrap) {
            return $markup . $templateMarkup;
        }

        // Every collection is wrapped by a fieldset if needed
        $attributes = $element->getAttributes();

        if ($attributes instanceof Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        unset($attributes['name']);

        $indent = $this->getIndent();
        $label  = $element->getLabel();
        $legend = '';

        if (!empty($label)) {
            if (null !== $this->translate) {
                $label = ($this->translate)(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = ($this->escapeHtml)($label);
            }

            if (
                '' !== $label && !$element->hasAttribute('id')
                || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
            ) {
                $label = '<span>' . $label . '</span>';
            }

            $labelClasses    = [];
            $labelAttributes = $element->getOption('label_attributes') ?? [];

            assert(is_array($labelAttributes));

            if (array_key_exists('class', $labelAttributes)) {
                $labelClasses = array_merge($labelClasses, explode(' ', $labelAttributes['class']));
            }

            $labelAttributes['class'] = trim(implode(' ', $labelClasses));

            $legend = PHP_EOL . $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('legend', $labelAttributes, $label);
        }

        $markup = PHP_EOL . $markup;

        return $indent . $this->htmlElement->toHtml('fieldset', $attributes, $legend . $markup . $templateMarkup . $indent);
    }
}
