<?php
/**
 * Install interpreter files in your application folder
 *
 */
App::uses('AppShell', 'Console/Command');

class InstallShell extends AppShell {


/**
 * Executes the shell action
 *
 * @return void
 **/
	public function main() {
		if (empty($this->params['no-classpath'])) {
			$this->dispatchShell('Interpreter.ClassPath --absolute');
		}
		if (!is_dir(APP . 'Console' . DS)) {
			mkdir(APP . 'Console' . DS);
		}

		$installDir = dirname(__DIR__) . DS . 'install' . DS;
		copy($installDir . 'cpi', APP . 'Console' . DS . 'cpi');
		copy($installDir . 'cpi.php', APP . 'Console' . DS . 'cpi.php');
		chmod(APP . 'Console' . DS . 'cpi', 0755);
		chmod(APP . 'Console' . DS . 'cpi.php', 0755);
		$this->out('<success>Files installed successfully. Run interpreter with "Console/cpi" from you application folder</success>');
	}

/**
 * Get and configure the Option parser
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(
			__d('class_path', 'Install interpreter files in your application folder')
		)->addOptions(array(
			'no-classpath' => array(
				'help' => __('Do not generate includes file for easier interpreter class loading'),
			    'boolean' => true,
			),
		));
	}

}
