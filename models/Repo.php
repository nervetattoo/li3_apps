<?php

namespace app\models;

class Repo extends \lithium\core\StaticObject {

	/**
	 * Class dependencies.
	 *
	 * @var array
	 */
	protected static $_classes = array(
		'git' => 'app\models\repo\Git',
	);

	/**
	 * Placeholder
	 *
	 * @param array $options
	 * @return void
	 */
	public static function __init() {
	}

	/**
     * Open the specific repo existing at a given path
     * Will determine what VCS is in use for the path
     * and return an object for that VCS
	 *
	 * @param string $path
	 * @param array $options
	 * @return Object 
	 */
	public static function fromPath($path, array $options = array()) {
        if (!file_exists($path)) {
            throw new \RuntimeException("Filepath $path does not exist");
        }
        $files = scandir($path);
        $vcs = false;
        $regex = "/\.(git|hz|svn)/";
        while (!$vcs && ($file = next($files))) {
            if (preg_match($regex, $file, $matches))
                $vcs = $matches[1];
        }
        $config = compact('path');
        return (!$vcs) ? false : new static::$_classes[$vcs]($config);
	}

	/**
     * Open the specific repo as the repo url states
	 *
	 * @param string $url
	 * @param array $options
	 * @return Object 
	 */
	public static function fromRemote($url, array $options = array()) {
        $vcs = "git";
        $config = compact('url') + $options;
        return (!$vcs) ? false : new static::$_classes[$vcs]($config);
	}
}
