<?php

namespace app\models;
use app\models\Repo;
use app\models\Deploy;

class App extends \lithium\data\Model {

	public $validates = array();
    protected $_schema = array(
        '_id' => array('type' => 'id'),
        'title' => array('type' => 'string'), 
        'repo' => array('type' => 'string'), 
        'path' => array('type' => 'string'), 
        'created' => array('type' => 'date'), 
    );

    /**
     * Override _init to ensure MongoDb indexes
     */
    public static function __init()
    {
        parent::__init();

        $collection = static::connection()->connection->{static::meta('source')};
        $collection->ensureIndex(array('path' => 1), array('unique' => true));
        $collection->ensureIndex(array('repo' => 1), array('unique' => true));
        $collection->ensureIndex(array('title' => 1));
    }

    public static function path($entity, $sub = null, array $options = array()) {
        $options += array(
            'create' => false
        );
        $path = $entity->path;

        $complete = (strpos($path, "/") === 0);

        if (!$complete) $path = "/home/apps/" . $path;
        if ($sub) {
            $sub = (is_array($sub)) ?: (array) $sub;
            $path .= "/" . implode("/", $sub);
        }

        if (!file_exists($path) && $options['create'])
            mkdir($path, 0777, true);

        return (file_exists($path)) ? $path : false;
    }

    public static function versions($entity, array $options = array()) {
        if (($path = $entity->path()) == false) {
            $path = $entity->path(null, array('create' => true));
        }
        $branch = "master";
        $remote = $entity->repo;
        $repo = Repo::fromRemote($remote, compact('path'));
        $pull = false;
        if ($repo->checkout(compact('branch', 'path', 'pull')))
            return $repo->tags(compact('path'));
        return array();
    }

    /**
     * List all deploys
     */
    public static function deployed($entity, array $options = array()) {
        $path = $entity->path();
        print_r($entity->deployed);
        $repo = Repo::fromRemote($entity->remote, compact('path'));
        $return = array();
        $deploy = $repo->ls('deploy');
        foreach ($deploy['files'] as $key => $name) {
            $file = $deploy['path'] . "/" . $name;
            if (is_link($file)) {
                $info = preg_split("/\s+/", readlink($file));
                $return[] = array(
                    'name' => $name,
                    'link' => reset($info)
                );
            }
        }
        return $return;
    }

    /**
     * Create a new deployable pointer in the thin line of history
     * Creates deploy/{:name} => versions/{:commit} which is created doing:
     * * cp {:master} {:target}
     * * git reset --hard {:commit/tag}
     */
    public static function deploy($entity, array $data = array(), array $options = array()) {
        $data += array(
            'branch' => 'master',
        );
        $data['account'] = $entity->_id;

        $deploy = Deploy::create();
        $data = array_intersect_key($data, $deploy->schema());
        $deploy->save($data);
        return $deploy;
    }

    public static function repo($entity) {
        $path = $entity->path();
        return Repo::fromRemote($entity->repo, compact('path'));
    }

    /**
     * Perform initial clone of repo
     */
    public function bootstrap($entity, array $options = array()) {
        /**
         * Create default app paths
         */
        if (($path = $entity->path()) == false) {
            $path = $entity->path(null, array('create' => true));
            $entity->path("deploy", array('create' => true));
            $entity->path("tags", array('create' => true));
        }
        // @var Git
        $repo = Repo::fromRemote($entity->repo, compact('path'));

        /**
         * Create a default deploy target
         */
        if ($repo->checkout()) {
            /*
            $entity->deploy(array(
                'tag' => 'HEAD',
                'name' => 'default'
            ));
             */
        }
    }
}
