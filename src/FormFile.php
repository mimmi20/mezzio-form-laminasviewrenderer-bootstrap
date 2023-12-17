<?php
/**
 * This file is part of the mimmi20/mezzio-form-laminasviewrenderer-bootstrap package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;

use function array_key_exists;
use function is_array;
use function is_string;
use function sprintf;

final class FormFile extends FormInput
{
    /**
     * Attributes valid for the input tag type="file"
     *
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $validTagAttributes = [
        'name' => true,
        'accept' => true,
        'autofocus' => true,
        'disabled' => true,
        'form' => true,
        'multiple' => true,
        'required' => true,
        'type' => true,
    ];

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws DomainException
     */
    public function render(ElementInterface $element): string
    {
        $name = $element->getName();

        if ($name === null || $name === '') {
            throw new DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__,
                ),
            );
        }

        $attributes = $element->getAttributes();

        $attributes['type'] = $this->getType($element);
        $attributes['name'] = $name;

        if (array_key_exists('multiple', $attributes) && $attributes['multiple']) {
            $attributes['name'] .= '[]';
        }

        $value = $element->getValue();

        if (is_array($value) && isset($value['name']) && !is_array($value['name'])) {
            $attributes['value'] = $value['name'];
        } elseif (is_string($value)) {
            $attributes['value'] = $value;
        }

        $indent = $this->getIndent();
        $markup = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket(),
        );

        return $indent . $markup;
    }

    /**
     * Determine input type to use
     *
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    protected function getType(ElementInterface $element): string
    {
        return 'file';
    }
}
