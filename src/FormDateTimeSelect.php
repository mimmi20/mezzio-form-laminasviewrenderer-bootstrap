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

use DateTime;
use IntlDateFormatter;
use Laminas\Form\Element\DateTimeSelect as DateTimeSelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\View\Helper\AbstractHelper;

use function extension_loaded;
use function is_numeric;
use function mb_stripos;
use function mb_strpos;
use function preg_split;
use function rtrim;
use function sprintf;
use function str_replace;
use function trim;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

final class FormDateTimeSelect extends AbstractHelper
{
    use FormDateSelectTrait;
    use FormMonthSelectTrait;
    use FormTrait;

    /**
     * Time formatter to use
     */
    private int $timeType;

    /**
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present
     */
    public function __construct(FormSelect $selectHelper)
    {
        if (!extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(
                sprintf(
                    '%s component requires the intl PHP extension',
                    __NAMESPACE__
                )
            );
        }

        $this->selectHelper = $selectHelper;

        // Delaying initialization until we know ext/intl is available
        $this->dateType = IntlDateFormatter::LONG;

        // Delaying initialization until we know ext/intl is available
        $this->timeType = IntlDateFormatter::LONG;
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function __invoke(
        ?ElementInterface $element = null,
        int $dateType = IntlDateFormatter::LONG,
        int $timeType = IntlDateFormatter::LONG,
        ?string $locale = null
    ) {
        if (!$element) {
            return $this;
        }

        $this->setDateType($dateType);
        $this->setTimeType($timeType);

        if (null !== $locale) {
            $this->setLocale($locale);
        }

        return $this->render($element);
    }

    /**
     * Render a date element that is composed of six selects
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof DateTimeSelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\DateTimeSelect',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if (null === $name || '' === $name) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $shouldRenderDelimiters = $element->shouldRenderDelimiters();
        $pattern                = $this->parsePattern($shouldRenderDelimiters);

        $daysOptions   = $this->getDaysOptions($pattern['day']);
        $monthsOptions = $this->getMonthsOptions($pattern['month']);
        $yearOptions   = $this->getYearsOptions($element->getMinYear(), $element->getMaxYear());
        $hourOptions   = $this->getHoursOptions($pattern['hour']);
        $minuteOptions = $this->getMinutesOptions($pattern['minute']);
        $secondOptions = $this->getSecondsOptions($pattern['second']);

        $dayElement    = $element->getDayElement()->setValueOptions($daysOptions);
        $monthElement  = $element->getMonthElement()->setValueOptions($monthsOptions);
        $yearElement   = $element->getYearElement()->setValueOptions($yearOptions);
        $hourElement   = $element->getHourElement()->setValueOptions($hourOptions);
        $minuteElement = $element->getMinuteElement()->setValueOptions($minuteOptions);
        $secondElement = $element->getSecondElement()->setValueOptions($secondOptions);

        if ($element->shouldCreateEmptyOption()) {
            $dayElement->setEmptyOption('');
            $yearElement->setEmptyOption('');
            $monthElement->setEmptyOption('');
            $hourElement->setEmptyOption('');
            $minuteElement->setEmptyOption('');
            $secondElement->setEmptyOption('');
        }

        $data                     = [];
        $data[$pattern['day']]    = $this->selectHelper->render($dayElement);
        $data[$pattern['month']]  = $this->selectHelper->render($monthElement);
        $data[$pattern['year']]   = $this->selectHelper->render($yearElement);
        $data[$pattern['hour']]   = $this->selectHelper->render($hourElement);
        $data[$pattern['minute']] = $this->selectHelper->render($minuteElement);

        if ($element->shouldShowSeconds()) {
            $data[$pattern['second']] = $this->selectHelper->render($secondElement);
        } else {
            unset($pattern['second']);
            if ($shouldRenderDelimiters) {
                unset($pattern[4]);
            }
        }

        $markup = '';
        foreach ($pattern as $key => $value) {
            // Delimiter
            if (is_numeric($key)) {
                $markup .= $value;
            } else {
                $markup .= $data[$value];
            }
        }

        return trim($markup);
    }

    public function setTimeType(int $timeType): self
    {
        // The FULL format uses values that are not used
        if (IntlDateFormatter::FULL === $timeType) {
            $timeType = IntlDateFormatter::LONG;
        }

        $this->timeType = $timeType;

        return $this;
    }

    public function getTimeType(): int
    {
        return $this->timeType;
    }

    /**
     * Override to also get time part
     */
    public function getPattern(): string
    {
        if (null === $this->pattern) {
            $intl = new IntlDateFormatter($this->getLocale(), $this->dateType, $this->timeType);
            // remove time zone format character
            $pattern       = rtrim($intl->getPattern(), ' z');
            $this->pattern = $pattern;
        }

        return $this->pattern;
    }

    /**
     * Parse the pattern
     *
     * @return array<int|string, string>
     */
    private function parsePattern(bool $renderDelimiters = true): array
    {
        $pattern    = $this->getPattern();
        $pregResult = preg_split(
            "/([ -,.:\\/]*'.*?'[ -,.:\\/]*)|([ -,.:\\/]+)/",
            $pattern,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        if (false === $pregResult) {
            return [];
        }

        $result = [];
        foreach ($pregResult as $value) {
            if (false === mb_stripos($value, "'") && false !== mb_stripos($value, 'd')) {
                $result['day'] = $value;
            } elseif (false === mb_stripos($value, "'") && false !== mb_strpos($value, 'M')) {
                $result['month'] = $value;
            } elseif (false === mb_stripos($value, "'") && false !== mb_stripos($value, 'y')) {
                $result['year'] = $value;
            } elseif (false === mb_stripos($value, "'") && false !== mb_stripos($value, 'h')) {
                $result['hour'] = $value;
            } elseif (false === mb_stripos($value, "'") && false !== mb_stripos($value, 'm')) {
                $result['minute'] = $value;
            } elseif (false === mb_stripos($value, "'") && false !== mb_strpos($value, 's')) {
                $result['second'] = $value;
            } elseif (false === mb_stripos($value, "'") && false !== mb_stripos($value, 'a')) {
                // ignore ante/post meridiem marker
                continue;
            } elseif ($renderDelimiters) {
                $result[] = str_replace("'", '', $value);
            }
        }

        return $result;
    }

    /**
     * Create a key => value options for hours
     *
     * @param string $pattern Pattern to use for hours
     *
     * @return array<string, string>
     */
    private function getHoursOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'HH');
        $valueFormatter = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, $pattern);
        $date           = new DateTime('1970-01-01 00:00:00');

