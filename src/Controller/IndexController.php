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
namespace Cupcake\Controller;

//use Cupcake\Controller;

class IndexController extends CupcakeController
{


    /**
     * Prepara a tela index da aplicação
     *
     * @return void
     */
    public function home()
    {
        $this->setBemVindo('Olá <b>Cupcaker</b>! Bem-vindo ao CupcakePHP | The Rapid and Tasty Development Framework - Um produto Simplesys.');

    }


}