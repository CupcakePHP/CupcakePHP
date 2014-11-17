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

use CupcakeApp;

class Config
{


    public static function load()
    {
        $ambientConfigs = array(
                'default' => array(
                        'title' => '(c) CupcakePHP: The Rapid and Tasty Development Framework.'
                ),
                'production' => array(
                        'db' => getenv('CLEARDB_DATABASE_URL')
                ),
                'homolog' => array(),
                'development' => array(
                        'db' => 'mysql://root@localhost/cupcake'
                )
        );

        $cupcakeAppConfig = CupcakeApp\Config::load();
        return array_merge($ambientConfigs['default'], $ambientConfigs[getenv('AMBIENT')], $cupcakeAppConfig['default'], $cupcakeAppConfig[getenv('AMBIENT')]);

    }


    public static function route()
    {
        $cupcakeAppConfig = CupcakeApp\Config::load();

        return array_merge(array(
                'appName' => 'Cupcake',
                'controller' => 'index',
                'action' => 'home',
        		'arguments' => array(),
                'appsFolder' => 'Apps',
                'controllerFolder' => 'Controller',
                'viewFolder' => 'View',
                'layoutFolder' => 'Layout',
                'webFolder' => 'Web',
        		'layout' => 'charisma',
        		'extensionView' => 'phtml',
        		'view' => 'Index' . DS . 'home.html',
                'twig' => true
        ), $cupcakeAppConfig['route']);

    }


}

?>