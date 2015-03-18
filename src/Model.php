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

class Model
{

    const ORDER = 'id';

    const DIRECTION = 'ASC';

    const LIMIT = 5;

    const OFFSET = 0;

    protected $listSeparator = ', ';


    /**
     * Call dinamico para invocar getters e setters pelo fw
     *
     * @param string $name
     * @param array $arguments
     *
     * @return string
     */
    public function __call($name, $arguments)
    {
        $param = lcfirst(substr($name, 3));
        if (substr($name, 0, 3) === 'has') {
            return isset($this->$param);
        } else if (substr($name, 0, 3) === 'set') {
            $this->$param = $arguments[0];
            return true;
        } else if (property_exists($this, $param) === true) {
            return $this->$param;
        } else if (isset($this->$name) === true) {
            return $this->$name;
        }

        die('Método de Model não encontrado: '.$name . ' (toDo: exception para isso)');

    }


    public function udata($data)
    {
        if ($data instanceof DateTime) {
            $dataHora = explode(' ', $data->format('Y-m-d H:i:s'));
            $data = explode('-', $dataHora[0]);
            $hora = explode(':', $dataHora[1]);

            return mktime($hora[0], $hora[1], $hora[2], $data[1], $data[2], $data[0]);
        }

    }


}
