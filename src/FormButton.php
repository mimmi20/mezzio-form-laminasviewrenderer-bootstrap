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

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\LabelAwareInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Traversable;

use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function iterator_to_array;
use function mb_strtolower;
use function sprintf;

final class FormButton extends FormInput
{
    /**
     * Attributes valid for the button tag
     *
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $validTagAttributes = [
        'name' => true,
        'autofocus' => true,
        'disabled' => true,
        'form' => true,
        'formaction' => true,
        'formenctype' => true,
        'formmethod' => true,
        'formnovalidate' => true,
        'formtarget' => true,
        'type' => true,
        'value' => true,
    ];

    /**
     * Valid values for the button type
     *
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $validTypes = [
        'button' => true,
        'reset' => true,
        'submit' => true,
    ];

    private ?Translate $translate;

    public function __construct(
        EscapeHtml $escapeHtml,
        EscapeHtmlAttr $escapeHtmlAttr,
        Doctype $doctype,
        ?Translate $translator = null
    ) {
        parent::__construct($escapeHtml, $escapeHtmlAttr, $doctype);

        $this->translate = $translator;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return FormButton|string
     *
     * @throws Exception\DomainException
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(?ElementInterface $element = null, ?string $buttonContent = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element, $buttonContent);
    }

    /**
     * Render a form <button> element from the provided $element,
     * using content from $buttonContent or the element's "label" attribute
     *
     * @throws Exception\DomainException
     * @throws Exception\InvalidArgumentException
     */
    public function render(ElementInterface $element, ?string $buttonContent = null): string
    {
        $openTag = $this->openTag($element);

        if (null === $buttonContent) {
            $buttonContent = $element->getLabel();
            if (null === $buttonContent) {
                throw new Exception\DomainException(
                    sprintf(
                        '%s expects either button content as the second argument, '
                        . 'or that the element provided has a label value; neither found',
                        __METHOD__
                    )
                );
            }
        }

        if (null !== $this->translate) {
            $buttonContent = ($this->translate)(
                $buttonContent,
                $this->getTranslatorTextDomain()
            );
        }

        if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
            $buttonContent = ($this->escapeHtml)($buttonContent);
        }

        $indent = $this->getIndent();

        return $indent . $openTag . $buttonContent . $this->closeTag();
    }

    /**
     * Generate an opening button tag
     *
     * @param array<string, bool|string>|ElementInterface|null $attributesOrElement
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function openTag($attributesOrElement = null): string
    {
        if (null === $attributesOrElement) {
            return '<button>';
        }

        if (is_array($attributesOrElement)) {
            $attributes = $this->createAttributesString($attributesOrElement);

            return sprintf('<button %s>', $attributes);
        }

        if (!$attributesOrElement instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Laminas\Form\ElementInterface instance; received "%s"',
                __METHOD__,
                is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement)
            ));
        }

        $element = $attributesOrElement;
        $name    = $element->getName();
        if (empty($name) && 0 !== $name) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes = $element->getAttributes();

        if ($attributes instanceof Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        $attributes['name'] = $name;
        $attributes['type'] = $this->getType($element);

        $value = $element->getValue();

        if ($value) {
            $attributes['value'] = $value;
        }

        return sprintf(
            '<button %s>',
            $this->createAttributesString($attributes)
        );
    }

    /**
     * Return a closing button tag
     */
    public function closeTag(): string
    {
        return '</button>';
    }

    /**
     * Determine button type to use
     */
    protected function getType(ElementInterface $element): string
    {
        $type = $element->getAttribute('type');
        if (empty($type)) {
            return 'submit';
        }

        $type = mb_strtolower($type);
        if (!isset($this->validTypes[$type])) {
            return 'submit';
        }

        return $type;
    }
}
