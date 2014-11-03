#!/usr/bin/env php
<?php
/**
 * This file is part of NoiseLabs Platform.php.
 *
 * NoiseLabs Platform.php is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * NoiseLabs Platform.php is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with NoiseLabs Platform.php; if not, see
 * <http://www.gnu.org/licenses/>.
 *
 * Copyright (C) 2014 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     Platform
 * @copyright   (C) 2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://noiselabs.org
 */

function includeIfExists($file)
{
    if (file_exists($file)) {
        return include $file;
    }
}

if ((!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php')) && (!$loader = includeIfExists(__DIR__.'/../../autoload.php'))) {
    die('You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -sS https://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL);
}

use NoiseLabs\Platform\PlatformCommand;
use Symfony\Component\Console\Application;

$console = new Application('NoiseLabs Platform', '1.0');
$console->add(new PlatformCommand());
$console->run();