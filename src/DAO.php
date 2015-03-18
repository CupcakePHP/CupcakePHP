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

class DAO
{

    public $db;

    public $app;

    public $EntityRepository;

    public $ClassMetadata;

    public $name;


    public function __construct($app, $entity='')
    {
        $this->app = $app;
        $this->defineName($app, $entity);
        $this->db = $app['db'];

        if ($app['FileSystem']->classExists($this->name) === true) {
            $this->EntityRepository = $app['db']->getRepository($this->name);
            $this->ClassMetadata = $this->db->getMetadataFactory()->getMetadataFor($this->name);
        }

    }


    public function defineName($app, $entity)
    {
        $classParts = explode('\\', get_class($this));
        $class = array_pop($classParts);
        $class = str_replace('DAO', '', $class);
        if ($class !== '') {
            $this->name = $class;
        } else if ($entity !== '') {
            $this->name = $entity;
        } else {
            $this->name = $app['route']['entity'];
        }

    }


    public function listen(array $data)
    {
        $mappings = $this->ClassMetadata->getAssociationMappings();
        $model = $this->EntityRepository->find($data[$this->name]['id']);
        if ($model === null) {
            $model = $this->loadModel($data[$this->name]['id']);
        }

        foreach ($data[$this->name] as $k => $v) {
            if (isset($mappings[$k]) === true) {
                $mappedDAO = new DAO($this->app, $mappings[$k]['targetEntity']);
                $v = $mappedDAO->find($v);
            }

            $set = 'set' . ucfirst($k);
            (is_array($v) === true) ? $v = implode($model->getListSeparator(), $v) : false;
            $model->$set($v);
        }

        return $model;

    }


    public function defineAssinante($model)
    {
        if (property_exists($model, 'assinante') === true) {
            $model->setAssinante(Cookies::restore('assinante'));
        }

        return $model;

    }


    public function loadModel()
    {
        $name = $this->name;
        $model = new $name();

        return $model;

    }


    public function find($id)
    {
        $criteria = array('id' => $id);
        $result = $this->findBy($criteria);

        if (isset($result[0]) === true) {
            return $result[0];
        }

    }


    public function defineFiltroAssinante(array $criteria)
    {
        $modelName = $this->name;
        $model = new $modelName();
        if (property_exists($model, 'assinante') === true) {
            $criteria['assinante'] = (string) Cookies::restore('assinante')->getId();
        }

        return $criteria;

    }


    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $criteria = $this->defineFiltroAssinante($criteria);
        $result = $this->EntityRepository->findBy($criteria, $orderBy, $limit, $offset);
        return $result;

    }


    public function findAll(array $criteria=array())
    {
        return $this->findBy($criteria);

    }


    public function salvar($model)
    {
        $original = $this->find($model->getId());
        if ($original === null) {
            $this->db->persist($model);
            $model = $this->defineAssinante($model);
            $this->db->merge($model);
        } else {
            $this->db->merge($model);
        }

        $this->db->flush();

    }


    public function deletar($id)
    {
        $model = $this->find($id);
        $this->db->remove($model);
        $this->db->flush();

        return true;

    }


}
