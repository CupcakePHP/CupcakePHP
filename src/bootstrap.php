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
header('Content-Type: text/html; charset=utf-8');
$autoload = require dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
$cupcake = new Application();
@header('Content-Type: text/html; charset=utf-8');
define('DS', DIRECTORY_SEPARATOR);
$cupcake['debug'] = true;
if (getenv('AMBIENT') === false) {
    putenv('AMBIENT=development');
    $cupcake['debug'] = true;
}
if ($cupcake['debug'] === true) {
    ini_set('display_errors', true);
    error_reporting(E_ALL);
}
$cupcake['autoload'] = $autoload;
$cupcake['Vars'] = new Cupcake\Vars();
$cupcake['FileSystem'] = new Cupcake\Filesystem();
$cupcake['config'] = function () {
    return Cupcake\Config::load();
};
$cupcake['route'] = function () {
    return Cupcake\Config::route();
};
$cupcake->match('{url}', function (Request $request) use ($cupcake) {
    $cupcake['GPS'] = new Cupcake\GPS($cupcake['route'], $cupcake['config'], substr($request->getPathInfo(), 1));
    $cupcake['route'] = $cupcake['GPS']->route();
    $cupcake['config'] = $cupcake['GPS']->config();
    $cupcake['request'] = $request;
    $cupcake['autoload']->add('', dirname(dirname(__FILE__)) . DS . $cupcake['route']['appsFolder'] . DS . $cupcake['route']['appName'] . DS . $cupcake['route']['modelFolder'] . DS);
    $config = Setup::createAnnotationMetadataConfiguration(array(dirname(dirname(__FILE__)) . DS . $cupcake['route']['appsFolder'] . DS . $cupcake['route']['appName'] . DS . $cupcake['route']['modelFolder']), $cupcake['debug']);
    $conn = parse_url($cupcake['config']['db']);
    $conn['driver'] = str_replace('pdo-mysql', 'pdo_mysql', $conn['scheme']);
    $conn['dbname'] = substr($conn['path'], 1);
    $conn['driverOptions'] = array(
            1002 => 'SET NAMES utf8'
    );
    if (isset($conn['pass']) === true) {
        $conn['password'] = $conn['pass'];
    }
    $cupcake['db'] = EntityManager::create($conn, $config);
    $controllerPath = $cupcake['route']['controller'];
    $controller = new $controllerPath($cupcake);
    $action = $cupcake['route']['action'];
    $loader = new FilesystemLoader(array(
            dirname(dirname(__FILE__)) . DS . 'View' . DS . '%name%',
            dirname(dirname(__FILE__)) . DS . 'src' . DS . 'View' . DS . '%name%',
            dirname(dirname(__FILE__)) . DS . $cupcake['route']['appsFolder'] . DS . $cupcake['route']['appName'] . DS . $cupcake['route']['viewFolder'] . DS . '%name%'
    ));
    $templateNameParser = new TemplateNameParser();
    $cupcake['Templating'] = new PhpEngine($templateNameParser, $loader);
    return $controller->render($controller->$action());
})->assert('url', '.+|');