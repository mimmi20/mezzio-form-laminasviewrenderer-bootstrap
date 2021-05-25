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
use Laminas\Form\Exception;
use Locale;

use function extension_loaded;
use function mb_stripos;
use function preg_split;
use function sprintf;
use function str_replace;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

trait FormMonthSelectTrait
{
    /**
     * FormSelect helper
     */
    private FormSelectInterface $selectHelper;

    /**
     * Date formatter to use
     */
    private int $dateType = IntlDateFormatter::LONG;

    /**
     * Pattern to use for Date rendering
     */
    private ?string $pattern = null;

    /**
     * Locale to use
     */
    private ?string $locale = null;

    /**
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present
     */
    public function __construct(FormSelectInterface $selectHelper)
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
    }

    /**
     * Retrieve pattern to use for Date rendering
     */
    public function getPattern(): string
    {
        if (null === $this->pattern) {
            $intl          = new IntlDateFormatter($this->getLocale(), $this->dateType, IntlDateFormatter::NONE);
            $this->pattern = $intl->getPattern();
        }

        return $this->pattern;
    }

    /**
     * Set date formatter
     */
    public function setDateType(int $dateType): self
    {
        // The FULL format uses values that are not used
        if (IntlDateFormatter::FULL === $dateType) {
            $dateType = IntlDateFormatter::LONG;
        }

        $this->dateType = $dateType;

        return $this;
    }

    /**
     * Get date formatter
     */
    public function getDateType(): int
    {
        return $this->dateType;
    }

    /**
     * Set locale
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     */
    public function getLocale(): string
    {
        if (null === $this->locale) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
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
            "/([ -,.\\/]*(?:'[a-zA-Z]+')*[ -,.\\/]+)/",
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
            } elseif (false === mb_stripos($value, "'") && false !== mb_stripos($value, 'm')) {
                $result['month'] = $value;
            } elseif (false === mb_stripos($value, "'") && false !== mb_stripos($value, 'y')) {
                $result['year'] = $value;
            } elseif ($renderDelimiters) {
                $result[] = str_replace("'", '', $value);
            }
        }

        return $result;
    }

    /**
     * Create a key => value options for months
     *
     * @param string $pattern Pattern to use for months
     *
     * @return array<int|string, array<string, string>>
     */
    private function getMonthsOptions(string $pattern): array
    {
        $keyFormatter   = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'MM');
        $valueFormatter = new IntlDateFormatter($this->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, $pattern);
        $date           = new DateTime('1970-01-01');

        $result = [];
        for ($month = 1; 12 >= $month; ++$month) {
            $key = $keyFormatter->format($date->getTimestamp());

            if (false === $key) {
                continue;
            }

            $value = $valueFormatter->format($date->getTimestamp());

            if (false === $value) {
                continue;
            }

            $result[$key] = ['value' => $key, 'label' => $value];

            $date->modify('+1 month');
        }

        return $result;
    }

    /**
     * Create a key => value options for years
     * NOTE: we don't use a pattern for years, as years written as two digits can lead to hard to
     * read date for users, so we only use four digits years
     *
     * @return array<int|string, array<string, string>>
     */
    private function getYearsOptions(int $minYear, int $maxYear): array
    {
        $result = [];
        for ($i = $maxYear; $i >= $minYear; --$i) {
            $result[$i] = ['value' => (string) $i, 'label' => (string) $i];
        }

        return $result;
    }
}
