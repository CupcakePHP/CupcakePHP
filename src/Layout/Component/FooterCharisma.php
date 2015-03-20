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
namespace Layout\Component;

use Cupcake\Flash;

class FooterCharisma extends \Cupcake\Controller
{


    /**
     * Prepara e renderiza o header
     *
     * @return void
     */
    public function index()
    {
        $this->setAlert(Flash::getAlert());
        $this->setTopFull(Flash::getTopFull());

    }


}
