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
use Mezzio\LaminasViewHelper\Helper\PartialRendererInterface;
use Traversable;

use function array_merge;
use function array_walk_recursive;
use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function iterator_to_array;
use function sprintf;

final class FormElementErrors extends AbstractHelper
{
    /**
     * Templates for the open/close/separators for message tags
     */
    private string $messageCloseString     = '</li></ul>';
    private string $messageOpenFormat      = '<ul%s><li>';
    private string $messageSeparatorString = '</li><li>';

    /** @var array<int|string, string> Default attributes for the open format tag */
    private array $attributes = [];

    /** @var bool whether or not to translate error messages during render */
    private bool $translateErrorMessages = true;

    private ?Translate $translate;
    private PartialRendererInterface $renderer;

    public function __construct(PartialRendererInterface $renderer, ?Translate $translate = null)
    {
        $this->renderer  = $renderer;
        $this->translate = $translate;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()} if an element is passed.
     *
     * @param array<int|string, string> $attributes
     *
     * @return FormElementErrors|string
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
     * If {@link $translateErrorMessages} is true, and a translator is
     * composed, messages retrieved from the element will be translated; if
     * either is not the case, they will not.
     *
     * @param array<int|string, string> $attributes
     *
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element, array $attributes = []): string
    {
        $messages = $element->getMessages();
        if ($messages instanceof Traversable) {
            $messages = iterator_to_array($messages);
        } elseif (!is_array($messages)) {
            throw new Exception\DomainException(sprintf(
                '%s expects that $element->getMessages() will return an array or Traversable; received "%s"',
                __METHOD__,
                is_object($messages) ? get_class($messages) : gettype($messages)
            ));
        }

        if (!$messages) {
            return '';
        }

        // Flatten message array
        $messages = $this->flattenMessages($messages);
        if (!$messages) {
            return '';
        }

        // Prepare attributes for opening tag
        $attributes = array_merge($this->attributes, $attributes);

        $errorAttributes = [
            'id' => $element->getAttribute('id') . 'Feedback',
            'class' => 'invalid-feedback',
        ];

        return $this->renderer->render(
            'elements::errors',
            [
                'errorAttributes' => $errorAttributes,
                'attributes' => $attributes,
                'messages' => $messages,
            ]
        );
    }

    /**
     * Set the attributes that will go on the message open format
     *
     * @param array<int|string, string> $attributes key value pairs of attributes
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get the attributes that will go on the message open format
     *
     * @return array<int|string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the string used to close message representation
     */
    public function setMessageCloseString(string $messageCloseString): self
    {
        $this->messageCloseString = $messageCloseString;

        return $this;
    }

    /**
     * Get the string used to close message representation
     */
    public function getMessageCloseString(): string
    {
        return $this->messageCloseString;
    }

    /**
     * Set the formatted string used to open message representation
     */
    public function setMessageOpenFormat(string $messageOpenFormat): self
    {
        $this->messageOpenFormat = $messageOpenFormat;

        return $this;
    }

    /**
     * Get the formatted string used to open message representation
     */
    public function getMessageOpenFormat(): string
    {
        return $this->messageOpenFormat;
    }

    /**
     * Set the string used to separate messages
     */
    public function setMessageSeparatorString(string $messageSeparatorString): self
    {
        $this->messageSeparatorString = $messageSeparatorString;

        return $this;
    }

    /**
     * Get the string used to separate messages
     */
    public function getMessageSeparatorString(): string
    {
        return $this->messageSeparatorString;
    }

    /**
     * Set the flag detailing whether or not to translate error messages.
     */
    public function setTranslateMessages(bool $flag): self
    {
        $this->translateErrorMessages = $flag;

        return $this;
    }

    /**
     * @param array<int|string, string> $messages
     *
     * @return array<int, string>
     */
    private function flattenMessages(array $messages): array
    {
        return $this->translateErrorMessages && null !== $this->translate
            ? $this->flattenMessagesWithTranslator($messages)
            : $this->flattenMessagesWithoutTranslator($messages);
    }

    /**
     * @param array<int|string, string> $messages
     *
     * @return array<int, string>
     */
    private function flattenMessagesWithoutTranslator(array $messages): array
    {
        $messagesToPrint = [];
        array_walk_recursive($messages, static function ($item) use (&$messagesToPrint): void {
            $messagesToPrint[] = $item;
        });

        return $messagesToPrint;
    }

    /**
     * @param array<int|string, string> $messages
     *
     * @return array<int, string>
     */
    private function flattenMessagesWithTranslator(array $messages): array
    {
        $translator      = $this->translate;
        $textDomain      = $this->getTranslatorTextDomain();
        $messagesToPrint = [];
        $messageCallback = static function ($item) use (&$messagesToPrint, $translator, $textDomain): void {
            $messagesToPrint[] = ($translator)($item, $textDomain);
        };
        array_walk_recursive($messages, $messageCallback);

        return $messagesToPrint;
    }
}