        $result = [];
        for ($hour = 1; 24 >= $hour; ++$hour) {
            $key = $keyFormatter->format($date);

            if (false === $key) {
                continue;
            }

            $value = $valueFormatter->format($date);

            if (false === $value) {
                continue;
            }

            $result[$key] = $value;

            $date->modify('+1 hour');
        }

        return $result;
    }

    /**
     * Create a key => value options for minutes
     *
     * @param string $pattern Pattern to use for minutes
     *
     * @return array<string, string>
     */
    private function getMinutesOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'mm');
        $valueFormatter = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, $pattern);
        $date           = new DateTime('1970-01-01 00:00:00');

        $result = [];
        for ($min = 1; 60 >= $min; ++$min) {
            $key = $keyFormatter->format($date);

            if (false === $key) {
                continue;
            }

            $value = $valueFormatter->format($date);

            if (false === $value) {
                continue;
            }

            $result[$key] = $value;

            $date->modify('+1 minute');
        }

        return $result;
    }

    /**
     * Create a key => value options for seconds
     *
     * @param string $pattern Pattern to use for seconds
     *
     * @return array<string, string>
     */
    private function getSecondsOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'ss');
        $valueFormatter = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, $pattern);
        $date           = new DateTime('1970-01-01 00:00:00');

        $result = [];
        for ($sec = 1; 60 >= $sec; ++$sec) {
            $key = $keyFormatter->format($date);

            if (false === $key) {
                continue;
            }

            $value = $valueFormatter->format($date);

            if (false === $value) {
                continue;
            }

            $result[$key] = $value;

            $date->modify('+1 second');
        }

        return $result;
    }
}
