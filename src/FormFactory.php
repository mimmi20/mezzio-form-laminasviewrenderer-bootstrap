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

use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function get_debug_type;
use function sprintf;

final class FormFactory
{
    /** @throws ContainerExceptionInterface */
    public function __invoke(ContainerInterface $container): Form
    {
        $plugin = $container->get(HelperPluginManager::class);
        assert(
            $plugin instanceof HelperPluginManager,
            sprintf(
                '$plugin should be an Instance of %s, but was %s',
                HelperPluginManager::class,
                get_debug_type($plugin),
            ),
        );

        $collection = $plugin->get(FormCollectionInterface::class);
        $row        = $plugin->get(FormRowInterface::class);

        assert($collection instanceof FormCollectionInterface);
        assert($row instanceof FormRowInterface);

        return new Form($collection, $row);
    }
}
