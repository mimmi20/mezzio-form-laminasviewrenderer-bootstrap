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
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use Traversable;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function implode;
use function is_array;
use function iterator_to_array;
use function sprintf;
use function trim;

use const PHP_EOL;

final class FormCollection extends AbstractHelper implements FormCollectionInterface
{
    use FormTrait;

    private FormRowInterface $formRow;
    private EscapeHtml $escapeHtml;
    private EscapeHtmlAttr $escapeHtmlAttr;
    private HtmlElementInterface $htmlElement;
    private ?Translate $translate;

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     */
    private bool $shouldWrap = true;

    /**
     * This is the default wrapper that the collection is wrapped into
     */
    private string $wrapper = '<fieldset%4$s>%2$s%1$s%3$s</fieldset>';

    /**
     * This is the default label-wrapper
     */
    private string $labelWrapper = '<legend>%s</legend>';

    /**
     * Where shall the template-data be inserted into
     */
    private string $templateWrapper = '<template>%s</template>';

    /**
     * The name of the default view helper that is used to render sub elements.
     */
    private string $defaultElementHelper = 'formrow';

    public function __construct(FormRowInterface $formRow, EscapeHtml $escapeHtml, HtmlElementInterface $htmlElement, ?Translate $translator = null)
    {
        $this->formRow     = $formRow;
        $this->escapeHtml  = $escapeHtml;
        $this->htmlElement = $htmlElement;
        $this->translate   = $translator;
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
     * @throws Exception\DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(ElementInterface $element = null, bool $wrap = true)
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

            $labelAttributes['class'] = trim(implode(' ', array_unique($labelClasses)));

            $legend = PHP_EOL . $indent . $this->getWhitespace(4) . $this->htmlElement->toHtml('legend', $labelAttributes, $label);
        }

        $markup = PHP_EOL . $markup;

        return $indent . $this->htmlElement->toHtml('fieldset', $attributes, $legend . $markup . $templateMarkup . $indent);
    }

    /**
     * Only render a template
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws Exception\DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws Exception\InvalidArgumentException
     */
    public function renderTemplate(CollectionElement $collection): string
    {
        $elementOrFieldset = $collection->getTemplateElement();

        if (null === $elementOrFieldset) {
            return '';
        }

        $templateMarkup = '';
        $indent         = $this->getIndent();

        if ($elementOrFieldset instanceof FieldsetInterface) {
            $this->setIndent($indent . $this->getWhitespace(4));

            $templateMarkup .= $this->render($elementOrFieldset) . PHP_EOL;

            $this->setIndent($indent);
        } else {
            $this->formRow->setIndent($indent . $this->getWhitespace(4));
            $templateMarkup .= $this->formRow->render($elementOrFieldset) . PHP_EOL;
        }

        return $indent . $this->htmlElement->toHtml('template', [], $templateMarkup . $indent);
    }

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     */
    public function setShouldWrap(bool $wrap): self
    {
        $this->shouldWrap = $wrap;

        return $this;
    }

    /**
     * Get wrapped
     */
    public function shouldWrap(): bool
    {
        return $this->shouldWrap;
    }
}
