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

use \Cupcake\NoInjection as NoInjection;
use Doctrine\DBAL\Types\Type;

class Form extends \Cupcake\Helper
{

    public $mappings = array();

    public $types = array(
            'integer' => 'number'
    );


    public function __construct($app)
    {
        parent::__construct($app);
        $this->mappings = $this->DAO->ClassMetadata->getAssociationMappings();

    }


    public function field($field, $value, array $attr = array())
    {
        $fieldFunction = $this->defineField($field);
        return $this->$fieldFunction($field, $value, $attr);

    }


    public function defineField($field)
    {
        if (property_exists($this->entity, $field . 'Options') === true && property_exists($this->entity, $field . 'Multiple') === true) {
            return 'checkbox';
        } else if (property_exists($this->entity, $field . 'Options') === true || $this->ehMapeado($field) === true) {
            return 'select';
        } else if ($this->DAO->ClassMetadata->getTypeOfField($field) === 'text') {
            return 'textarea';
        }

        return 'input';

    }


    public function ehMapeado($field)
    {
        return array_key_exists($field, $this->mappings);

    }


    public function input($field, $value, array $attr = array())
    {
        return '<input ' . $this->required($field) . ' id="form-' . $this->entity . '-' . $field . '" name="' . $this->entity . '[' . $field . ']" ' . $this->concatAttr($attr) . ' type="' . $this->type($field) . '" value="' . $this->value($value, $field) . '"/>'.$this->mask($field);

    }


    public function textarea($field, $value, array $attr = array())
    {
        return '<textarea ' . $this->required($field) . ' id="form-' . $this->entity . '-' . $field . '" name="' . $this->entity . '[' . $field . ']" ' . $this->concatAttr($attr) . '>' . $this->value($value, $field) . '</textarea>';

    }


    public function mask($field)
    {
        if ($this->DAO->ClassMetadata->getTypeOfField($field) === 'float') {
            return '<script>
                        $(document).ready(function() {
                            $("#form-' . $this->entity . '-' . $field . '").maskMoney({thousands:\'.\', decimal:\',\'});
                        });
                    </script>';
        }

    }


    public function value($value, $field)
    {
        if ($this->DAO->ClassMetadata->getTypeOfField($field) === 'float') {
            return number_format($value, 2, ',', '.');
        }

        return $value;

    }


    public function checkbox($field, $value, array $attr = array())
    {
        $getter = 'get' . $field . 'Options';
        $options = ($this->model->$getter());
        $strOpts = '';
        $value = explode($this->model->getListSeparator(), $value);

        foreach ($options as $k => $v) {
            $strOpts .= '<label class="left" style="margin-right:10px;"><input type="checkbox" value="'.$v.'" ' . $this->checked($v, $value) . ' id="form-' . $this->entity . '-' . $field . '" name="' . $this->entity . '[' . $field . '][]" ' . $this->concatAttr($attr) . '>' . $v . '</label>';
        }

        return $strOpts;

    }


    public function select($field, $value, array $attr = array())
    {
        $multiple = $this->multiple($field);

        return '<select ' . $multiple . ' ' . $this->required($field) . ' id="form-' . $this->entity . '-' . $field . '" name="' . $this->entity . '[' . $field . ']" ' . $this->concatAttr($attr) . '>
                ' . $this->selecione($multiple) . '
                ' . $this->options($field, $value) . '
                </select>';

    }


    public function selecione($multiple)
    {
        if ($multiple === null) {
            return '<option value="">Selecione</option>';
        }

    }


    public function options($field, $value)
    {
        if ($this->ehMapeado($field) === true) {
            $options = $this->loadMappedData($field);
        } else {
            $getter = 'get' . $field . 'Options';
            $options = ($this->model->$getter());
        }

        if (is_object($value) === true) {
            $value = $this->getIdentifier($value);
        }

        $strOpts = '';
        foreach ($options as $k => $v) {
            $id = $v;

            if (is_object($v) === true) {
                $id = $v->getId();
                $v = $this->getIdentifier($v);
            }

            $strOpts .= '<option value="' . $id . '" ' . $this->selected($v, $value) . '>' . $v . '</option>';
        }

        return $strOpts;

    }


    public function loadMappedData($field)
    {
        $this->uses($this->mappings[$field]['targetEntity']);
        $mappedDAO = $this->mappings[$field]['targetEntity'] . 'DAO';

        $data = $this->$mappedDAO->findAll();
        return $data;

    }


    public function selected($v1, $v2)
    {
        if ((string) $v1 === (string) $v2) {
            return 'selected';
        }

    }


    public function getIdentifier($v)
    {
        (property_exists($v, 'identifier') === true) ? $getter = 'get' . ucfirst($v->getIdentifier()) : $getter = 'getId';
        return $v->$getter();

    }


    public function checked($v1, $v2)
    {
        if (in_array($v1, $v2) === true) {
            return 'checked';
        }

    }


    public function multiple($field)
    {
        if (property_exists($this->entity, $field . 'Multiple') === true) {
            return 'multiple data-placeholder="Selecione um ou mais itens"';
        }

    }


    public function concatAttr(array $attr = array())
    {
        $attrs = '';
        foreach ($attr as $k => $v) {
            $attrs .= $k . '="' . $v . '" ';
        }

        return $attrs;

    }


    public function required($field)
    {
        $required = false;
        if ($this->ehMapeado($field) === true) {
            if (isset($this->mappings[$field]['joinColumns'][0]['nullable']) === true || @$this->mappings[$field]['joinColumns'][0]['nullable'] === false) {
                $required = true;
            }
        } else if ($this->DAO->ClassMetadata->isnullable($field) === false) {
            $required = true;
        }

        if ($required === true) {
            return 'required="required"';
        }

    }


    public function type($field)
    {
        if (isset($this->types[$this->DAO->ClassMetadata->getTypeOfField($field)]) === true) {
            return $this->types[$this->DAO->ClassMetadata->getTypeOfField($field)];
        }

        return 'text';

    }


}