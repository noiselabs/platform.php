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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PlatformCommand.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class PlatformCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('noiselabs:platform')
            ->setDefinition(array())
            ->setDescription('Access to underlying platform\'s identifying data')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command prints the platform information concatenated as
single string to stdout. The output format is useable as part of a filename.
EOF
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $platform = new Platform();

        $output->writeln('   <comment>System:</comment> '.$platform->getSystem());
        $output->writeln('  <comment>Release:</comment> '.$platform->getRelease());
        $output->writeln('     <comment>Node:</comment> '.$platform->getNode());
        $output->writeln('  <comment>Version:</comment> '.$platform->getVersion());
        $output->writeln('  <comment>Machine:</comment> '.$platform->getMachine());
        $output->writeln('<comment>Processor:</comment> '.$platform->getProcessor());

        $platform->getPhpBuild();
    }
}
