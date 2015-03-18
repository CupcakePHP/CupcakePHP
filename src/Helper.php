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

use \Cupcake\NoInjection as NoInjection;

class Helper extends Controller
{

    public $classController;

    public $model;


    public function __construct($app)
    {
        parent::__construct($app);

        $helperName = $this->entity;
        $modelClass = $app['route']['entity'];

        $this->entity = $app['route']['entity'];

        $this->model = new $modelClass();
        $this->DAO = new DAO($app, $this->entity);

        $setName = 'set'.$helperName;
        $this->$setName($this);

    }


}