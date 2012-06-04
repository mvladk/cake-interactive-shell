<?php

/**
 * Command-line classpath generation file
 *
 */
App::uses('AppShell', 'Console/Command');

class ClassPathShell extends AppShell {

/**
 * Paths to skip
 *
 * @var array
 */
	public $excluded = array(
		'Config',
		'Console',
		'Test',
		'TestSuite',
		'tmp',
	);

/**
 * Classes in existence
 *
 * @var array
 */
	public $classes = array();

/**
 * Files that have been been processed
 *
 * @var array
 */
	public $files = array();

/**
 * List of paths to be parsed
 *
 * @var array
 **/
	public $dirs = array(CAKE, APP);

/**
 * Override main() to handle action
 *
 * @return mixed
 */
	public function main() {
		foreach ($this->dirs as $dir) {
			$files = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)), '/\.php$/');
			foreach ($files as $item) {
				if ($item->isDir()) {
					continue;
				}

				foreach ($this->excluded as $package) {
					if (strpos($item->getRealPath(), APP . $package) === 0 || strpos($item->getRealPath(), CAKE . $package) === 0) {
						continue(2);
					}
					foreach (CakePlugin::loaded() as $plugin) {
						if (strpos($item->getRealPath(), CakePlugin::path($plugin) . $package) === 0) {
							continue(3);
						}
					}
				}

				$this->_includeFile($item->getRealPath());
			}
		}
		$this->_writeIncludes();
	}

/**
 * Adds an include for a file if a class exists within the file
 *
 * @param string $file path to file
 * @return void
 */
	protected function _includeFile($file) {
		if (!is_file($file)) {
			$this->out(sprintf('<info>Skipping Non-existent File:</info> %s', $file));
			return;
		}

		if (in_array($file, $this->files)) {
			$this->out(sprintf('<info>Skipping Included File:</info> %s', $file));
			return;
		}

		$this->files[] = $file;

		if (preg_match('/(\/\.[\w_-]+\/)/iS', $file)) {
			$this->out(sprintf('<info>Skipping Hidden File:</info> %s', $file));
			return;
		}

		$contents = file_get_contents($file);
		$tokens = token_get_all($contents);
		$count = count($tokens);
		for ($i = 2; $i < $count; $i++) {
			$isClass = in_array($tokens[$i - 2][0], array(T_CLASS, T_INTERFACE));
			if ($isClass && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
				$class = $tokens[$i][1];
				if (isset($this->classes[$class])) {
					$this->out(sprintf('<warning>Class Already Exists:</warning> %s already exists in %s, not overriding with %s',
						$tokens[$i][1], $this->classes[$class], $file
					));
				}
				$this->classes[$class] = $file;
			}
		}
	}

/**
 * Writes the includes to a file
 *
 * @return void
 */
	protected function _writeIncludes() {
		$this->_normalizePaths();
		$result = '';
		$classes = array_unique($this->classes);
		foreach ($classes as $file) {
			$result .= "include_once('$file');\n";
		}

		$this->_outfile($this->params['path'] . DS . 'includes.php',
			"<?php\n" . $result
		);
	}

/**
 * Normalizes class file paths for output
 *
 * @return void
 */
	protected function _normalizePaths() {
		if ($this->params['absolute']) {
			return false;
		}

		foreach ($this->classes as $class => $file) {
			if ($this->params['path'] == APP . 'Config') {
				$replace = array('../../lib/Cake/', '../');
			} else {
				if (strpos(CAKE, $this->params['path']) === 0) {
					$cakeEnd = rtrim(substr(CAKE, strlen($this->params['path'])), DS) . DS;
				} else {
					$cakeEnd = rtrim($this->_relativePath($this->params['path'], CAKE), DS) . DS;
				}

				if (strpos(APP, $this->params['path']) === 0) {
					$appEnd = rtrim(substr(APP, strlen($this->params['path'])), DS) . DS;
				} else {
					$appEnd = rtrim($this->_relativePath($this->params['path'], APP), DS) . DS;
				}
				$replace = array($cakeEnd, $appEnd);
			}

			$replace[] = $this->_relativePath($this->params['path'], $file);
			$this->classes[$class] = str_replace(
				array(CAKE, APP, $file),
				$replace,
				$file
			);
		}
	}

/**
 * Computes the relative path from one full-path to another
 *
 * @param string $from
 * @param string $to
 * @param string $ds directory separator
 * @return string relative path
 */
	function _relativePath($from, $to, $ds = DIRECTORY_SEPARATOR) {
		$arFrom = explode($ds, rtrim($from, $ds));
		$arTo = explode($ds, rtrim($to, $ds));
		while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
			array_shift($arFrom);
			array_shift($arTo);
		}

		return str_pad('', count($arFrom) * 3, '..' . $ds).implode($ds, $arTo);
	}

/**
 * Writes some contents to a path
 *
 * @param string $path full filepath
 * @param string $contents contents to write to file
 * @return void
 */
	protected function _outfile($path, $contents) {
		if ($this->params['dry']) {
			$this->out(sprintf('<info>Performing a dry run:</info> %s', $path));
			$this->hr();
			$this->out(sprintf("%s", $contents));
			$this->hr();
		} else {
			file_put_contents($path, $contents);
		}
	}

/**
 * Get and configure the Option parser
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(
			__d('class_path', 'Creates an include file with all classes inside a project.')
		)->addOptions(array(
			'path' => array(
				'help' => __('Output path for files'),
				'default' => APP . 'Config',
			),
			'absolute' => array(
				'help' => __('Use absolute paths'),
			    'boolean' => true
			),
			'dry' => array(
				'help' => __('Perform a dry-run'),
			    'boolean' => true
			)
		));
	}

}