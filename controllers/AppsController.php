<?php

namespace app\controllers;

use \app\models\App;

class AppsController extends AppController {

	public function index() {
		$apps = App::all();
		return compact('apps');
	}

	public function view() {
		$app = App::first($this->request->id);
        $versions = $app->versions();
        /*
        $app->deploy(array(
            'tag' => 'HEAD',
            'name' => 'gold'
        ));
         */
        $deployed = $app->deployed();
		return compact('app', 'versions', 'deployed');
	}

	public function add() {
		$app = App::create();

		if (($this->request->data) && $app->save($this->request->data)) {
            $app->bootstrap();
			$this->redirect(array('Apps::view', 'args' => array($app->_id)));
		}
		return compact('app');
	}

	public function edit() {
		$app = App::find($this->request->id);

		if (!$app) {
			$this->redirect('Apps::index');
		}
		if (($this->request->data) && $app->save($this->request->data)) {
			$this->redirect(array('Apps::view', 'args' => array($app->_id)));
		}
		return compact('app');
	}
}

?>
