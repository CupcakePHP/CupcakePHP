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

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{


    /**
     * Verifica se a classe existe
     *
     * @param string $className
     *
     * @return bool
     */
    public function classExists($className)
    {
        return class_exists($className);

    }


    /**
     * Verifica se um metodo existe em um objeto
     *
     * @param string $object
     * @param string $methodName
     *
     * @return boolean
     */
    public function methodExists($object, $methodName)
    {
        return method_exists($object, $methodName);

    }


}
