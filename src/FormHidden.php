<?php

declare(strict_types = 1);

namespace Mezzio\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormInput;

class FormHidden extends FormInput
{
    use FormTrait;

    /**
     * Attributes valid for the input tag type="hidden"
     *
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $validTagAttributes = [
        'name'           => true,
        'disabled'       => true,
        'form'           => true,
        'type'           => true,
        'value'          => true,
        'autocomplete'   => true,
    ];

    /**
     * Determine input type to use
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    protected function getType(ElementInterface $element): string
    {
        return 'hidden';
    }

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
