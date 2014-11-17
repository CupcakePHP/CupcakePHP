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
        if ($this->fs->exists($this->route['appsFolder'] . DS . ucfirst($this->urlToCamel($this->url[0]))) === true) {
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