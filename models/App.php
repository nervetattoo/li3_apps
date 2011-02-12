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
}
