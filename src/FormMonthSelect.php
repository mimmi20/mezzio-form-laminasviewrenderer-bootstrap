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

use IntlDateFormatter;
use Laminas\Form\Element\MonthSelect as MonthSelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\AbstractHelper;

use function implode;
use function is_numeric;
use function sprintf;

use const PHP_EOL;

final class FormMonthSelect extends AbstractHelper implements FormIndentInterface, FormRenderInterface
{
    use FormMonthSelectTrait;
    use FormTrait;

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @return self|string
     *
     * @throws DomainException
     * @throws InvalidArgumentException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function __invoke(
        ElementInterface | null $element = null,
        int $dateType = IntlDateFormatter::LONG,
        string | null $locale = null,
    ) {
        if (!$element) {
            return $this;
        }

        $this->setDateType($dateType);

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        return $this->render($element);
    }

    /**
     * Render a month element that is composed of two selects
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof MonthSelectElement) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type %s',
                    __METHOD__,
                    MonthSelectElement::class,
                ),
            );
        }

        $name = $element->getName();

        if ($name === null || $name === '') {
            throw new DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__,
                ),
            );
        }

        $pattern = $this->parsePattern($element->shouldRenderDelimiters());

        // The pattern always contains "day" part and the first separator, so we have to remove it
        unset($pattern['day'], $pattern[0]);

        $monthsOptions = $this->getMonthsOptions($pattern['month']);
        $yearOptions   = $this->getYearsOptions($element->getMinYear(), $element->getMaxYear());

        $monthElement = $element->getMonthElement()->setValueOptions($monthsOptions);
        $yearElement  = $element->getYearElement()->setValueOptions($yearOptions);

        if ($element->shouldCreateEmptyOption()) {
            $monthElement->setEmptyOption('');
            $yearElement->setEmptyOption('');
        }

        $indent = $this->getIndent();
        $this->selectHelper->setIndent($indent);

        $data                    = [];
        $data[$pattern['month']] = $this->selectHelper->render($monthElement);
        $data[$pattern['year']]  = $this->selectHelper->render($yearElement);

        $markups = [];

        foreach ($pattern as $key => $value) {
            // Delimiter
            $markups[] = is_numeric($key) ? $indent . $value : $data[$value];
        }

        return $indent . PHP_EOL . implode(PHP_EOL, $markups) . PHP_EOL . $indent;
    }
}
