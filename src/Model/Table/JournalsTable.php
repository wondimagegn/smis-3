<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class JournalsTable extends Table
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

        $this->setTable('journals');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Courses', [
            'foreignKey' => 'journal_id',
            'targetForeignKey' => 'course_id',
            'joinTable' => 'courses_journals',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('journal_title', 'Please provide journal title, it is required.')
            ->notEmptyString('article_title', 'Please provide journal article title, it is required.')
            ->notEmptyString('author', 'Please provide journal author, it is required.');

        return $validator;
    }

    function deleteJournalList ($course_id=null,$data=null) {
        $dontdeleteids=array();
        $deleteids=array();
        $deleteids=$this->find('list',
            array('conditions'=>array('Journal.course_id'=>$course_id),
                'fields'=>'id'));
        if (!empty($data['Journal'])) {
            foreach ($data['Journal'] as $in=>$va) {
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
                'Journal.id'=>$deleteids), false);
        }


    }

}
