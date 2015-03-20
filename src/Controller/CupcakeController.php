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

class CupcakeController extends \Cupcake\Controller
{


    public function __construct($app)
    {
    	parent::__construct($app);
    	
        $this->setAssinante('Você Cupcaker');
        $this->setAlert(false);
        $this->setTopFull(false);
        
    }


}