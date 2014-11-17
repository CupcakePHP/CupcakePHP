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

/**
 * Cupcake is a autoRouter and autoRender microFramework based on the Symfony2 Components.
 * Controllers, actions and views automatic executed by the url path.
 */
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

@header('Content-Type: text/html; charset=utf-8');
define('DS', DIRECTORY_SEPARATOR);

class App
{

    public $app;

    public function __construct()
    {
        $this->app = new Application();

        if (getenv('AMBIENT') === false) {
            putenv('AMBIENT=development');
            $this->app['debug'] = true;
        }

        $this->app['Vars'] = $this->app->share(function () { return new Vars(); });
        $this->app['config'] = function () { return Config::load(); };
        $this->app['route'] = function () { return Config::route(); };
        $this->app['Request'] = function () { return new Request(); };

        $app = $this->app;
        $app->match('{url}', function(Request $request) use ($app) {
            $app['GPS'] = function () use ($app, $request) { return new GPS($app['route'], $app['config'], substr($request->getPathInfo(), 1)); };
            $route = $app['GPS']->route();
            $config = $app['GPS']->config();

            return $this->run($app, $request, $route, $config);

        })->assert('url', '.+|');

        $app->run();
    }


    public function run($app, $request, $route, $config)
    {
        $controllerPath = 'CupcakeApp\\' . $route['appsFolder'] . '\\' . $route['appName'] . '\\' . $route['controllerFolder'] . '\\' . $route['controller'];
        $controller = new $controllerPath($app, $request);
        $controller->setConfig($config);
        $controller->setRoute($route);

        $action = $route['action'];
        $cliRender = $controller->$action();

        return $this->render($cliRender, $controller->app['Vars']->vars);

    }

    public function render($cliRender, $vars)
	{
	    if($cliRender !== null) {
	        return $cliRender;
	    }
	    else {
	        set_include_path(get_include_path().PATH_SEPARATOR.'Apps\Syscacambas\View');

	        ob_start();
    	    extract($vars);
	        require $vars['route']['view'];
	        return ob_get_clean();

	    }

	}


}

?>