<?php
class GradeSettingsController extends AppController
{
    var $name = 'GradeSettings';
    var $uses = array();

    var $menuOptions = array(
        'parent' => 'grades',
        'exclude' => array('index'),
    );

    var $components = array('EthiopicDateTime', 'AcademicYear');

    function beforeRender()
    {
        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->current_academicyear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $defaultacademicyear) {
                $defaultacademicyear = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        unset($this->request->data['User']['password']);
    }

    function index()
    {
    }
}
