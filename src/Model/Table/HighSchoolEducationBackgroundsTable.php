<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class HighSchoolEducationBackgroundsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('high_school_education_backgrounds');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('name', 'Name is required field.')
            ->notEmptyString('town', 'Town is required field.')
            ->notEmptyString('zone', 'Zone is required.')
            ->notEmptyString('school_level', 'School level is required.');

        return $validator;
    }

    function deleteHighSchoolEducationBackgroundList ($student_id=null,$data=null) {
        $dontdeleteids=array();
        $deleteids=array();
        $deleteids=$this->find('list',
            array('conditions'=>array('HighSchoolEducationBackground.student_id'=>$student_id),
                'fields'=>'id'));

        if (!empty($data['HighSchoolEducationBackground'])) {
            foreach ($data['HighSchoolEducationBackground'] as $in=>$va) {
                if (!empty($va['id'])) {
                    if (in_array($va['id'],$deleteids)) {
                        $dontdeleteids[]=$va['id'];
                    }

                }
            }

        }
        if (!empty($dontdeleteids)) {
            foreach ($deleteids as $in=>&$va) {
                if (in_array($va,$dontdeleteids)) {
                    unset($deleteids[$in]);
                }
            }
        }

        if (!empty($deleteids)) {
            $this->deleteAll(array(
                'HighSchoolEducationBackground.id'=>$deleteids), false);
        }


    }

}
