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

use \Cupcake\Flash as Flash;
use Apps\Simplesys\Controller\IndexController;

class Controller
{

    public $app;

    public $db;

    public $entity;

    public $DAO;

    public $layout = null;

    public $view = null;

    public $args = array();

    public $request = array();

    public $helpers = array();


    /**
     * Inicia controller
     *
     * @param array $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;

        if ($this->layout === null) {
            $this->layout = $app['route']['layout'];
        }

        if ($this->view === null) {
            $this->view = $app['route']['view'];
        }

        $this->args = $app['route']['arguments'];
        $this->request = array_merge($_GET, $_POST);

        $this->entity = str_replace('Controller', '', ucfirst(basename(str_replace('\\', '/', get_class($this)))));

        $this->setBaseVars();
        if ($app['FileSystem']->classExists($this->entity) === true) {
            $model = $this->entity;
            $this->$model = new $model();
        }

        $this->uses($this->entity);
        $DAO = $this->entity . 'DAO';

        $this->DAO = $this->$DAO;

        $this->loginFake();

    }


    public function home()
    {
        if ($this->app['route']['entity'] !== '') {
            $this->view = 'lista.phtml';
            $this->help('Lista');

            $this->setResults($this->Lista->get());
            $this->setTitLista($this->app['route']['entity']);

            $this->setColunas(array_keys(get_class_vars($this->app['route']['entity'])));
            $this->setIcon('icon-star-empty');
        } else {
            $controllerPath = '\Apps\\'.$this->app['route']['appName'].'\Controller\IndexController';
            $controller = new $controllerPath($this->app);
            $controller->home();
            $this->view = 'Index/indexHome.phtml';
        }

    }


    public function novo()
    {
        $this->view = 'form.phtml';
        $this->help('Form');

        $class = $this->app['route']['entity'];
        $dados = new $class();
        $this->setDados($dados);

        $this->setCampos(array_keys(get_class_vars($this->app['route']['entity'])));
        $this->setTitulo($this->app['route']['entity']);
        $this->setMigalha('Novo registro');
        $this->setAcao('Cadastro');
        $this->setMsgFlash('Registro inserido com sucesso!');

    }


    public function editar()
    {
        $this->view = 'form.phtml';
        $this->help('Form');

        $dados = $this->DAO->find($this->args[0]);
        if ($dados === null) {
            $class = $this->app['route']['entity'];
            $dados = new $class();
        }

        $this->setDados($dados);

        $this->setCampos(array_keys(get_class_vars($this->app['route']['entity'])));
        $this->setTitulo($this->app['route']['entity']);
        $this->setMigalha('Edição do registro nº ' . $this->args[0]);
        $this->setAcao('Edição');
        $this->setMsgFlash('Registro editado com sucesso!');

    }


    public function ver()
    {
        $this->view = 'view.phtml';

        $dados = $this->DAO->find($this->args[0]);
        $this->setDados($dados);

        $this->setCampos(array_keys(get_class_vars($this->app['route']['entity'])));
        $this->setTitulo($this->app['route']['entity']);
        $this->setMigalha('Registro nº ' . $this->args[0]);
        $this->setAcao('Detalhes');
        $this->setIcon('icon-star-empty');

    }


    /**
     * Prepara a tela de cadastro de cliente
     *
     * @return void
     */
    public function salvar()
    {
        $this->layout = false;
        $dados = $this->DAO->listen($_POST);

        $this->DAO->salvar($dados);

        Flash::topFull($_POST['flashMsg'], 'information');

        if ($_POST['saida'] === 'view') {
            return '2;'.$this->getIndexController().'ver/'.$dados->getId();
        } else {
            return '2;'.$this->getIndexController();
        }

    }


    public function loginFake()
    {
        $assinante = Cookies::restore('assinante');
        if (isset($assinante) === false) {
            $this->uses('Assinantes');
            $assinante = $this->AssinantesDAO->find(1);
            Cookies::save('assinante', $assinante);
        }

        $this->setAssinante($assinante);

    }


