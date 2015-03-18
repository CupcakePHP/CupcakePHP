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

class NoInjection
{


    public static function sql($sql)
    {
        $sql = preg_replace(self::mySqlRegcase('/(distinct |having |truncate |replace |handler |like | as |or |procedure |limit |order by |group by | asc| desc|from |update |select |insert |delete |where |drop table |show tables |#|\*|\\\\)/'), '', $sql);

        $sql = trim($sql);
        $sql = strip_tags($sql);
        $sql = addslashes($sql);

        return $sql;

    }


    public static function mySqlRegcase($str)
    {
        $res = '';

        $chars = str_split($str);
        foreach ($chars as $char) {
            if (preg_match('/[A-Za-z]/', $char) === true) {
                $charUpperUtf = utf8_encode(strtoupper($char));
                $res .= '[' . $charUpperUtf . $charUpperUtf . ']';
            } else {
                $res .= $char;
            }
        }

        return $res;

    }


    public static function xss($str)
    {
        $str = strip_tags($str);
        $str = utf8_decode($str);
        $str = htmlspecialchars($str);

        $str = strtr($str, array(
                '\\' => '',
                '\'' => '',
                '|' => '',
                '>' => '',
                '<' => '',
                '"' => '',
                '--' => '',
                '%' => '',
                '(' => '',
                ')' => '',
                '[' => '',
                ']' => '',
                ':' => '',
                ';' => ''
        ));
        $str = trim($str);

        return $str;

    }


}