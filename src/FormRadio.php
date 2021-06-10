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

use function sprintf;

final class FormRadio extends AbstractFormMultiCheckbox
{
    /**
     * Return input type
     */
    protected function getInputType(): string
    {
        return 'radio';
    }

    /**
     * Get element name
     */
    protected static function getName(ElementInterface $element): string
    {
        $name = $element->getName();

        if (null === $name || '' === $name) {
            throw new Exception\DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__
                )
            );
        }

        return $name;
    }
}
