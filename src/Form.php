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
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\Form\View\Helper\Form as BaseForm;

use function assert;
use function method_exists;
use function trim;

use const PHP_EOL;

/**
 * View helper for rendering Form objects
 */
final class Form extends BaseForm
{
    public const LAYOUT_HORIZONTAL = 'horizontal';
    public const LAYOUT_VERTICAL   = 'vertical';
    public const LAYOUT_INLINE     = 'inline';

    private FormCollection $formCollection;
    private FormRow $formRow;

    public function __construct(FormCollection $formCollection, FormRow $formRow)
    {
        $this->formCollection = $formCollection;
        $this->formRow        = $formRow;
    }

    /**
     * Render a form from the provided $form,
     */
    public function render(FormInterface $form): string
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        // Set form role
        if (!$form->getAttribute('role')) {
            $form->setAttribute('role', 'form');
        }

        $formLayout = $form->getOption('layout');
        $class      = $form->getAttribute('class') ?? '';

        if (self::LAYOUT_VERTICAL === $formLayout) {
            $class .= ' row';
        } elseif (self::LAYOUT_INLINE === $formLayout) {
            $class .= ' row row-cols-lg-auto align-items-center';
        }

        if ((self::LAYOUT_VERTICAL === $formLayout || self::LAYOUT_INLINE === $formLayout) && $form->getOption('floating-labels')) {
            $class .= ' form-floating';
        }

        $form->setAttribute('class', trim($class));

        $formContent = '';

        foreach ($form as $element) {
            assert($element instanceof FieldsetInterface || $element instanceof ElementInterface);

            $element->setOption('form', $form);

            if (null !== $formLayout && !$element->getOption('layout')) {
                $element->setOption('layout', $formLayout);
            }

            if ((self::LAYOUT_VERTICAL === $formLayout || self::LAYOUT_INLINE === $formLayout) && $form->getOption('floating-labels')) {
                $element->setOption('floating', true);
            }

            if ($element instanceof FieldsetInterface) {
                $formContent .= ($this->formCollection)($element) . PHP_EOL;
            } else {
                $formContent .= ($this->formRow)($element) . PHP_EOL;
            }
        }

        return $this->openTag($form) . PHP_EOL . $formContent . $this->closeTag() . PHP_EOL;
    }
}
