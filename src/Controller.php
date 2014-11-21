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

    public $layout = null;


    public function __construct($app)
    {
        $this->app = $app;

        if($this->layout === null) {
            $this->layout = $app['route']['layout'];
        }

        $this->setWeb('//' . $this->app['request']->getHost() . $this->app['request']->getBasePath() . '/' . $this->app['route']['webFolder'] . '/');
        $url = '//' . $this->app['request']->getHost() . $this->app['request']->getBasePath() . '/';
        $this->setUrl($url);
        $this->setIndex($url . $this->app['route']['appName'] . '/');
        $this->setIndexController($url . $this->app['route']['appName'] . '/' . $this->app['route']['controller'] . '/');
        $this->setAqui($this->app['request']->getPathInfo());

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


    public function render($content = null)
    {
        $this->setV($this->app['Vars']);
        $this->app['GPS']->addIncludePaths();

        if ($this->layout !== false) {
            if ($content === null) {
                $content = $this->getView();
            }

            $this->setContent($content);
            return $this->getLayout();
        } else {
            if ($content !== null) {
                return $content;
            } else {
                return $this->getView();
            }
        }

    }


    public function getView()
    {
        ob_start();
        extract($this->app['Vars']->vars);
        require $this->app['route']['view'];
        return ob_get_clean();
    }


    public function getLayout()
    {
        $layoutClassName = $this->app['GPS']->getLayoutClassName(ucfirst($this->layout));

        $Layout = new $layoutClassName($this->app);
        $Layout->index();

        ob_start();
        extract($Layout->app['Vars']->vars);
        $layoutViewFile = $this->app['GPS']->getLayoutViewFile();
        require $layoutViewFile;
        return ob_get_clean();

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


    public function useComponent($component)
    {
        ob_start();
        $componentClassName = $this->app['GPS']->getComponentClassName($component);
        if ($componentClassName !== false) {
            $Component = new $componentClassName($this->app);
            $Component->index();

            extract($Component->app['Vars']->vars);
        } else {
            extract($this->app['Vars']->vars);
        }

        $componentViewFile = $this->app['GPS']->getComponentViewFile($component);
        require $componentViewFile;
        $content = ob_get_clean();

        $setVar = 'set' . $component;
        $this->$setVar($content);

    }

    public function home() {
        return 'Ops, acho que não era pra vir para cá! - Faça um método home() no controller index da sua aplicação.';

    }


}

?>