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

namespace Calculator;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\Validator\NotEmpty;

return [
    'type' => Form::class,
    'options' => [
        'layout' => \Mezzio\BootstrapForm\LaminasView\View\Helper\Form::LAYOUT_HORIZONTAL,
    ],
    'attributes' => [
        'method' => 'post',
        'accept-charset' => 'utf-8',
        'action' => '/calculator/hr/1/input/2doqt23okbdqkgabg80guef8en?subid=A-00-000',
        'class' => 'form input-form js-help has-help has-preloader js-form-validation-base col-12 js-input-form-init',
        'data-show-arrow' => 'left',
        'id' => 'hr-form',
    ],
    'elements' => [
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'plz',
                'options' => [
                    'label' => 'PLZ - Risiko-Anschrift<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'plz',
                    'class' => 'form-control form-control-input form-control-short js-special-zip-message js-adress-completition',
                    'required' => 'required',
                    'pattern' => '^\d{5}$',
                    'minlength' => '5',
                    'maxlength' => '5',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'ort',
                'options' => [
                    'label' => 'Ort',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => ['' => 'Bitte zuerst PLZ eintragen'],
                    'disable_inarray_validator' => true,
                ],
                'attributes' => ['id' => 'ort'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'strasse',
                'options' => [
                    'label' => 'Straße',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => ['' => 'Bitte zuerst Ort wählen'],
                    'disable_inarray_validator' => true,
                ],
                'attributes' => ['id' => 'strasse'],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'hnr',
                'options' => [
                    'label' => 'Hausnummer',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'hnr',
                    'class' => 'form-control form-control-input form-control-short',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'etage',
                'options' => [
                    'label' => 'Etage',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '0' => 'EG',
                        '1' => '1.Stock und höher',
                    ],
                ],
                'attributes' => ['id' => 'etage'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'versbeginn',
                'options' => [
                    'label' => 'Versicherungsbeginn',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'sofort' => 'schnellstmöglich',
                        'datum' => 'Datum angeben',
                    ],
                ],
                'attributes' => [
                    'id' => 'versbeginn',
                    'class' => 'form-control form-control-select toggle-trigger',
                    'data-toggle-modus' => 'show',
                    'data-toggle-value' => 'datum',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'versbeginn_datum',
                'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                'options' => [
                    'label' => 'Beginn am<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                ],
                'attributes' => [
                    'id' => 'versbeginn_datum',
                    'class' => 'form-control form-control-input datepicker js-datepicker',
                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'gebdatum',
                'options' => [
                    'label' => 'Geburtsdatum<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'gebdatum',
                    'class' => 'form-control form-control-input datepicker js-datepicker',
                    'placeholder' => 'TT.MM.JJJJ',
                    'required' => 'required',
                    'data-date-format' => 'de',
                    'data-date-format-message' => 'Bitte geben Sie ein korrektes Geburtsdatum an!',
                    'data-min-age' => '18',
                    'data-min-age-message' => 'Sie sind leider zu jung, um eine Versicherung abzuschließen.',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'whg',
                'options' => [
                    'label' => 'Wo wohnen Sie?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'Mehrfamilienhaus' => 'Mehrfamilienhaus',
                        'Einfamilienhaus' => 'Einfamilienhaus',
                        'Zweifamilienhaus' => 'Zweifamilienhaus',
                        'Doppelhaushälfte' => 'Doppelhaushälfte',
                        'Reihenhaus' => 'Reihenhaus',
                    ],
                ],
                'attributes' => [
                    'id' => 'whg',
                    'class' => 'form-control form-control-select toggle-trigger',
                    'data-toggle-modus' => 'hide',
                    'data-toggle-value' => 'Mehrfamilienhaus',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'vermietet',
                'options' => [
                    'label' => 'Ist die Wohnung möbliert vermietet?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'vermietet'],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'wohnfl',
                'options' => [
                    'label' => 'Ihre gesamte Wohnfläche im Haus<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'wohnfl',
                    'class' => 'form-control form-control-input has-legend',
                    'required' => 'required',
                    'min' => '10',
                    'max' => '2000',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'wohnfl_kg',
                'options' => [
                    'label' => 'Davon sind im Keller',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'wohnfl_kg',
                    'class' => 'form-control form-control-input has-legend',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'kellerfl',
                'options' => [
                    'label' => 'Grundfläche des Kellers',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'kellerfl',
                    'class' => 'form-control form-control-input has-legend',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'beamte',
                'options' => [
                    'label' => 'Tarif',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'Normal' => 'Normal',
                        'öffentl. Dienst' => 'öffentlicher Dienst',
                    ],
                ],
                'attributes' => ['id' => 'beamte'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'verssummeauto',
                'options' => [
                    'label' => 'Versicherungssumme',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'auto' => 'automatisch ermitteln',
                        'manuell' => 'selbst angeben',
                    ],
                ],
                'attributes' => [
                    'id' => 'verssummeauto',
                    'class' => 'form-control form-control-select toggle-trigger',
                    'data-toggle-modus' => 'show',
                    'data-toggle-value' => 'manuell',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'verssumme',
                'options' => [
                    'label' => 'Versicherungssumme selbst angeben',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'verssumme',
                    'class' => 'form-control form-control-input has-legend',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'fahrrad',
                'options' => [
                    'label' => 'Fahrraddiebstahl bis',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'fahrrad',
                    'class' => 'form-control form-control-input has-legend',
                    'min' => '0',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'glas',
                'options' => [
                    'label' => 'Glasversicherung',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'glas'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'elem',
                'options' => [
                    'label' => 'Elementarschäden (Überschwemmung, Erdbeben, Erdsenkung, Erdrutsch, Schneedruck- und Lawinenschäden)',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'elem'],
            ],
        ],
        'grob' => [
            'spec' => [
                'type' => Select::class,
                'name' => 'grob',
                'options' => [
                    'label' => 'Mitversicherung der groben Fahrlässigkeit?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'grob'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'bauart',
                'options' => [
                    'label' => 'Bauartklasse <a href="#modal-dialog-contruction-type" data-toggle="modal">Info</a>',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'massive Bauweise mit harter Dachung (BAK I)' => 'massive Bauweise mit harter Dachung (BAK I)',
                        'Stahl/Glas Bauweise mit harter Dachung (BAK II)' => 'Stahl/Glas Bauweise mit harter Dachung (BAK II)',
                        'Holzhaus oder Lehmfachwerk mit harter Dachung (BAK III)' => 'Holzhaus oder Lehmfachwerk mit harter Dachung (BAK III)',
                        'weiche Dachung (BAK IV oder V)' => 'weiche Dachung (BAK IV oder V)',
                        'Fertighaus, massiv mit harter Dachung (FHG I, FHG II)' => 'Fertighaus, massiv mit harter Dachung (FHG I, FHG II)',
                        'Fertighaus mit harter Dachung (FHG III)' => 'Fertighaus mit harter Dachung (FHG III)',
                    ],
                    'label_options' => ['disable_html_escape' => true],
                ],
                'attributes' => ['id' => 'bauart'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'selbst',
                'options' => [
                    'label' => 'Selbstbeteiligung',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => '0 €',
                        '150' => '150 €',
                        '200' => '200 €',
                        '250' => '250 €',
                        '300' => '300 €',
                    ],
                ],
                'attributes' => ['id' => 'selbst'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'zahlweise',
                'options' => [
                    'label' => 'Zahlweise',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '1' => 'jährlich',
                        '2' => 'halbjährlich',
                        '4' => 'vierteljährlich',
                        '12' => 'monatlich',
                    ],
                ],
                'attributes' => ['id' => 'zahlweise'],
            ],
        ],
        'laufzeit' => [
            'spec' => [
                'type' => Select::class,
                'name' => 'laufzeit',
                'options' => [
                    'label' => 'Laufzeit',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '1' => '1 Jahr',
                        '3' => '3 Jahre',
                        '5' => '5 Jahre',
                    ],
                ],
                'attributes' => ['id' => 'laufzeit'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'vorvers5',
                'options' => [
                    'label' => 'Bestand in den letzten 5 Jahren eine Vorversicherung?<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '' => '-- Bitte wählen --',
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'vorvers5',

                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'schaeden5',
                'options' => [
                    'label' => 'Schäden in den letzten 5 Jahren<span class="text-info-required">*</span>',
                    'label_options' => ['disable_html_escape' => true],
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        '' => '-- Bitte wählen --',
                        '0' => '0',
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ],
                ],
                'attributes' => [
                    'id' => 'schaeden5',

                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'kombirabatte',
                'options' => [
                    'label' => 'Kombirabatte mit berechnen?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => [
                    'id' => 'kombirabatte',
                    'class' => 'form-control form-control-select toggle-trigger',
                    'data-toggle-modus' => 'show',
                    'data-toggle-value' => 'ja',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrPHV',
                'options' => [
                    'label' => 'Privathaftpflicht',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrPHV'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrTIE',
                'options' => [
                    'label' => 'Tierhalterhaftpflicht',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrTIE'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrHUG',
                'options' => [
                    'label' => 'Haus-Grundbesitzer Haftpflicht',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrHUG'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrOEL',
                'options' => [
                    'label' => 'Gewässerschaden/Öltank',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrOEL'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrWG',
                'options' => [
                    'label' => 'Wohngebäude',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrWG'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrWGGLS',
                'options' => [
                    'label' => 'Wohngebäude-Glas',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrWGGLS'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrHR',
                'options' => [
                    'label' => 'Hausrat',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrHR'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrHRGLS',
                'options' => [
                    'label' => 'Hausrat-Glas',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrHRGLS'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrUNF',
                'options' => [
                    'label' => 'Unfall',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrUNF'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'KrRS',
                'options' => [
                    'label' => 'Rechtsschutz',
                    'label_attributes' => ['class' => 'col-sm text-sm-right'],
                    'use_hidden_element' => false,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => ['id' => 'KrRS'],
            ],
        ],
        [
            'spec' => [
                'type' => Radio::class,
                'name' => 'zusatzfragen',
                'options' => [
                    'label' => 'weitere Fragen',
                    'value_options' => [
                        'nein' => [
                            'value' => 'nein',
                            'label' => 'Ich verzichte auf die Beantwortung weiterer Fragen und wähle aus dem Vergleich einen Tarif, der meinen Bedarf erfüllt.',
                            'attributes' => [
                                'id' => 'zusatzfragen_nein',
                                'class' => 'form-check-input form-radio-input',
                                'required' => 'required',
                            ],
                            'label_attributes' => [
                                'class' => 'form-radio js-gtm-event',
                                'for' => 'zusatzfragen_nein',
                                'data-event-type' => 'click',
                                'data-event-category' => 'versicherung',
                                'data-event-action' => 'no additional questions',
                                'data-event-label' => 'hr',
                            ],
                        ],
                        'ja' => [
                            'value' => 'ja',
                            'label' => 'Ich möchte weitere Angaben zum gewünschten Versicherungsschutz machen. Es werden dann nur Tarife angezeigt, welche die Vorgaben erfüllen.',
                            'attributes' => [
                                'id' => 'zusatzfragen_ja',
                                'class' => 'form-check-input form-radio-input',
                                'required' => 'required',
                            ],
                            'label_attributes' => [
                                'class' => 'form-radio js-gtm-event',
                                'for' => 'zusatzfragen_ja',
                                'data-event-type' => 'click',
                                'data-event-category' => 'versicherung',
                                'data-event-action' => 'additional questions requested',
                                'data-event-label' => 'hr',
                            ],
                        ],
                    ],
                ],
                'attributes' => ['id' => 'zusatzfragen'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'uver_v',
                'options' => [
                    'label' => 'Wünschen Sie einen Unterversicherungsverzicht?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein - Eingabe einer V-Summe' => 'nein - Eingabe einer V-Summe',
                        'ja' => 'ja - empfohlen',
                    ],
                ],
                'attributes' => ['id' => 'uver_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'ues_v',
                'options' => [
                    'label' => 'Mitversicherung von Überspannungsschäden?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'ja' => 'ja - empfohlen',
                        'nein' => 'nein',
                    ],
                ],
                'attributes' => ['id' => 'ues_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Text::class,
                'name' => 'werts_v',
                'options' => [
                    'label' => 'Mitversicherung von Wertsachen? Ohne Angaben sind mind. 20% der VS mitversichert.',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                ],
                'attributes' => [
                    'id' => 'werts_v',
                    'class' => 'form-control form-control-input has-legend',
                    'min' => '0',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'dieb_v',
                'options' => [
                    'label' => 'Diebstahl von Kinderwagen und Krankenfahrstühlen?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'dieb_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'dkfz_v',
                'options' => [
                    'label' => 'Diebstahl aus KFZ?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'dkfz_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'dkle_v',
                'options' => [
                    'label' => 'Diebstahl von Wäsche auf der Leine?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'dkle_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'rauch_v',
                'options' => [
                    'label' => 'Schäden durch Verpuffung, Rauch und Ruß?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'rauch_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'kfz_v',
                'options' => [
                    'label' => 'Schäden durch Anprall von Landfahrzeugen?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'kfz_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'bank_v',
                'options' => [
                    'label' => 'Sachen in Bankgewahrsam?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'bank_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'dgar_v',
                'options' => [
                    'label' => 'Diebstahl von Gartenmöbeln/Geräten?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'dgar_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'was_v',
                'options' => [
                    'label' => 'Haben Sie Aquarien oder Wasserbetten (Wasserschäden)?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'was_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'seng_v',
                'options' => [
                    'label' => 'Sollen Sengschäden mitversichert werden?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'seng_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'waver_v',
                'options' => [
                    'label' => 'Soll Wasserverlust infolge Rohrbruch mitversichert werden?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'waver_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'hot_v',
                'options' => [
                    'label' => 'Hotelkosten im Schadenfall?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'hot_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'reis_v',
                'options' => [
                    'label' => 'Rückreisekosten aus dem Urlaub?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'reis_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'arb_v',
                'options' => [
                    'label' => 'Sachen im häuslichen Arbeitszimmer?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'arb_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'evors_v',
                'options' => [
                    'label' => 'Erweiterte Vorsorge',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'evors_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'all_v',
                'options' => [
                    'label' => 'Wünschen Sie für Ihren Hausrat eine Allgefahrendeckung?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'all_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'sich_v',
                'options' => [
                    'label' => 'Sind an allen Haus- und sonstigen Eingangstüren Sicherheitsschlösser mit von außen nicht abschraubbaren, bündig montierten Sicherheitsbeschlägen vorhanden?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'sich_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'meld_v',
                'options' => [
                    'label' => 'Ist eine vom VdS (Verband der Sachversicherer) anerkannte Einbruchmeldeanlage vorhanden?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'meld_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'bewo_v',
                'options' => [
                    'label' => 'Ist die Wohnung länger als 60 Tage ununterbrochen unbewohnt?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'bewo_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'tres_v',
                'options' => [
                    'label' => 'Ist ein mehrwandiger Stahlschrank mit einem Gewicht von > 200 kg oder ein eingemauerter Tresor mit mehrwandiger Tür vorhanden?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'tres_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'feu_v',
                'options' => [
                    'label' => 'Gibt es auf dem Versicherungsgrundstück oder in einer Entfernung von unter 10 m Betriebe / Lager, von denen eine erhöhte Feuergefahr ausgeht?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'feu_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Select::class,
                'name' => 'feu_v',
                'options' => [
                    'label' => 'Gibt es auf dem Versicherungsgrundstück oder in einer Entfernung von unter 10 m Betriebe / Lager, von denen eine erhöhte Feuergefahr ausgeht?',
                    'label_attributes' => ['class' => 'col-sm col-form-label text-sm-right'],
                    'value_options' => [
                        'nein' => 'nein',
                        'ja' => 'ja',
                    ],
                ],
                'attributes' => ['id' => 'feu_v'],
            ],
        ],
        [
            'spec' => [
                'type' => Checkbox::class,
                'name' => 'chkErstinfo',
                'options' => [
                    'use_hidden_element' => true,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
                'attributes' => [
                    'id' => 'mrmoErstinfo',

                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Hidden::class,
                'name' => 'sToken',
            ],
        ],
        [
            'spec' => [
                'type' => Submit::class,
                'name' => 'btn_berechnen',
                'options' => ['label' => 'Berechnen'],
                'attributes' => [
                    'value' => 'Berechnen',
                    'class' => 'btn btn-default js-gtm-event',
                    'data-event-type' => 'click',
                    'data-event-category' => 'versicherung',
                    'data-event-action' => 'calculate',
                    'data-event-label' => 'hr',
                ],
            ],
        ],
        [
            'spec' => [
                'type' => Submit::class,
                'name' => 'recalc',
                'options' => ['label' => 'neu berechnen'],
                'attributes' => ['value' => 'neu berechnen'],
            ],
        ],
    ],
    'input_filter' => [
        'plz' => ['required' => true],
        'ort' => [
            'required' => false,
            'validators' => [
                [
                    'name' => NotEmpty::class,
                ],
            ],
        ],
        'strasse' => [
            'required' => false,
            'validators' => [
                [
                    'name' => NotEmpty::class,
                ],
            ],
        ],
        'hnr' => ['required' => false],
        'etage' => ['required' => false],
        'versbeginn' => ['required' => false],
        'versbeginn_datum' => ['required' => false],
        'gebdatum' => ['required' => true],
        'vorvers5' => ['required' => true],
        'schaeden5' => ['required' => true],
        'KrPHV' => ['required' => false],
        'KrTIE' => ['required' => false],
        'KrHUG' => ['required' => false],
        'KrOEL' => ['required' => false],
        'KrWG' => ['required' => false],
        'KrWGGLS' => ['required' => false],
        'KrHR' => ['required' => false],
        'KrHRGLS' => ['required' => false],
        'KrUNF' => ['required' => false],
        'KrRS' => ['required' => false],
        'selbst' => ['required' => false],
        'zahlweise' => ['required' => false],
    ],
];
