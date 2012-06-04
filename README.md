# Interactive shell for CakePHP #

This plugin provides a an easy way to run an interactive shell for CakePHP, allowing you to instantiate any class such as models and
watch the produced output in your console. Ths makes a great tool for development and quick debugging any function.

## Requirements ##

* `phpsh` command line (https://github.com/facebook/phpsh) (Optional but highly recommended)
* PHP command line tool (cli) compiled with interactive support and readline
* CakePHP 2.x

## Installation ##

For an optimal experience you need to install [Facebook's phpsh](https://github.com/facebook/phpsh) otherwise this plugin can only be used
by native php's command line, which does not suppport autocompletion or fatal error recovery. Follow steps on README for the aforementioned
project and then continue with the following steps for using this plugin

_[Manual]_

* Download this: [https://github.com/nodesagency/cake-interactive-shell/zipball/master](https://github.com/nodesagency/cake-interactive-shell/zipball/master)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `Interactive`

_[GIT Submodule]_

In your app directory type:

	git submodule add git://github.com/nodesagency/cake-interactive-shell.git Plugin/Interactive
	git submodule init
	git submodule update

_[GIT Clone]_

In your plugin directory type

	git clone git://github.com/nodesagency/cake-interactive-shell.git Plugin/Interactive

### Enable plugin

In 2.0 you need to enable the plugin your `app/Config/bootstrap.php` file:

    CakePlugin::load('Interactive');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

### Intall command line tools in your app's Console folder

Now you need to execute the install shell to copy necessary files into your `Console` folder

	# Console/cake Interactive.Install

## Usage

Once you have intalled required files using the Install shell, you just need to execute the CakePHP interactive shell

	# Console/cpi

If you correctly installed `phpsh` then you will be able to use autocompletion and get automatically documentation for all php functions
and your application classes as well. To use it just write php sentences followed by a semicolon. If the stememnt echoes by default you
should see the output immediately, otherwise you can use functions such as `debug`, `pr`, `var_dump` etc. to inspect the contents of a function
return value or variable.

Autocompletion is triggered by writing par of the name for a class and then hitting tab a couple times

![Autocompletion](http://f.cl.ly/items/0g3F240a3p152R0D1b2r/Screen%20Shot%202012-06-04%20at%209.51.07%20AM.png)