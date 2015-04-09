<?php
/**
 * (c) CupcakePHP: The Rapid and Tasty Development Framework.
 *
 * PHP version 5.5.12
 *
 * @author  Ge Bender <gesianbender@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version GIT: <git_id>
 * @link    http://cupcake.simplesys.com.br
 */
namespace Cupcake;

class GPS
{

    public $route;

    public $config;

    public $url;

    public $fs;


    /**
     * Prepara o GPS
     *
     * @param array $route
     * @param array $config
     * @param string $url
     *
     * @return void
     */
    public function __construct($route, $config, $url)
    {
        $this->route = $route;
        $this->config = $config;
        $this->url = explode('/', $url);
        $this->fs = new Filesystem();

    }


    /**
     * Define o roteamento a ser seguido pela aplicação
     *
     * @return array
     */
    public function route()
    {
        $this->defineAppName();
        $this->defineEntity();
        $this->defineController();
        $this->defineAction();
        $this->defineArguments();
        $this->defineView();

        return $this->route;

    }


    /**
     * Acessa as variáveis de configuração
     *
     * @return array
     */
    public function config()
    {
        return $this->config;

    }


    /**
     * Define o Aplicativo do cliente
     *
     * @return void
     */
    public function defineAppName()
    {
        if ($this->fs->exists(dirname(dirname(__FILE__)) . DS . $this->route['appsFolder'] . DS . ucfirst($this->urlToCamel($this->url[0]))) === true && $this->url[0] !== '') {
            $this->route['appName'] = ucfirst($this->urlToCamel($this->url[0]));
            $this->mergeConfigs();
            $this->shiftUrl();
        }

    }


    /**
     * Une o config global com o da aplicação
     *
     * @return void
     */
    public function mergeConfigs()
    {
        if ($this->fs->exists(dirname(dirname(__FILE__)) . DS . $this->route['appsFolder'] . DS . $this->route['appName'] . DS . 'Config.php') === true) {
            $path = $this->route['appsFolder'] . '\\' . ucfirst($this->urlToCamel($this->url[0])) . '\Config';

            $configApp = $path::load();
            $this->config = array_merge($this->config, (isset($configApp['default']) === true) ? $configApp['default'] : array(), (isset($configApp[getenv('AMBIENT')]) === true) ? $configApp[getenv('AMBIENT')] : array());
            $this->route = array_merge($this->route, (isset($configApp['route']) === true) ? $configApp['route'] : array());
        }

    }


    /**
     * Tira a primeira parte da url
     *
     * @return void
     */
    public function shiftUrl()
    {
        unset($this->url[0]);
        $this->url = array_values($this->url);
        if (count($this->url) === 0) {
            $this->url[0] = '';
        }

    }


    public function defineEntity()
    {
        $this->route['entity'] = ucfirst($this->urlToCamel($this->url[0]));

    }

    /**
     * Define o Controller pela URL
     *
     * @return void
     */
    public function defineController()
    {
        if ($this->fs->exists(dirname(dirname(__FILE__)) . DS . $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['controllerFolder'] . DS . ucfirst($this->urlToCamel($this->url[0])) . 'Controller.php') === true || $this->fs->exists(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DS . $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['controllerFolder'] . DS . ucfirst($this->urlToCamel($this->url[0])) . 'Controller.php') === true) {
            $this->route['controller'] = $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['controllerFolder'] . '\\' . ucfirst($this->urlToCamel($this->url[0])) . 'Controller';
        }
        $this->shiftUrl();

    }


    /**
     * Define o Action pela URL
     *
     * @return void
     */
    public function defineAction()
    {
        if ($this->fs->methodExists($this->route['controller'], $this->urlToCamel($this->url[0])) === true) {
            $this->route['action'] = $this->urlToCamel($this->url[0]);
            $this->shiftUrl();
        }

    }


    /**
     * Define os argumentos
     *
     * @return void
     */
    public function defineArguments()
    {
        $this->route['arguments'] = $this->url;

    }


    /**
     * Define as variáveis de view
     *
     * @return void
     */
    public function defineView()
    {
        $this->route['view'] = $this->route['entity'] . DS . lcfirst($this->route['entity']).ucfirst($this->route['action']) . '.' . $this->route['extensionView'];

    }


    /**
     * Retorna o nome da classe de Layout
     *
     * @param string $layout
     *
     * @return string|boolean
     */
    public function getLayoutClassName($layout)
    {
        if ($this->fs->classExists($this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['layoutFolder'] . '\\' . $layout) === true) {
            return $this->route['appsFolder'] . '\\' . $this->route['appName'] . '\\' . $this->route['layoutFolder'] . '\\' . $layout;
        } else if ($this->fs->classExists('Layout\\' . $layout) === true) {
            return 'Layout\\' . $layout;
        }

    }


    /**
     * Retorna o nome da classe de um componente
     *
     * @param string $component
     *
     * @return string|boolean
     */
    public function getComponentClassName($component)
    {
        if ($this->fs->classExists('Apps\\' . $this->route['appName'] . '\\' . $this->route['componentFolder'] . '\\' . $component) === true) {
            return 'Apps\\' . $this->route['appName'] . '\\' . $this->route['componentFolder'] . '\\' . $component;
        } else if ($this->fs->classExists('Layout\\' . $this->route['componentFolder'] . '\\' . $component) === true) {
            return 'Layout\\' . $this->route['componentFolder'] . '\\' . $component;
        } else {
            return false;
        }

    }


    /**
     * Retorna o nome do arquivo de um componente
     *
     * @param string $component
     *
     * @return string|boolean
     */
    public function getComponentViewFile($component)
    {
        if ($this->fs->exists(dirname(dirname(__FILE__)) . DS . $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['viewFolder'] . DS . $this->route['componentFolder'] . DS . lcfirst($component) . '.' . $this->route['extensionView']) === true) {
            return $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['viewFolder'] . DS . $this->route['componentFolder'] . DS . lcfirst($component) . '.' . $this->route['extensionView'];
        } else if ($this->fs->exists(dirname(dirname(__FILE__)) . DS . $this->route['viewFolder'] . DS . $this->route['componentFolder'] . DS . lcfirst($component) . '.' . $this->route['extensionView']) === true) {
            return $this->route['viewFolder'] . DS . $this->route['componentFolder'] . DS . lcfirst($component) . '.' . $this->route['extensionView'];
        } else {
            return false;
        }

    }


    /**
     * Retorna o nome do arquivo do layout
     *
     * @return string|boolean
     */
    public function getLayoutViewFile()
    {
        if ($this->fs->exists(dirname(dirname(__FILE__)) . DS . $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['viewFolder'] . DS . $this->route['layout'] . '.' . $this->route['extensionView']) === true) {
            return $this->route['appsFolder'] . DS . $this->route['appName'] . DS . $this->route['viewFolder'] . DS . $this->route['layout'] . '.' . $this->route['extensionView'];
        } else if ($this->fs->exists(dirname(dirname(__FILE__)) . DS . $this->route['viewFolder'] . DS . $this->route['layout'] . '.' . $this->route['extensionView']) === true) {
            return $this->route['viewFolder'] . DS . $this->route['layout'] . '.' . $this->route['extensionView'];
        } else {
            return false;
        }

    }


    /**
     * Formata variavel vinda da url para camelCase
     *
     * @param string $str
     *
     * @return string
     */
    public function urlToCamel($str)
    {
        return str_replace(' ', '', lcfirst(ucwords(str_replace('-', ' ', $str))));

    }


}
