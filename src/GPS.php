<?php
/**
 * (c) CupcakePHP: The Rapid and Tasty Development Framework.
 *
 * PHP version 5.5.12
 *
 * @author    Ge Bender <gesianbender@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   GIT: <git_id>
 * @link      http://cupcake.simplesys.com.br
 */
namespace Cupcake;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class GPS
{

    public $route;

    public $config;

    public $url;

    public $fs;


    public function __construct($route, $config, $url)
    {
        $this->route = $route;
        $this->config = $config;
        $this->url = explode('/', $url);
        $this->fs = new Filesystem();

    }


    public function route()
    {
        $this->defineAppName();
        $this->defineController();
        $this->defineAction();
        $this->defineArguments();
        $this->defineView();

        return $this->route;

    }


    public function config()
    {
    	return $this->config;

    }


    /**
     * Localiza o Aplicativo do cliente
     */
    public function defineAppName()
    {
        if ($this->fs->exists($this->route['appsFolder'] . DS . ucfirst($this->urlToCamel($this->url[0]))) === true && $this->url[0] !== '') {

            $this->route['appName'] = ucfirst($this->urlToCamel($this->url[0]));
            $this->mergeConfigs();
            $this->shiftUrl();
        }

    }


    public function mergeConfigs()
    {
        if ($this->fs->exists($this->route['appsFolder'] . DS . $this->route['appName'] . DS . 'Config.php') === true) {
            $path = 'CupcakeApp\\' . $this->route['appsFolder'] . '\\' . ucfirst($this->urlToCamel($this->url[0])) . '\Config';

            $configApp = $path::load();
            $this->config = array_merge($this->config, (isset($configApp['default'])) ? $configApp['default'] : array(), (isset($configApp[getenv('AMBIENT')]) === true) ? $configApp[getenv('AMBIENT')] : array());
            $this->route = array_merge($this->route, (isset($configApp['route']) === true) ? $configApp['route'] : array());
        }

    }


    /**
     * Tira a primeira parte da url
     */
    public function shiftUrl()
    {
        unset($this->url[0]);
        $this->url = array_values($this->url);
        if (count($this->url) === 0) {
            $this->url[0] = '';
        }

    }


    /**
     * Localiza o Controller pela URL
     */
    public function defineController()
    {
    	if ($this->fs->exists($this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['controllerFolder'] . DS . ucfirst($this->urlToCamel($this->url[0])) . '.php') === true) {
    		$this->route['controller'] = ucfirst($this->urlToCamel($this->url[0]));
            $this->shiftUrl();
        }

    }


    /**
     * Localiza o Action pela URL
     */
    public function defineAction()
    {
    	if (method_exists('CupcakeApp\\' . $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['controllerFolder'] . '\\' . $this->route['controller'], $this->urlToCamel($this->url[0])) === true) {
			$this->route['action'] = $this->urlToCamel($this->url[0]);
			$this->shiftUrl();
         }

    }


    /**
     * Define os argumentos
     */
    public function defineArguments()
    {
        $this->route['arguments'] = $this->url;

    }


    /**
     * Define as variáveis de view
     */
    public function defineView()
    {
		$this->route['view'] = $this->route['controller'] . DS . $this->route['action'] . '.' . $this->route['extensionView'];

    }


    public function getControllerPath()
    {
        return 'CupcakeApp\\' . $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['controllerFolder'] . '\\' . $this->route['controller'];
    }


    public function addIncludePaths()
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['layoutFolder'] . PATH_SEPARATOR . dirname(__FILE__). DS . 'Layout');
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['viewFolder'] . PATH_SEPARATOR . dirname(__FILE__). DS . 'View');

    }


    public function fileExists($filename)
    {
        $paths = explode(';', get_include_path());
        foreach ($paths as $path) {
            if (file_exists($path . DS . $filename) === true) {
                return true;
            }
        }

        return false;

    }


    public function getLayoutClassName($layout)
    {
        if (class_exists('CupcakeApp\\' . $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['layoutFolder'] . '\\' . $layout) === true) {
            return 'CupcakeApp\\' . $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['layoutFolder'] . '\\' . $layout;
        } elseif (class_exists('CupcakeApp\\' . $this->route['layoutFolder'] . '\\' . $layout) === true) {
            return 'CupcakeApp\\' . $this->route['layoutFolder'] . '\\' . $layout;
        } else {
            return false;
        }

    }


    public function getComponentClassName($component)
    {
        if (class_exists('CupcakeApp\\' . $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['layoutFolder'] . '\\' . $this->route['componentFolder'] . '\\' . $component) === true) {
            return 'CupcakeApp\\' . $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['layoutFolder'] . '\\' . $this->route['componentFolder'] . '\\' . $component;
        } elseif (class_exists('CupcakeApp\\' . $this->route['layoutFolder'] . '\\' . $this->route['componentFolder'] . '\\' . $component) === true) {
            return 'CupcakeApp\\' . $this->route['layoutFolder'] . '\\' . $this->route['componentFolder'] . '\\' . $component;
        } else {
            return false;
        }


    }


    public function getComponentViewFile($component)
    {
        if ($this->fs->exists($this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['viewFolder'] . '\\' . $this->route['componentFolder'] . '\\' . lcfirst($component) . '.' . $this->route['extensionView']) === true) {
            return $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['viewFolder'] . '\\' . $this->route['componentFolder'] . '\\' . lcfirst($component) . '.' . $this->route['extensionView'];
        } elseif ($this->fs->exists($this->route['viewFolder'] . '\\' . $this->route['componentFolder'] . '\\' . lcfirst($component) . '.' . $this->route['extensionView']) === true) {
            return $this->route['viewFolder'] . '\\' . $this->route['componentFolder'] . '\\' . lcfirst($component) . '.' . $this->route['extensionView'];
        } else {
            return false;
        }


    }


    public function getLayoutViewFile()
    {
        if ($this->fs->exists($this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['viewFolder'] . '\\' . $this->route['layout'] . '.' . $this->route['extensionView']) === true) {
            return $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['viewFolder'] . '\\' . $this->route['layout'] . '.' . $this->route['extensionView'];
        } elseif ($this->fs->exists($this->route['viewFolder'] . '\\' . $this->route['layout'] . '.' . $this->route['extensionView']) === true) {
            return $this->route['viewFolder'] . '\\' . $this->route['layout'] . '.' . $this->route['extensionView'];
        } else {
            return false;
        }


    }


    /**
     * Formata variavel vinda da url para camelCase
     * @param unknown $str
     */
    public function urlToCamel($str)
    {
        return str_replace(' ', '', lcfirst(ucwords(str_replace('-', ' ', $str))));

    }


}

?>