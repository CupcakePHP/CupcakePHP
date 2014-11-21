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

        $this->app['Vars'] = new Vars();
        $this->app['config'] = function () { return Config::load(); };
        $this->app['route'] = function () { return Config::route(); };
        $this->app['Request'] = function () { return new Request(); };

        $app = $this->app;
        $app->match('{url}', function(Request $request) use ($app) {
            $app['GPS'] = new GPS($app['route'], $app['config'], substr($request->getPathInfo(), 1));
            $app['route'] = $app['GPS']->route();
            $app['config'] = $app['GPS']->config();

            $app['request'] = $request;

            $controllerPath = $app['GPS']->getControllerPath();
            $controller = new $controllerPath($app);
            $action = $app['route']['action'];

            return $controller->render($controller->$action());

        })->assert('url', '.+|');

        $app->run();
    }


}

?>