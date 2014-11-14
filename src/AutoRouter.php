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

class AutoRouter
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
        var_dump($this->route);
        var_dump($this->config);
        var_dump($this->url);
        // $this->defineAction();
        // $this->defineArguments();
        // $this->defineView();
    }


    /**
     * Localiza o Aplicativo do cliente
     */
    public function defineAppName()
    {
        if ($this->fs->exists($this->route['appsFolder'] . DS . ucfirst($this->urlToCamel($this->url[0]))) === true) {
            $this->route['appName'] = ucfirst($this->urlToCamel($this->url[0]));
            $this->uniConfigs();
            $this->tiraUmDaUrl();
        }

    }


    public function uniConfigs()
    {
        if ($this->fs->exists($this->route['appsFolder'] . DS . ucfirst($this->urlToCamel($this->url[0])) . DS . 'Config.php') === true) {
            $path = 'CupcakeApp\Apps\\' . ucfirst($this->urlToCamel($this->url[0])) . '\Config';

            $configApp = $path::load();
            $this->config = array_merge($this->config, (isset($configApp['default'])) ? $configApp['default'] : array(), (isset($configApp[getenv('AMBIENT')]) === true) ? $configApp[getenv('AMBIENT')] : array());
            $this->route = array_merge($this->route, (isset($configApp['route']) === true) ? $configApp['route'] : array());
        }

    }


    /**
     * Tira a primeira parte da url
     */
    public function tiraUmDaUrl()
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
        $this->controller = $this->cliApp;
        $appController = str_replace('Index', ucfirst($this->url[0]), $this->cliApp);
        if (class_exists($appController) === true) {
            $this->controller = $appController;
            $this->tiraUmDaUrl();
        }

    }


    /**
     * Localiza o Action pela URL
     */
    public function defineAction()
    {
        $this->action = $this->app['config']['actionPadrao'];
        if (method_exists($this->controller, $this->urlToCamel($this->url[0])) === true) {
            $this->action = $this->url[0];
            $this->tiraUmDaUrl();
        }

    }


    /**
     * Define os argumentos
     */
    public function defineArguments()
    {
        $this->arguments = $this->url;

    }


    /**
     * Define as variáveis de view
     */
    public function defineView()
    {
        $this->layout = $this->app['config']['layout'];
        $this->viewFolder = substr($this->controller, (strrpos($this->controller, '\\') + 1));
        $this->view = $this->action;

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