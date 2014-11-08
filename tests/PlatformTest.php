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

namespace NoiseLabs\Platform;

class PlatformTest extends \PHPUnit_Framework_TestCase
{
    public function createPlatform()
    {
        return new Platform();
    }

    public function testGetArchitecture()
    {
        $architecture = $this->createPlatform()->getArchitecture();

        foreach (array('bits', 'linkage') as $key) {
            $this->assertArrayHasKey($key, $architecture);
        }
    }

    public function testGetUname()
    {
        $uname = $this->createPlatform()->getUname();

        foreach (array('system', 'node', 'release', 'version', 'machine', 'processor') as $key) {
            $this->assertArrayHasKey($key, $uname);
        }
    }

    public function testGetSystemAlias()
    {

        $alias = $this->createPlatform()->getSystemAlias('Rhapsody', 'Mac OS X Server 1.0', '');
        foreach (array('system', 'release', 'version') as $key) {
            $this->assertArrayHasKey($key, $alias);
        }
    }

    public function testGetPhpVersion()
    {
        $this->assertInternalType('string', $this->createPlatform()->getPhpVersion());
    }

    public function testGetPhpVersionArray()
    {
        $phpVersion = $this->createPlatform()->getPhpVersionArray();
        foreach (array('major', 'minor', 'patchlevel') as $key) {
            $this->assertArrayHasKey($key, $phpVersion);
        }
    }

    public function testGetPhpBranch()
    {
        $this->assertInternalType('string', $this->createPlatform()->getPhpBranch());
    }

    public function testGetPhpRevision()
    {
        $this->assertInternalType('string', $this->createPlatform()->getPhpRevision());
    }

    public function testGetPhpBuild()
    {
        $phpBuild = $this->createPlatform()->getPhpBuild();
        foreach (array('buildno', 'builddate') as $key) {
            $this->assertArrayHasKey($key, $phpBuild);
        }
    }

    public function testGetPhpCompiler()
    {
        $this->assertInternalType('string', $this->createPlatform()->getPhpCompiler());
    }
}
