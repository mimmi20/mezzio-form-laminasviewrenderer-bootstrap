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

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\Form\View\Helper\Form as BaseForm;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;

use function assert;
use function is_string;
use function method_exists;
use function trim;

use const PHP_EOL;

/**
 * View helper for rendering Form objects
 */
final class Form extends BaseForm
{
    use FormTrait;

    public const LAYOUT_HORIZONTAL = 'horizontal';

    public const LAYOUT_VERTICAL = 'vertical';

    public const LAYOUT_INLINE = 'inline';

    /** @throws void */
    public function __construct(
        private readonly FormCollectionInterface $formCollection,
        private readonly FormRowInterface $formRow,
    ) {
        // nothing to do
    }

    /**
     * Render a form from the provided $form
     *
     * @param FormInterface<TFilteredValues> $form
     *
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     * @throws DomainException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws \Laminas\I18n\Exception\RuntimeException
     *
     * @template TFilteredValues of object
     */
    public function render(FormInterface $form): string
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        // Set form role
        if (!$form->hasAttribute('role')) {
            $form->setAttribute('role', 'form');
        }

        $formLayout   = $form->getOption('layout');
        $class        = $form->getAttribute('class') ?? '';
        $requiredMark = $form->getOption('form-required-mark');

        assert(is_string($class));

        if ($formLayout === null && $form->getOption('floating-labels')) {
            $formLayout = self::LAYOUT_VERTICAL;
        }

        if ($formLayout === self::LAYOUT_VERTICAL) {
            if ($form->getOption('as-card')) {
                $class .= ' card';
            } else {
                $class .= ' row';
            }
        } elseif ($formLayout === self::LAYOUT_INLINE) {
            $class .= ' row row-cols-lg-auto align-items-center';
        }

        $form->setAttribute('class', trim($class));

        $formContent = '';
        $indent      = $this->getIndent();

        foreach ($form->getIterator() as $element) {
            assert($element instanceof ElementInterface);

            $element->setOption('form', $form);

            if ($formLayout !== null && !$element->getOption('layout')) {
                $element->setOption('layout', $formLayout);
            }

            if ($requiredMark && $form->getOption('field-required-mark')) {
                $element->setOption('show-required-mark', true);
                $element->setOption('field-required-mark', $form->getOption('field-required-mark'));
            }

            if (
                (
                    $formLayout === self::LAYOUT_VERTICAL
                    || $formLayout === self::LAYOUT_INLINE
                )
                && $form->getOption('floating-labels')
            ) {
                $element->setOption('floating', true);
            }

            if ($element instanceof FieldsetInterface) {
                $this->formCollection->setIndent($indent . $this->getWhitespace(4));
                $this->formCollection->setShouldWrap(true);

                $formContent .= $this->formCollection->render($element) . PHP_EOL;
            } else {
                $this->formRow->setIndent($indent . $this->getWhitespace(4));
                $formContent .= $this->formRow->render($element) . PHP_EOL;
            }
        }

        if ($requiredMark) {
            $formContent .= $indent . $this->getWhitespace(4) . $requiredMark . PHP_EOL;
        }

        return $this->openTag($form) . PHP_EOL . $formContent . $indent . $this->closeTag() . PHP_EOL;
    }
}
