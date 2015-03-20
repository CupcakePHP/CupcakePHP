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
namespace Cupcake\Layout;

class Charisma extends \Cupcake\Controller
{


    /**
     * Carrega o componente do layout Charisma
     *
     * @return void
     */
    public function index()
    {
        $this->setTitle($this->app['config']['title']);
        $this->setWeb('//' . $this->app['request']->getHost() . $this->app['request']->getBasePath() . '/CupcakePHP/src/' . $this->app['route']['webFolder'] . '/');

        $this->useComponent('HeaderCharisma');
        $this->useComponent('TopCharisma');
        $this->useComponent('SidebarCharisma');
        $this->useComponent('FooterCharisma');

    }


}