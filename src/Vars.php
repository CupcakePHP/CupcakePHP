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

class Vars
{

    public $vars = array();


    /**
     * Call dinamico para registar e ler variaveis
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        $var = lcfirst(substr($name, 3));

        if (substr($name, 0, 3) === 'has') {
            return isset($this->vars[$var]);
        } else if (substr($name, 0, 3) === 'set') {
            $this->vars[$var] = $arguments[0];
        } else if (isset($this->vars[$var]) === true) {
            return $this->vars[$var];
        } else if (isset($this->vars[$name]) === true) {
            return $this->vars[$name];
        } else {
            return false;
        }

    }


    /**
     * Valida se o argumento é válido
     *
     * @param array $arg
     *
     * @return mixed
     */
    public function validaArg($arg)
    {
        if (isset($arg[0]) === false) {
            return false;
        }

        return $arg[0];

    }


    public function get()
    {
        return $this->vars;
    }


}

?>