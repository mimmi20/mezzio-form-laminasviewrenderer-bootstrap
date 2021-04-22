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

use Laminas\Form\View\Helper\FormEmail;
use Laminas\Form\View\Helper\FormHidden;
use Laminas\Form\View\Helper\FormPassword;
use Laminas\Form\View\Helper\FormText;
use Laminas\ServiceManager\Factory\InvokableFactory;

final class ConfigProvider
{
    /**
     * Return general-purpose laminas-navigation configuration.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public function __invoke(): array
    {
        return [
            'view_helpers' => $this->getViewHelperConfig(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return array<string, array<string, string>>
     */
    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'form' => Form::class,
                'formButton' => FormButton::class,
                'formCheckbox' => FormCheckbox::class,
                'formCollection' => FormCollection::class,
                'formElement' => FormElement::class,
                'form_element' => FormElement::class,
                'formElementErrors' => FormElementErrors::class,
                'formFile' => FormFile::class,
                'formLabel' => FormLabel::class,
                'formMultiCheckbox' => FormMultiCheckbox::class,
                'formRadio' => FormRadio::class,
                'formRange' => FormRange::class,
                'formRow' => FormRow::class,
                'formSelect' => FormSelect::class,
                'formSubmit' => FormSubmit::class,
                // original Laminas Viewhelpers
                'formEmail' => FormEmail::class,
                'formHidden' => FormHidden::class,
                'formPassword' => FormPassword::class,
                'formText' => FormText::class,
            ],
            'factories' => [
                Form::class => FormFactory::class,
                FormButton::class => FormButtonFactory::class,
                FormCollection::class => FormCollectionFactory::class,
                FormCheckbox::class => FormCheckboxFactory::class,
                FormElement::class => FormElementFactory::class,
                FormElementErrors::class => FormElementErrorsFactory::class,
                FormMultiCheckbox::class => FormMultiCheckboxFactory::class,
                FormRadio::class => FormRadioFactory::class,
                FormRange::class => InvokableFactory::class,
                FormRow::class => FormRowFactory::class,
                FormSelect::class => FormSelectFactory::class,
                FormSubmit::class => InvokableFactory::class,
                FormLabel::class => FormLabelFactory::class,
                FormFile::class => InvokableFactory::class,
                // original Laminas Viewhelpers
                FormEmail::class => InvokableFactory::class,
                FormHidden::class => InvokableFactory::class,
                FormPassword::class => InvokableFactory::class,
                FormText::class => InvokableFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array<string, array<string, array<int, string>>>
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'horizontal' => [__DIR__ . '/../templates/horizontal'],
                'vertical' => [__DIR__ . '/../templates/vertical'],
                'elements' => [__DIR__ . '/../templates/elements'],
            ],
        ];
    }
}
