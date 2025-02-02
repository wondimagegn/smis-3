<?php

class EvalutionController extends AppController
{
    public $name = "Evalution";
    public $uses = array();
    public $menuOptions = array(
        'exclude' => array('index'),
        'weight' => -10000000,
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

    }

    public function index()
    {

    }
}