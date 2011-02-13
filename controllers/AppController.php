<?php

namespace app\controllers;

class AppController extends \lithium\action\Controller {

	public function bootstrap() {
		$apps = App::all();
		return compact('apps');
	}
}
