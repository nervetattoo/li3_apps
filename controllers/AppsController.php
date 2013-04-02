<?php

namespace app\controllers;

use \app\models\App;
use \app\models\Deploy;

class AppsController extends AppController {

	public function index() {
		$apps = App::all();
		return compact('apps');
	}

	public function view() {
		$app = App::first($this->request->id);
        $deployed = $app->deployed();
		return compact('app', 'deployed');
	}

	public function add() {
        if ($this->request->id)
            $app = App::find($this->request->id);
        else
            $app = App::create();

		if (($this->request->data) && $app->save($this->request->data)) {
            $app->bootstrap();
			$this->redirect(array('Apps::view', 'args' => array($app->_id)));
		}
		return compact('app');
	}

    public function deploy() {
        /**
         * POST /apps/deploy -d
         * {
         *   branch : master,
         *   ref : tag/commit-hash,
         *   name : foobar
         * }
         * Creates :
         * $appPath/deploy/foobar/ <- full repo
         */
        $id = $this->request->id;
		$app = App::first($id);

        if ($this->request->data) {
            $deploy = $app->deploy($this->request->data);
            $repo = $app->repo();
            $deploy->checkout(compact('repo'));
            $this->redirect('Apps::view', array('args' => compact('id')));
        }

        $refs = array();
        foreach ($app->repo()->refs(100) as $r) {
            extract($r);
            $refs[$hash] = "$date : $message";
        }
        $branches = $app->repo()->branches();
        //$versions = $app->versions();
        $deployed = $app->deployed();
        if (!count($deployed)) {
            $defaults = array(
                'target' => "/home/apps/" . strtolower($app->title),
                'name' => 'default',
            );
        }
		return compact('app', 'refs', 'defaults');
    }
}
