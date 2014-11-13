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

/**
 * Cupcake is a autoRouter and autoRender microFramework based on the Symfony2 Components.
 * Controllers, actions and views automatic executed by the url path.
 */
//use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request as Request;

class App
{

    public $app;

    public function __construct()
    {
        $this->app = new \Silex\Application();
    }


    /**
	 * Run the Framework and do all the magic.
	 */
	public function run()
	{
		$app = $this->app;
		$app->match('{url}', function(Request $request) use ($app) {
		    return $app['Simplesys']->run($request);
		})->assert('url', '.+|');

		$app->run();
	}
}

?>