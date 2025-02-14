<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class HigherEducationBackgroundsTable extends Table
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

        $this->setTable('higher_education_backgrounds');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->notEmptyString('name', 'Name is required.')
            ->notEmptyString('field_of_study', 'Field of study cannot be empty.')
            ->notEmptyString('diploma_awarded', 'Diploma/Degree awarded date is required.')
            ->notEmptyString('date_graduated', 'Date of graduation is required.')
            ->numeric('cgpa_at_graduation', 'CGPA is required.');

        return $validator;
    }

    function deleteHigherEducationList ($student_id=null,$data=null) {
        $dontdeleteids=array();
        $deleteids=array();
        $deleteids=$this->find('list',
            array('conditions'=>array('HigherEducationBackground.student_id'=>$student_id),
                'fields'=>'id'));
        if (!empty($data['HigherEducationBackground'])) {
            foreach ($data['HigherEducationBackground'] as $in=>$va) {
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
                'HigherEducationBackground.id'=>$deleteids), false);
        }


    }

}
