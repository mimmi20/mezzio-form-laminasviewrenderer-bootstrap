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
use Laminas\Form\FieldsetInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormCollection as BaseFormCollection;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;

use function assert;
use function sprintf;

final class FormCollection extends BaseFormCollection
{
    private FormRow $formRow;
    private EscapeHtml $escapeHtml;
    private ?Translate $translate;

    public function __construct(FormRow $formRow, EscapeHtml $escapeHtml, ?Translate $translator = null)
    {
        $this->formRow    = $formRow;
        $this->escapeHtml = $escapeHtml;
        $this->translate  = $translator;
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     */
    public function render(ElementInterface $element): string
    {
        $markup         = '';
        $templateMarkup = '';

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = $this->renderTemplate($element);
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

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= ($this)($elementOrFieldset, $this->shouldWrap());
            } else {
                $markup .= ($this->formRow)($elementOrFieldset);
            }
        }

        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            $attributes = $element->getAttributes();
            unset($attributes['name']);
            $attributesString = $attributes ? ' ' . $this->createAttributesString($attributes) : '';

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

                $legend = sprintf(
                    $this->labelWrapper,
                    $label
                );
            }

            $markup = sprintf(
                $this->wrapper,
                $markup,
                $legend,
                $templateMarkup,
                $attributesString
            );
        } else {
            $markup .= $templateMarkup;
        }

        return $markup;
    }
}
