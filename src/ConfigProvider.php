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

use Mimmi20\Form\Links\View\Helper\FormLinks;
use Mimmi20\Form\Links\View\Helper\FormLinksFactory;
use Mimmi20\Form\Paragraph\View\Helper\FormParagraph;
use Mimmi20\Form\Paragraph\View\Helper\FormParagraphFactory;

final class ConfigProvider
{
    /**
     * Return general-purpose laminas-form configuration.
     *
     * @return array<string, array<string, array<string, string>>>
     * @phpstan-return array{view_helpers: array{aliases: array<string, class-string>, factories: array<class-string, class-string>}}
     *
     * @throws void
     */
    public function __invoke(): array
    {
        return [
            'view_helpers' => $this->getViewHelperConfig(),
        ];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return array<string, array<string, string>>
     * @phpstan-return array{aliases: array<string, class-string>, factories: array<class-string, class-string>}
     *
     * @throws void
     */
    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'form' => Form::class,
                'Form' => Form::class,
                'formbutton' => FormButton::class,
                'form_button' => FormButton::class,
                'formButton' => FormButton::class,
                'FormButton' => FormButton::class,
                'formcheckbox' => FormCheckbox::class,
                'form_checkbox' => FormCheckbox::class,
                'formCheckbox' => FormCheckbox::class,
                'FormCheckbox' => FormCheckbox::class,
                FormCollectionInterface::class => FormCollection::class,
                'formcollection' => FormCollection::class,
                'form_collection' => FormCollection::class,
                'formCollection' => FormCollection::class,
                'FormCollection' => FormCollection::class,
                'formcolor' => FormColor::class,
                'form_color' => FormColor::class,
                'formColor' => FormColor::class,
                'FormColor' => FormColor::class,
                'formdate' => FormDate::class,
                'form_date' => FormDate::class,
                'formDate' => FormDate::class,
                'FormDate' => FormDate::class,
                'formdatetime' => FormDateTime::class,
                'form_date_time' => FormDateTime::class,
                'formDateTime' => FormDateTime::class,
                'formDatetime' => FormDateTime::class,
                'FormDateTime' => FormDateTime::class,
                'formdatetimelocal' => FormDateTimeLocal::class,
                'form_date_time_local' => FormDateTimeLocal::class,
                'formDateTimeLocal' => FormDateTimeLocal::class,
                'FormDateTimeLocal' => FormDateTimeLocal::class,
                'formdatetimeselect' => FormDateTimeSelect::class,
                'form_date_time_select' => FormDateTimeSelect::class,
                'formDateTimeSelect' => FormDateTimeSelect::class,
                'FormDateTimeSelect' => FormDateTimeSelect::class,
                'formdateselect' => FormDateSelect::class,
                'form_date_select' => FormDateSelect::class,
                'formDateSelect' => FormDateSelect::class,
                'FormDateSelect' => FormDateSelect::class,
                FormElementInterface::class => FormElement::class,
                'form_element' => FormElement::class,
                'formelement' => FormElement::class,
                'formElement' => FormElement::class,
                'FormElement' => FormElement::class,
                FormElementErrorsInterface::class => FormElementErrors::class,
                'form_element_errors' => FormElementErrors::class,
                'formelementerrors' => FormElementErrors::class,
                'formElementErrors' => FormElementErrors::class,
                'FormElementErrors' => FormElementErrors::class,
                'form_email' => FormEmail::class,
                'formemail' => FormEmail::class,
                'formEmail' => FormEmail::class,
                'FormEmail' => FormEmail::class,
                'form_file' => FormFile::class,
                'formfile' => FormFile::class,
                'formFile' => FormFile::class,
                'FormFile' => FormFile::class,
                FormHiddenInterface::class => FormHidden::class,
                'formhidden' => FormHidden::class,
                'form_hidden' => FormHidden::class,
                'formHidden' => FormHidden::class,
                'FormHidden' => FormHidden::class,
                'formimage' => FormImage::class,
                'form_image' => FormImage::class,
                'formImage' => FormImage::class,
                'FormImage' => FormImage::class,
                'forminput' => FormInput::class,
                'form_input' => FormInput::class,
                'formInput' => FormInput::class,
                'FormInput' => FormInput::class,
                FormLabelInterface::class => FormLabel::class,
                'formlabel' => FormLabel::class,
                'form_label' => FormLabel::class,
                'formLabel' => FormLabel::class,
                'FormLabel' => FormLabel::class,
                'formlinks' => FormLinks::class,
                'form_links' => FormLinks::class,
                'formLinks' => FormLinks::class,
                'FormLinks' => FormLinks::class,
                'formmonth' => FormMonth::class,
                'form_month' => FormMonth::class,
                'formMonth' => FormMonth::class,
                'FormMonth' => FormMonth::class,
                'formmonthselect' => FormMonthSelect::class,
                'form_month_select' => FormMonthSelect::class,
                'formMonthSelect' => FormMonthSelect::class,
                'FormMonthSelect' => FormMonthSelect::class,
                'formmulticheckbox' => FormMultiCheckbox::class,
                'form_multi_checkbox' => FormMultiCheckbox::class,
                'formMultiCheckbox' => FormMultiCheckbox::class,
                'FormMultiCheckbox' => FormMultiCheckbox::class,
                'formnumber' => FormNumber::class,
                'form_number' => FormNumber::class,
                'formNumber' => FormNumber::class,
                'FormNumber' => FormNumber::class,
                'formparagraph' => FormParagraph::class,
                'form_paragraph' => FormParagraph::class,
                'formParagraph' => FormParagraph::class,
                'FormParagraph' => FormParagraph::class,
                'formpassword' => FormPassword::class,
                'form_password' => FormPassword::class,
                'formPassword' => FormPassword::class,
                'FormPassword' => FormPassword::class,
                'formradio' => FormRadio::class,
                'form_radio' => FormRadio::class,
                'formRadio' => FormRadio::class,
                'FormRadio' => FormRadio::class,
                'formrange' => FormRange::class,
                'form_range' => FormRange::class,
                'formRange' => FormRange::class,
                'FormRange' => FormRange::class,
                'formreset' => FormReset::class,
                'form_reset' => FormReset::class,
                'formReset' => FormReset::class,
                'FormReset' => FormReset::class,
                FormRowInterface::class => FormRow::class,
                'formrow' => FormRow::class,
                'form_row' => FormRow::class,
                'formRow' => FormRow::class,
                'FormRow' => FormRow::class,
                'formsearch' => FormSearch::class,
                'form_search' => FormSearch::class,
                'formSearch' => FormSearch::class,
                'FormSearch' => FormSearch::class,
                FormSelectInterface::class => FormSelect::class,
                'formselect' => FormSelect::class,
                'form_select' => FormSelect::class,
                'formSelect' => FormSelect::class,
                'FormSelect' => FormSelect::class,
                'formsubmit' => FormSubmit::class,
                'form_submit' => FormSubmit::class,
                'formSubmit' => FormSubmit::class,
                'FormSubmit' => FormSubmit::class,
                'formtel' => FormTel::class,
                'form_tel' => FormTel::class,
                'formTel' => FormTel::class,
                'FormTel' => FormTel::class,
                'formtext' => FormText::class,
                'form_text' => FormText::class,
                'formText' => FormText::class,
                'FormText' => FormText::class,
                'formtextarea' => FormTextarea::class,
                'form_text_area' => FormTextarea::class,
                'formTextarea' => FormTextarea::class,
                'formTextArea' => FormTextarea::class,
                'FormTextArea' => FormTextarea::class,
                'formtime' => FormTime::class,
                'form_time' => FormTime::class,
                'formTime' => FormTime::class,
                'FormTime' => FormTime::class,
                'formurl' => FormUrl::class,
                'form_url' => FormUrl::class,
                'formUrl' => FormUrl::class,
                'FormUrl' => FormUrl::class,
                'formweek' => FormWeek::class,
                'form_week' => FormWeek::class,
                'formWeek' => FormWeek::class,
                'FormWeek' => FormWeek::class,
            ],
            'factories' => [
                Form::class => FormFactory::class,
                FormButton::class => FormButtonFactory::class,
                FormCollection::class => FormCollectionFactory::class,
                FormCheckbox::class => FormCheckboxFactory::class,
                FormColor::class => FormColorFactory::class,
                FormDate::class => FormDateFactory::class,
                FormDateSelect::class => FormDateSelectFactory::class,
                FormDateTime::class => FormDateTimeFactory::class,
                FormDateTimeLocal::class => FormDateTimeLocalFactory::class,
                FormDateTimeSelect::class => FormDateTimeSelectFactory::class,
                FormElement::class => FormElementFactory::class,
                FormElementErrors::class => FormElementErrorsFactory::class,
                FormEmail::class => FormEmailFactory::class,
                FormFile::class => FormFileFactory::class,
                FormHidden::class => FormHiddenFactory::class,
                FormImage::class => FormImageFactory::class,
                FormLabel::class => FormLabelFactory::class,
                FormLinks::class => FormLinksFactory::class,
                FormMonth::class => FormMonthFactory::class,
                FormMonthSelect::class => FormMonthSelectFactory::class,
                FormMultiCheckbox::class => FormMultiCheckboxFactory::class,
                FormNumber::class => FormNumberFactory::class,
                FormParagraph::class => FormParagraphFactory::class,
                FormPassword::class => FormPasswordFactory::class,
                FormRadio::class => FormRadioFactory::class,
                FormRange::class => FormRangeFactory::class,
                FormReset::class => FormResetFactory::class,
                FormRow::class => FormRowFactory::class,
                FormSearch::class => FormSearchFactory::class,
                FormSelect::class => FormSelectFactory::class,
                FormSubmit::class => FormSubmitFactory::class,
                FormTel::class => FormTelFactory::class,
                FormText::class => FormTextFactory::class,
                FormTextarea::class => FormTextareaFactory::class,
                FormTime::class => FormTimeFactory::class,
                FormUrl::class => FormUrlFactory::class,
                FormWeek::class => FormWeekFactory::class,
            ],
        ];
    }
}
