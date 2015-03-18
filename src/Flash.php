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

class Flash
{


    public static function alert($msg)
    {
        Cookies::save('flashAlert', $msg);

    }


    public static function getAlert()
    {
        $alert = Cookies::restore('flashAlert');
        Cookies::delete('flashAlert');

        return $alert;

    }


    public static function topFull($msg, $type='information')
    {
        Cookies::save('flashTopFull', $msg);
        Cookies::save('flashTopFullType', $type);

    }


    public static function getTopFull()
    {
        $msg = Cookies::restore('flashTopFull');
        $type = Cookies::restore('flashTopFullType');

        Cookies::delete('flashTopFull');
        Cookies::delete('flashTopFullType');

        if ($msg !== null) {
            return '"text":"'.$msg.'","layout":"top","type":"'.$type.'"';
        }

        return false;

    }


}