    public function uses($entity)
    {
        $entityDAO = $entity . 'DAO';
        $entityName = 'Apps\\'.$this->app['route']['appName'].'\DAO\\' . $entityDAO;
        if ($this->app['FileSystem']->classExists($entityName) === true) {
            $this->$entityDAO = new $entityName($this->app);
        } else {
            $this->$entityDAO = new DAO($this->app, $entity);
        }

    }


    public function help($helper)
    {
        $helperName = 'Apps\\' . $this->app['route']['appName'] . '\helpers\\' . ucfirst($helper);
        if ($this->app['FileSystem']->classExists($helperName) === false) {
            $helperName = 'Cupcake\helpers\\' . ucfirst($helper);
        }

        $this->helpers[] = $helper;
        $this->$helper = new $helperName($this->app);
        $this->$helper->request = $this->request;

    }


    /**
     * Seta as variaveis básicas
     *
     * @return void
     */
    public function setBaseVars()
    {
        $this->setWeb('//' . $this->app['request']->getHost() . $this->app['request']->getBasePath() . '/' . $this->app['route']['webFolder'] . '/');
        $url = '//' . $this->app['request']->getHost() . $this->app['request']->getBasePath() . '/';
        $this->setUrl($url);
        $this->setIndex($url . strtolower($this->app['route']['appName'] . '/'));
        $this->setIndexController(strtolower($url . $this->app['route']['appName'] . '/' . $this->app['route']['entity'] . '/'));
        $this->setHere(strtolower($this->app['request']->getBasePath() . $this->app['request']->getPathInfo()));

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
        $argument = $this->app['Vars']->validaArg($arguments);
        $var = $this->app['Vars']->$name($argument);

        if ($var === false) {
            die('Método de Controller não encontrado: <b>' . $name . '</b> (toDo: exception para isso)');
        }

        return $var;

    }


    /**
     * Renderiza o resultado do action no controller
     *
     * @param string $content
     *
     * @return string
     */
    public function render($content = null)
    {
        $this->setModel();
        $this->setV($this->app['Vars']);

        if ($this->layout !== false) {
            if ($content === null) {
                $content = $this->getView();
            }

            $this->setContent($content);
            return $this->getLayout();
        } else {
            if ($content !== null) {
                return $content;
            }

            return $this->getView();
        }

    }


    public function setModel()
    {
        $setModel = 'set'.$this->entity;
        $model = $this->entity;
        if (isset($this->$model) === true) {
            $this->$setModel($this->$model);
        }
    }


    /**
     * Retorna uma view renderizada
     *
     * @return string
     */
    public function getView()
    {
        return $this->app['Templating']->render($this->view, $this->app['Vars']->vars);

    }


    /**
     * Retorna um layout renderizado
     *
     * @return string
     */
    public function getLayout()
    {
        $layoutClassName = $this->app['GPS']->getLayoutClassName(ucfirst($this->layout));
        $layout = new $layoutClassName($this->app);
        $layout->index();

        return $this->app['Templating']->render($this->app['route']['layout'] . '.' . $this->app['route']['extensionView'], $this->app['Vars']->vars);

    }


    /**
     * Retorna um componente renderizado
     *
     * @param string $component
     *
     * @return void
     */
    public function useComponent($component)
    {
        ob_start();
        $componentClassName = $this->app['GPS']->getComponentClassName($component);
        if ($componentClassName !== false) {
            $componentClass = new $componentClassName($this->app);
            $componentClass->index();
            extract($componentClass->app['Vars']->vars);
        }

        extract($this->app['Vars']->vars);

        $componentViewFile = $this->app['GPS']->getComponentViewFile($component);
        require $componentViewFile;
        $content = ob_get_clean();

        $setVar = 'set' . $component;
        $this->$setVar($content);

        return true;

    }


    public function deletar()
    {
        $this->layout = false;
        $this->DAO->deletar($this->args[0]);

        if (isset($_GET['saida']) === true) {
            $saida = '4;' . $this->getIndex() . urldecode($_GET['saida']);
        } else {
            $saida = '4;' . $this->getIndexController();
        }

        Flash::getTopFull('Registro deletado com sucesso.', 'warning');

        return $saida;

    }


}
