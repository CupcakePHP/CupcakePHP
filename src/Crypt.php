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

class Crypt
{


    public static function encode($str, $j = 5)
    {
        for ($i = 0; $i < $j; $i++) {
            $str = strrev(base64_encode(serialize($str)));
        }

        return $str;

    }


    public static function decode($str, $j = 5)
    {
        for ($i = 0; $i < $j; $i++) {
            $str = unserialize(base64_decode(strrev($str)));
        }

        return $str;

    }


    public static function revert($input, $fim)
    {
        $output = null;

        for ($i = 2; $i <= $fim; $i++) {
            $j = 0;
            $k = 0;
            $len = strlen($input);
            while ($j < $len) {
                $aux[$k] = null;
                for ($z = 0; $z < $i; $z++) {
                    $j++;
                    if (isset($input[$j]) === true) {
                        $aux[$k] .= strval($input[$j]);
                    }
                }

                $output .= strrev($aux[$k]);
                $k++;
            }

            $input = $output;

            if ($i < $fim) {
                $output = null;
            }
        }//end for

        return $output;

    }


    public static function unrevert($input, $fim)
    {
        $output = null;

        for ($i = $fim; $i >= 2; $i--) {
            $j = 0;
            $k = 0;
            $len = strlen($input);
            while ($j < $len) {
                $aux[$k] = null;
                for ($z = 0; $z < $i; $z++) {
                    $aux[$k] .= strval($input[$j++]);
                }

                $output .= strrev($aux[$k]);
                $k++;
            }

            $input = $output;

            if ($i > 2) {
                $output = null;
            }
        }

        return $output;

    }


    public static function cript($input, $fim = 5)
    {
        $output = null;

        $i = 0;
        $len = strlen($input);
        while ($i < $len) {
            $output .= strrev(strval(dechex(ord($input[$i]))));
            $i++;
        }

        $output = str_replace('0', 'AB', $output);
        $output = str_replace('1', 'CD', $output);
        $output = str_replace('2', 'EF', $output);
        $output = str_replace('3', 'GH', $output);
        $output = str_replace('4', 'IJ', $output);
        $output = str_replace('5', 'KL', $output);
        $output = str_replace('6', 'MN', $output);
        $output = str_replace('7', 'OP', $output);
        $output = str_replace('8', 'QR', $output);
        $output = str_replace('9', 'ST', $output);
        $output = str_replace('a', 'UV', $output);
        $output = str_replace('b', 'WX', $output);
        $output = str_replace('c', 'YZ', $output);
        $output = str_replace('d', '@#', $output);
        $output = str_replace('e', '$%', $output);
        $output = str_replace('f', '&*', $output);

        $output = self::revert($output, $fim);

        return $output;

    }


    public static function uncript($input, $fim = 5)
    {
        $output = null;
        $aux = null;

        $input = self::unrevert($input, $fim);

        $input = str_replace('AB', '0', $input);
        $input = str_replace('CD', '1', $input);
        $input = str_replace('EF', '2', $input);
        $input = str_replace('GH', '3', $input);
        $input = str_replace('IJ', '4', $input);
        $input = str_replace('KL', '5', $input);
        $input = str_replace('MN', '6', $input);
        $input = str_replace('OP', '7', $input);
        $input = str_replace('QR', '8', $input);
        $input = str_replace('ST', '9', $input);
        $input = str_replace('UV', 'a', $input);
        $input = str_replace('WX', 'b', $input);
        $input = str_replace('YZ', 'c', $input);
        $input = str_replace('@#', 'd', $input);
        $input = str_replace('$%', 'e', $input);
        $input = str_replace('&*', 'f', $input);

        $i = 0;
        $j = 0;
        $len = strlen($input);
        while ($i < $len) {
            $aux[$j] = strval($input[$i]) . strval($input[($i + 1)]);
            $i += 2;
            $j++;
        }

        $i = 0;
        while ($aux[$i]) {
            $output .= chr(hexdec(strrev($aux[$i])));
            $i++;
        }

        return $output;

    }


}