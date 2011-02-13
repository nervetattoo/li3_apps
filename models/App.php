<?php

namespace app\models;

class App extends \lithium\data\Model {

	public $validates = array();
    protected $_schema = array(
        '_id' => array('type' => 'id'),
        'title' => array('type' => 'string'), 
        'repo' => array('type' => 'repo'), 
        'path' => array('type' => 'string'), 
        'created' => array('type' => 'date'), 
    );
    public static function path($entity, $sub = null, array $options = array()) {
        $options += array(
            'create' => false
        );
        $path = $entity->path;

        $complete = (strpos($path, "/") === 0);

        if (!$complete) $path = "/home/apps/" . $path;
        if ($sub) $path .= "/" . $sub;

        return (!file_exists($path))
            ?  ($options['create']) 
                ? mkdir($path,0777,true) : false 
            : $path;
    }

    public static function checkout($entity, array $data = array(), array $options = array()) {
        if (!isset($data['path']))
            return false;

        $path = $data['path'];
        $commands = array();

        if (isset($data['branch'])) {
            $commands[] = "git clone {$entity->repo} $path";
        }
        elseif (isset($data['tag'])) {
            if (!$entity->path('master'))
                $commands[] = "git clone {$entity->repo} $path";
            $commands[] = "cp {$entity->repo} $path";
        }
        foreach ($commands as $cmd)
            $result = shell_exec($cmd);
        return $result;
    }

    public static function tags($entity, array $options = array()) {
        extract($options);
        // PAth better friggin be in options
        chdir($path);
        $out = shell_exec("git tag");
        return explode("\n", $out);
    }

    public static function versions($entity, array $options = array()) {
        if (($path = $entity->path('master')) == false) {
            $path = $entity->path('master', array('create' => true));
        }
        $branch = "master";
        $entity->checkout(compact('branch', 'path'));
        return $entity->tags(compact('path'));
    }
}
