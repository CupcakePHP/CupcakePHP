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
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;

@header('Content-Type: text/html; charset=utf-8');
define('DS', DIRECTORY_SEPARATOR);

class App
{

    public function __construct()
    {
        $app = new Application();

        if (getenv('AMBIENT') === false) {
            putenv('AMBIENT=development');
            $app['debug'] = true;
        }

        $app['Vars'] = new Vars();
        $app['config'] = function () { return Config::load(); };
        $app['route'] = function () { return Config::route(); };
        $app['Request'] = function () { return new Request(); };
        $app->register(new FormServiceProvider());
        $app->register(new \Silex\Provider\ValidatorServiceProvider());
        $app->register(new \Silex\Provider\TranslationServiceProvider(), array(
                'translator.domains' => array(),
        ));

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