<?php

namespace app\models;

class Deploy extends \lithium\data\Model {

	public $validates = array();
    protected static $_apps = array();
    protected $_schema = array(
        '_id' => array('type' => 'id'), 
        'app' => array('type' => 'id'), 
        'name' => array('type' => 'string'), 
        'ref' => array('type' => 'string'), 
        'branch' => array('type' => 'string'),
        'created' => array('type' => 'date')
    );

    /**
     * Override _init to ensure MongoDb indexes
     */
    public static function __init()
    {
        parent::__init();

        $collection = static::connection()->connection->{static::meta('source')};
        $collection->ensureIndex(array('index' => 1));
        $collection->ensureIndex(array('app' => 1));
    }

    /**
     * Perform a checkout of app
     *
     * @param array $data Indicates where to check out and in what starting state
     *              - `path` Where to check out to
     *              - `branch` Check out a new branch
     *              - `tag` Check out a new tag
     * @return bool
     */
    public static function checkout($entity, array $options = array()) {
        if (!isset($options['repo']))
            throw new \RuntimeException("Deploy::checkout() must receive Repo in options");

        $repo = $options['repo'];
        $to = $this->_app()->path(array('deploy', $entity->name));

        if (!file_exists($to)) {
            $command = "git clone {$this->repo} {$to}";
            $ok = (strpos(shell_exec($command), 'Initialized') !== false);
            chdir($to);
            shell_exec("git checkout {$branch}");
            return $ok;
        }
        else
            return ($pull) ? $this->pull($branch) : true;
    }

    /**
     * Return App object connected to given entity
     * @param object $entity
     * @param array $options
     * @return App
     */
    protected static function _app($entity, array $options = array()) {
        $id = (string) $entity->app;
        if (!$entity->_apps[$id]) {
            $model = $entity->model();
            $entity->_apps[$id] = $model::first($entity->app);
        }
        return $entity->_apps[$id];
    }
}
