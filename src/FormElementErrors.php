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
use Laminas\Form\Exception\DomainException;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;

use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use function array_merge;
use function array_walk_recursive;
use function implode;

use const PHP_EOL;

final class FormElementErrors extends AbstractHelper implements FormElementErrorsInterface
{
    use FormTrait;

    /** @var array<string, string> Default attributes for the open format tag */
    private array $attributes = [];

    private ?Translate $translate;
    private EscapeHtml $escapeHtml;
    private HtmlElementInterface $htmlElement;

    public function __construct(
        HtmlElementInterface $htmlElement,
        EscapeHtml $escapeHtml,
        ?Translate $translate = null
    ) {
        $this->htmlElement = $htmlElement;
        $this->escapeHtml  = $escapeHtml;
        $this->translate   = $translate;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()} if an element is passed.
     *
     * @param array<string, string> $attributes
     *
     * @return FormElementErrors|string
     *
     * @throws DomainException
     */
    public function __invoke(?ElementInterface $element = null, array $attributes = [])
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element, $attributes);
    }

    /**
     * Render validation errors for the provided $element
     *
     * If a translator is
     * composed, messages retrieved from the element will be translated; if
     * either is not the case, they will not.
     *
     * @param array<string, string> $attributes
     *
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element, array $attributes = []): string
    {
        $messages = $element->getMessages();

        if ([] === $messages) {
            return '';
        }

        // Flatten message array
        $messages = $this->flattenMessages($messages);

        if ([] === $messages) {
            return '';
        }

        $indent  = $this->getIndent();
        $markups = [];

        foreach ($messages as $message) {
            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $message = ($this->escapeHtml)($message);
            }

            $markups[] = $indent . $this->getWhitespace(8) . $this->htmlElement->toHtml('li', [], $message);
        }

        // Prepare attributes for opening tag
        $attributes      = array_merge($this->attributes, $attributes);
        $errorAttributes = ['class' => 'invalid-feedback'];

        if ($element->hasAttribute('id')) {
            $errorAttributes['id'] = $element->getAttribute('id') . 'Feedback';
        }

        $ul = $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('ul', $attributes, implode(PHP_EOL, $markups) . PHP_EOL . $indent . $this->getWhitespace(4));

        return $indent . $this->htmlElement->toHtml('div', $errorAttributes, $ul);
    }

    /**
     * Set the attributes that will go on the message open format
     *
     * @param array<string, string> $attributes key value pairs of attributes
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get the attributes that will go on the message open format
     *
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<int|string, string> $messages
     *
     * @return array<int, string>
     */
    private function flattenMessages(array $messages): array
    {
        $messagesToPrint = [];

        if (null === $this->translate) {
            $messageCallback = static function ($message) use (&$messagesToPrint): void {
                if ('' === $message) {
                    return;
                }

                $messagesToPrint[] = $message;
            };
        } else {
            $translator      = $this->translate;
            $textDomain      = $this->getTranslatorTextDomain();
            $messageCallback = static function ($message) use (&$messagesToPrint, $translator, $textDomain): void {
                if ('' === $message) {
                    return;
                }

                $messagesToPrint[] = ($translator)($message, $textDomain);
            };
        }

        array_walk_recursive($messages, $messageCallback);

        return $messagesToPrint;
    }
}
