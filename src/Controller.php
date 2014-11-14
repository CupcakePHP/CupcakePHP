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

class Controller
{

    public $app;

    public $request;


    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;

    }


    /**
     * Call dinamico para invocar metodos pelo fw
     *
     * @param string $name
     * @param array $arguments
     *
     * @return string
     */
    public function __call($name, $arguments)
    {
        $actionName = $this->getActionName($name);

        if (method_exists($this, $actionName) === true) {
            $this->$actionName($arguments);
        } else {
            $argument = $this->app['Vars']->validaArg($arguments);
            return $this->app['Vars']->$name($argument);
        }

    }


    public function render()
    {
        $app = substr(str_replace('App', 'Apps', str_replace('Controller\Index', '', $this->app['Simplesys']->cliApp)), 1);
        $controller = substr($this->app['Simplesys']->controller, (strrpos($this->app['Simplesys']->controller, '\\') + 1));

        $this->app['twig.loader.filesystem']->addPath($app . 'View\\' .  $controller);
        $this->app['Vars']->setContent($this->app['Simplesys']->action);

        return $this->app['twig']->render('Layout\\' . $this->app['config']['layout'] . '.phtml', $this->app['Vars']->get());

    }


    /**
     * retorna nome de um metodo do tipo Action
     *
     * @param string $name
     *
     * @return string
     */
    public function getActionName($name)
    {
        return substr($name, 1);

    }


}

?>