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

use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Platform. This class tries to retrieve as much platform-identifying data as possible. It makes this information
 * available via function APIs.
 *
 * Heavily inspired by the `platform.py` module, maintained by Marc-Andre Lemburg <mal@egenix.com>. Source code
 * available at {@link https://hg.python.org/cpython/file/3.3/Lib/platform.py}.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class Platform
{
    /**
     * @const Directory to search for configuration information on Unix.
     * Constant used by testPlatform() to test linuxDistribution().
     */
    const UNIXCONFDIR = '/etc';

    /**
     * @var array
     *
     * See:
     * - {@link http://www.novell.com/coolsolutions/feature/11251.html}
     * - {@link http://linuxmafia.com/faq/Admin/release-files.html}
     * - {@link http://data.linux-ntfs.org/rpm/whichrpm}
     * - {@link http://www.die.net/doc/linux/man/man1/lsb_release.1.html}
     */
    protected $supportedDists = array('SuSE', 'debian', 'fedora', 'redhat', 'centos', 'mandrake', 'mandriva', 'rocks',
        'slackware', 'yellowdog', 'gentoo', 'UnitedLinux', 'turbolinux', 'arch', 'mageia');

    /**
     * Queries the given executable (defaults to the PHP interpreter binary) for various architecture information.
     *
     * Returns a tuple (bits,linkage) which contains information about the bit architecture and the linkage format used
     * for the executable. Both values are returned as strings.
     *
     * Values that cannot be determined are returned as given by the parameter presets. If bits is given as NULL, the
     * sizeof(pointer) is used as indicator for the supported pointer size.
     *
     * The function relies on the system's "file" command to do the actual work. This is available on most if not all
     * Unix platforms. On some non-Unix platforms where the "file" command does not exist and the executable is set to
     * the PHP interpreter binary defaults from _default_architecture are used.
     *
     * @param string|null $executable
     * @param null        $bits
     * @param null        $linkage
     *
     * @return array
     */
    public function getArchitecture($executable = null, $bits = null, $linkage = null)
    {
        // Use the sizeof(pointer) as default number of bits if nothing else is given as default.
        if (null === $bits) {
            $bits = (string) (8 * PHP_INT_SIZE) . 'bit';
        }

        if (null === $executable) {
            $phpFinder = new PhpExecutableFinder();
            if (!$executable = $phpFinder->find()) {
                throw new \RuntimeException('The php executable could not be found, please add it to your PATH environment variable and try again');
            }
        }

        // TODO
        return array('bits' => $bits, 'linkage' => $linkage);
    }

    /**
     * Fairly portable uname interface. Returns an associative array of strings (system, node, release, version,
     * machine, processor) identifying the underlying platform. Entries which cannot be determined are set to ''.
     *
     * @return array
     */
    public function getUname()
    {
        return array(
            'system'    => php_uname('s'),
            'node'      => php_uname('n'),
            'release'   => php_uname('r'),
            'version'   => php_uname('v'),
            'machine'   => php_uname('m'),
            'processor' => $this->getProcessor()
        );
    }

    /**
     * Returns the system/OS name, e.g. 'Linux', 'Windows' or 'Java'. An empty string is returned if the value cannot be
     * determined.
     *
     * @note php_uname('s') is more reliable than the PHP_OS constant because the later contains the operating system
     * PHP was built on, which may be different that the current system.
     *
     * @return string
     */
    public function getSystem()
    {
        return php_uname('s');
    }

    /**
     * Returns the computer's network name (which may not be fully qualified). An empty string is returned if the value
     * cannot be determined.
     *
     * @return string
     */
    public function getNode()
    {
        return php_uname('n');
    }

    /**
     * Returns the system's release, e.g. '2.2.0' or 'NT'
     *
     * An empty string is returned if the value cannot be determined.
     *
     * @return string
     */
    public function getRelease()
    {
        return php_uname('r');
    }

    /**
     * Returns the system's release version, e.g. '#3 on degas'
     *
     * An empty string is returned if the value cannot be determined.
     *
     * @return string
     */
    public function getVersion()
    {
        return php_uname('v');
    }

    /**
     * Returns the machine type, e.g. 'i386'
     *
     * An empty string is returned if the value cannot be determined.
     *
     * @return string
     */
    public function getMachine()
    {
        return php_uname('m');
    }

    /**
     * Returns the (true) processor name, e.g. 'amdk6'
    *
     * An empty string is returned if the value cannot be determined. Note that many platforms do not provide this
     * information or simply return the same value as for machine(), e.g.  NetBSD does this.
     *
     * @return string
     */
    public function getProcessor()
    {
        // TODO
        return '';
    }

    /**
     * Tries to determine the name of the Linux OS distribution name.
     *
     * The function first looks for a distribution release file in /etc and then reverts to _distTryHarder() in case
     * no suitable files are found.
     *
     * $supportedDists may be given to define the set of Linux distributions to look for. It defaults to a list of
     * currently supported Linux distributions identified by their release file name.
     *
     * If $fullDistributionName is true (default), the full distribution read from the OS is returned. Otherwise the
     * short name taken from $supportedDists is used.
     *
     * Returns an associative array ("distname", "version", "id") which defaults to the args given as parameters.
     *
     * @param string $distname
     * @param string $version
     * @param string $id
     * @param null   $supportedDists
     * @param bool   $fullDistributionName
     *
     * @return array
     */
    public function getLinuxDistribution($distname = '', $version = '', $id = '', $supportedDists = null,
        $fullDistributionName = true)
    {
        if (!is_dir(self::UNIXCONFDIR)) {
            // Probably not a Unix system
            return array('distname' => $distname, 'version' => $version, 'id' => $id);
        }

        if (null === $supportedDists) {
            $supportedDists = $this->supportedDists;
        }

        // Lookup /etc for *-release or *-version files
        $file = null;
        foreach (scandir(self::UNIXCONFDIR) as $file) {
            preg_match('/(\w+)(-|_)(release|version)$/', $file, $matches);
            if (isset($matches[1]) && in_array($matches[1], $supportedDists)) {
                $distname = $matches[1];
                break;
            }
        }

        if (!$file || !$distname) {
            return $this->_distTryHarder($distname, $version, $id);
        }

        // Read the first line
        $handle = fopen(self::UNIXCONFDIR . '/'. $file, 'r');
        $line   = fgets($handle);
        fclose($handle);

        $fromReleaseFile = $this->_parseReleaseFile($line);

        if (isset($fromReleaseFile['distname']) && $fromReleaseFile['distname'] && $fullDistributionName) {
            $distname = $fromReleaseFile['distname'];
        }
        if (isset($fromReleaseFile['version']) && $fromReleaseFile['version']) {
            $version = $fromReleaseFile['version'];
        }
        if (isset($fromReleaseFile) && $fromReleaseFile['id']) {
            $id = $fromReleaseFile['id'];
        }

        return array('distname' => $distname, 'version' => $version, 'id' => $id);
    }

    /**
     * System name aliasing.
     *
     * Returns (system,release,version) aliased to common marketing names used for some systems.
     *
     * It also does some reordering of the information in some cases where it would otherwise cause confusion.
     *
     * @param string $system
     * @param string $release
     * @param string $version
     *
     * @return array()
     */
    public function getSystemAlias($system, $release, $version)
    {
        if ($system == 'Rhapsody') {
            // Apple's BSD derivative
            // XXX How can we determine the marketing release number?
            return array('system' => 'MacOS X Server', 'release' => $system . $release, 'version' => $version);

        } elseif ($system == 'SunOS') {
            // Sun's OS
            if ($release < '5') {
                // These releases use the old name SunOS
                return array('system' => $system, 'release' => $release, 'version' => $version);
            }

            // Modify release (marketing release = SunOS release - 3)
            $l = explode('.', $release);
            if ($l) {
                $major = (int)$l[0] - 3;
                $l[0] = (string)$major;
                $release = implode('.', $l);
                if ($release < '6') {
                    $system = 'Solaris';
                } else {
                    // XXX Whatever the new SunOS marketing name is...
                    $system = 'Solaris';
                }
            }

        } elseif ($system == 'IRIX64') {
            // IRIX reports IRIX64 on platforms with 64-bit support; yet it is really a version and not a different
            // platform, since 32-bit apps are also supported.
            $system = 'IRIX';
            if ($version) {
                $version = $version . ' (64bit)';
            } else {
                $version = '64bit';
            }
        }

        elseif (in_array($system, array('win32', 'win16'))) {
            // In case one of the other tricks
            $system = 'Windows';
        }

        return array('system' => $system, 'release' => $release, 'version' => $version);
    }

    /**
     * Allows Python-style method naming. Platform::version() gets converted into Platform::getVersion().
     *
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, $arguments)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }

        throw new \BadMethodCallException(sprintf('Method "%s::%s()" does not exist.', __CLASS__, $method));
    }

    /**
     * Tries some special tricks to get the distribution information in case the default method fails.
     *
     * Currently supports older SuSE Linux, Caldera OpenLinux and Slackware Linux distributions.
     *
     * @param $distname
     * @param $version
     * @param $id
     *
     * @return array
     */
    protected function _distTryHarder($distname, $version, $id)
    {
        if (file_exists('/var/adm/inst-log/info')) {
            // SuSE Linux stores distribution information in that file
            $distname = 'SuSE';
            $handle   = fopen('/var/adm/inst-log/info', 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $tv = preg_split('/\s+/', $line);
                    if (count($tv) == 2) {
                        $tag = $tv[0];
                        $value = $tv[1];
                    } else {
                        continue;
                    }

                    if ('MIN_DIST_VERSION' == $tag) {
                        $version = trim($value);
                    } elseif ('DIST_IDENT' == $tag) {
                        $values = explode('-', $value);
                        $id     = $values[2];
                    }
                }

                fclose($handle);

                return array('distname' => $distname, 'version' => $version, 'id' => $id);
            }
        }

        if (file_exists('/etc/.installed')) {
            // Caldera OpenLinux has some infos in that file (thanks to Colin Kong)
            $handle   = fopen('/etc/.installed', 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $pkg = explode('-', $line);
                    if (count($pkg) >= 2 && $pkg[0] == 'OpenLinux') {
                        // XXX does Caldera support non Intel platforms? If yes, where can we find the needed id?
                        fclose($handle);

                        return array('distname' => 'OpenLinux', 'version' => $pkg[1], 'id' => $id);
                    }
                }

                fclose($handle);
            }
        }

        if (is_dir('/usr/lib/setup')) {
            // Check for slackware version tag file (thanks to Greg Andruk)
            $verfiles = scandir('/usr/lib/setup');
            for ($n = count($verfiles) - 1; $n >= 0; $n--) {
                if (!isset($verfiles[$n][14]) || $verfiles[$n][14] != 'slack-version-') {
                    unset($verfiles[$n]);
                }
            }

            if ($verfiles) {
                sort($verfiles);
                $distname = 'slackware';
                $last     = end($varfiles);
                $version  = isset($last[14]) ? $last[14] : $version;

            }

            return array('distname' => $distname, 'version' => $version, 'id' => $id);
        }

        return array('distname' => $distname, 'version' => $version, 'id' => $id);
    }

    /**
     * Default to empty 'version' and 'id' strings. Both defaults are used when 'line' is empty.  'id' defaults to
     * empty when an id can not be deduced.
     *
     * @param string $line
     */
    protected function _parseReleaseFile($line)
    {
        $version = '';
        $id      = '';

        // TODO
    }
}
