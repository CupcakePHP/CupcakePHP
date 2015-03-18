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

class Sessions
{


    public static function start($name)
    {
        session_name($name);
        session_start();

    }


    public static function save($var, $data)
    {
        $_SESSION[$var] = Crypt::encode($data);
        return $data;

    }


    public static function restore($var)
    {
        $data = $_SESSION[$var];

        if (is_object($data) === true) {
            $data = $_SESSION[$var];
        } else {
            (isset($_SESSION[$var]) === true) ? $data = Crypt::decode($_SESSION[$var]) : $data = null;
        }

        return $data;

    }


    public static function remove($var)
    {
        unset($_SESSION[$var]);

    }


    public static function clean()
    {
        session_destroy();

    }


}