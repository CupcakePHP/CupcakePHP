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
namespace Cupcake\helpers;

class Pages extends \Cupcake\Helper
{


    public function showData($result, $field)
    {
        if ($result->$field instanceof \DateTime) {
            return date('d-m-Y - H:i \h.', $result->$field->getTimestamp());
        } else if (is_object($result->$field) === true) {
            (property_exists($result->$field, 'identifier') === true) ? $getter = 'get' . ucfirst($result->$field->getIdentifier()) : $getter = 'getId';
            return $result->$field->$getter();
        } else {
            $getter = 'get'.ucfirst($field);
            return $result->$getter();
        }

    }


}