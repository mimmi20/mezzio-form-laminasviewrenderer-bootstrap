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

namespace MezzioTest\BootstrapForm\LaminasView\View\Helper;

use Laminas\Form\Element\DateSelect as DateSelectElement;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect;
use Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelectInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class FormDateSelectTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderWithWrongElement(): void
    {
        $selectHelper = $this->getMockBuilder(FormSelectInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormDateSelect($selectHelper);

        $element = $this->getMockBuilder(Text::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::never())
            ->method('getName');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element is of type Laminas\Form\Element\DateSelect',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect::render'
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function testRenderWithoutName(): void
    {
        $selectHelper = $this->getMockBuilder(FormSelectInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $selectHelper->expects(self::never())
            ->method('setIndent');
        $selectHelper->expects(self::never())
            ->method('render');

        $helper = new FormDateSelect($selectHelper);

        $element = $this->getMockBuilder(DateSelectElement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects(self::once())
            ->method('getName')
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s requires that the element has an assigned name; none discovered',
                'Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect::render'
            )
        );
        $this->expectExceptionCode(0);

        $helper->render($element);
    }
}
