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

namespace Mimmi20Test\Mezzio\BootstrapForm\LaminasView\View\Helper\Compare;

use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Factory;
use Laminas\View\HelperPluginManager;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormDateSelect;
use Mimmi20\Mezzio\BootstrapForm\LaminasView\View\Helper\FormSelectInterface;
use PHPUnit\Framework\Exception;
use Psr\Container\ContainerExceptionInterface;

use function assert;
use function trim;

final class FormDateSelectTest extends AbstractTestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws ContainerExceptionInterface
     */
    public function testRender(): void
    {
        $form = (new Factory())->createForm(require '_files/config/date-select.config.php');

        $expected = $this->getExpected('form/date-select.html');

        $plugin = $this->serviceManager->get(HelperPluginManager::class);

        assert($plugin instanceof HelperPluginManager);

        $select = $plugin->get(FormSelectInterface::class);

        assert($select instanceof FormSelectInterface);

        $helper = new FormDateSelect($select);

        self::assertSame($expected, trim($helper->render($form->get('inputDate4'))));
    }
}
