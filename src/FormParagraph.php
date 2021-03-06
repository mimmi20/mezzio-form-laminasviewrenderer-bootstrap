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
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mimmi20\Form\Element\Paragraph\ParagraphInterface as ParagraphElement;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function implode;
use function is_string;
use function sprintf;
use function trim;

final class FormParagraph extends AbstractHelper implements FormIndentInterface, FormRenderInterface
{
    use FormTrait;

    private ?Translate $translate;
    private EscapeHtml $escapeHtml;

    public function __construct(
        EscapeHtml $escaper,
        ?Translate $translator = null
    ) {
        $this->escapeHtml = $escaper;
        $this->translate  = $translator;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return FormParagraph|string
     *
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(?ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form <select> element from the provided $element
     *
     * @throws Exception\InvalidArgumentException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof ParagraphElement) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type %s',
                    __METHOD__,
                    ParagraphElement::class
                )
            );
        }

        $text = $element->getText();

        $classes    = [];
        $attributes = $element->getAttributes();

        if (array_key_exists('class', $attributes)) {
            $classes = array_merge($classes, explode(' ', $attributes['class']));
            unset($attributes['class']);
        }

        $attributes['class'] = trim(implode(' ', array_unique($classes)));

        if ('' !== $text) {
            // Translate the label
            if (null !== $this->translate) {
                $text = ($this->translate)($text, $this->getTranslatorTextDomain());
            }

            $text = ($this->escapeHtml)($text);

            assert(is_string($text));
        }

        $renderedElement = sprintf(
            '<p %s>%s</p>',
            $this->createAttributesString($attributes),
            $text
        );

        $indent = $this->getIndent();

        return $indent . $renderedElement;
    }
}
