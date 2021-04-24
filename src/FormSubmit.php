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
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormSubmit as BaseFormSubmit;

final class FormSubmit extends BaseFormSubmit
{
    use FormTrait;

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws DomainException
     */
    public function render(ElementInterface $element): string
    {
        $markup = parent::render($element);
        $indent = $this->getIndent();

        return $indent . $markup;
    }
}
