<?php

namespace app\models\repo;

class Git extends \lithium\core\Object {

    protected $_config = array(
        'path' => ''
    );

    public function __construct($config) {
        $config += $this->_config;
        parent::__construct($config);
    }

    private function _path($data = array()) {
        $data += array(
            'branch' => 'master',
            'tag' => false,
            'name' => false,
            'dir' => false
        ) + $this->_config;

        if ($data['dir']) {
            return join("/", array(
                $data['path'], 
                $data['dir']
            ));
        }
        elseif ($data['name']) {
            return join("/", array(
                $data['path'], 
                "deploy", 
                $data['name']
            ));
        }
        elseif ($data['tag']) {
            return join("/", array(
                $data['path'], 
                "tags", 
                $data['tag']
            ));
        }
        elseif ($data['branch']) {
            return join("/", array(
                $data['path'], 
                "branches", 
                $data['branch']
            ));
        }
    }

    /**
     * Perform a checkout, should normally happen just once for each repo
     * @param array $data Indicates where to check out and in what starting state
     *              - `path` Where to check out to
     *              - `branch` Check out a new branch
     *              - `tag` Check out a new tag
     * @return bool
     */
    public function checkout(array $data = array(), array $options = array()) {
        $data += array(
            'branch' => 'master'
        ) + $this->_config;
        extract($data);

        if (isset($tag)) {
            $name = ($name) ?: $tag;
            $from = $this->_path(compact('branch'));
            $to = $this->_path(compact('tag'));
            $link = $this->_path(compact('name'));

            mkdir($to, 0777, true);
            shell_exec("cp -r {$from}/* {$to}/");
            chdir($to);
            shell_exec("git reset --hard {$tag}");

            // Create symlink
            chdir($this->_path(array('dir' => 'deploy')));
            symlink($to, $name);
        }
        elseif (isset($branch)) {
            $to = $this->_path(compact('branch'));
            if (!file_exists($to)) {
                $command = "git clone {$url} {$to}";
                $ok = (strpos(shell_exec($command), 'Initialized') !== false);
                chdir($to);
                shell_exec("git checkout {$branch}");
                return $ok;
            }
            else {
                if ($pull) {
                    chdir($to);
                    return shell_exec("git pull --all");
                }
                return true;
            }
        }
    }

    public function ls($dir) {
        $path = $this->_path(compact('dir'));
        $files = scandir($path);
        return compact('files', 'path');
    }
    public function tags(array $data = array(), array $options = array()) {
        $data += array(
            'branch' => 'master'
        ) + $this->_config;
        extract($data);

        $path = $this->_path(compact('branch'));
        chdir($path);
        $out = shell_exec("git tag");
        $tags = array_filter(explode("\n", $out));
        return $tags;
    }
}
