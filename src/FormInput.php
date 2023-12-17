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

namespace Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\View\Helper\FormInput as BaseFormInput;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function explode;
use function implode;
use function sprintf;
use function trim;

abstract class FormInput extends BaseFormInput implements FormInputInterface
{
    use FormTrait;

    protected EscapeHtml $escapeHtml;
    protected EscapeHtmlAttr $escapeHtmlAttr;
    protected Doctype $doctype;

    public function __construct(EscapeHtml $escapeHtml, EscapeHtmlAttr $escapeHtmlAttr, Doctype $doctype)
    {
        $this->escapeHtml     = $escapeHtml;
        $this->escapeHtmlAttr = $escapeHtmlAttr;
        $this->doctype        = $doctype;
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws \Laminas\View\Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        $name = $element->getName();

        if (null === $name || '' === $name) {
            throw new \Laminas\View\Exception\DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__
                )
            );
        }

        $attributes = $element->getAttributes();

        $attributes['name'] = $name;
        $type               = $this->getType($element);
        $attributes['type'] = $type;

        if ('password' === $type) {
            $attributes['value'] = '';
        } else {
            $attributes['value'] = $element->getValue();
        }

        if (isset($attributes['readonly']) && $element->getOption('plain')) {
            $classes = ['form-control-plaintext'];
        } else {
            $classes = ['form-control'];

            if (array_key_exists('class', $attributes)) {
                $classes = array_merge($classes, explode(' ', $attributes['class']));
            }
        }

        $attributes['class'] = trim(implode(' ', array_unique($classes)));

        $markup = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );

        $indent = $this->getIndent();

        return $indent . $markup;
    }

    /**
     * Get the closing bracket for an inline tag
     *
     * Closes as either "/>" for XHTML doctypes or ">" otherwise.
     */
    public function getInlineClosingBracket(): string
    {
        if ($this->doctype->isXhtml()) {
            return '/>';
        }

        return '>';
    }
}
