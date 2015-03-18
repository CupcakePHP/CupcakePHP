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

use Apps;

class Config
{


    /**
     * Entrega as configurações globais do projeto
     *
     * @return array
     */
    public static function load()
    {
        $ambientConfigs = array(
                'default' => array(
                        'title' => '(c) CupcakePHP: The Rapid and Tasty Development Framework.',
                        'nomeSistema' => 'CupcakePHP'
                ),
                'production' => array(
                        'db' => str_replace('pdo_mysql', 'pdo-mysql', 'pdo-'.getenv('CLEARDB_DATABASE_URL'))
                ),
                'homolog' => array(),
                'test' => array(),
                'development' => array(
                        'db' => 'pdo-mysql://root@localhost/cupcake'
                )
        );

         $cupcakeAppConfig = Apps\Config::load();
         return array_merge($ambientConfigs['default'], $ambientConfigs[getenv('AMBIENT')], $cupcakeAppConfig['default'], $cupcakeAppConfig[getenv('AMBIENT')]);

    }


    /**
     * Entrega as configurações de roteamento do projeto
     *
     * @return array
     */
    public static function route()
    {
        $cupcakeAppConfig = Apps\Config::load();

        return array_merge(array(
                'appName' => 'Cupcake',
                'entity' => '',
                'controller' => 'Cupcake\Controller',
                'action' => 'home',
                'arguments' => array(),
                'appsFolder' => 'Apps',
                'controllerFolder' => 'Controller',
                'viewFolder' => 'View',
                'layoutFolder' => 'Layout',
                'modelFolder' => 'Model',
                'componentFolder' => 'Component',
                'webFolder' => 'Web',
                'layout' => 'charisma',
                'extensionView' => 'phtml',
                'view' => 'Index' . DS . 'home',
                'twig' => true,
                'contentVar' => 'content'
        ), $cupcakeAppConfig['route']);

    }


}
