<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Configs\Yamls;

use Symfony\Component\Yaml\Yaml;

trait YamlDump
{
	/**
	 * Global flags configuration for YAML dumping.
	 * @var int
	 */
	protected static $dumpingFlags = 0;

	/**
	 * Set globally MvcCore YAML Config dumping flags.
	 * @param int $dumpingFlags 
	 * @return int
	 */
	public static function SetDumpingFlags ($dumpingFlags) {
		return self::$dumpingFlags = $dumpingFlags;
	}

	/**
	 * Get globally configured MvcCore YAML Config dumping flags.
	 * @return int
	 */
	public static function GetDumpingFlags () {
		return self::$dumpingFlags;
	}

	/**
	 * Dump all configuration data in YAML format. There will not be included
	 * any other data for different environment. You need to save different 
	 * configuration file separately. Return string with dumped XAML config
	 * or boolean `FALSE` of there was an error about strange data to encode.
	 * @return \bool|string
	 */
	public function Dump () {
		$dataClone = unserialize(serialize($this->data)); // clone
		$maxLevel = 0;
		$dataClone = $this->dumpYamlObjectTypes($dataClone, $maxLevel);
		$result = Yaml::dump($dataClone, $maxLevel, 4, self::$dumpingFlags);
		if (!$result) $result = FALSE;
		return $result;
	}

	/**
	 * Before encode all data into YAML format, convert all `\stdClass` objects
	 * recursively into arrays. YAML encoder could encode only array types.
	 * @param mixed $data 
	 * @param int $maxLevel 
	 * @param int $level 
	 * @return mixed
	 */
	protected function dumpYamlObjectTypes (& $data, & $maxLevel, $level = 0) {
		if (is_object($data)) 
			$data = (array) $data;
		if (is_array($data) || is_object($data)) {
			$level += 1;
			if ($level > $maxLevel) 
				$maxLevel = $level;
			foreach ($data as & $value) {
				if (is_object($value)) 
					$value = (array) $value;
				if (is_array($value)) 
					$value = $this->dumpYamlObjectTypes($value, $maxLevel, $level);
			}
		}
		return $data;
	}
}
