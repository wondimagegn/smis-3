<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class BooksTable extends Table
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

        $this->setTable('books');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
            'propertyName' => 'Course',
        ]);
        $this->belongsToMany('Courses', [
            'foreignKey' => 'book_id',
            'targetForeignKey' => 'course_id',
            'joinTable' => 'courses_books',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('ISBN')
            ->maxLength('ISBN', 100)
            ->requirePresence('ISBN', 'create')
            ->notEmptyString('ISBN');

        $validator
            ->scalar('publisher')
            ->maxLength('publisher', 200)
            ->allowEmptyString('publisher');

        $validator
            ->scalar('place_of_publication')
            ->maxLength('place_of_publication', 100)
            ->allowEmptyString('place_of_publication');

        $validator
            ->scalar('edition')
            ->maxLength('edition', 15)
            ->allowEmptyString('edition');

        $validator
            ->scalar('author')
            ->maxLength('author', 100)
            ->allowEmptyString('author');

        $validator
            ->scalar('year_of_publication')
            ->allowEmptyString('year_of_publication');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {

        $rules->add($rules->existsIn(['course_id'], 'Courses'));

        return $rules;
    }

    public function deleteBookList($course_id = null, $data = null)
    {

        $dontdeleteids = array();
        $deleteids = array();
        $deleteids = $this->find(
            'list',
            array(
                'conditions' => array('Book.course_id' => $course_id),
                'fields' => 'id'
            )
        );
        if (!empty($data['Book'])) {
            foreach ($data['Book'] as $in => $va) {
                if (!empty($va['id'])) {
                    if (in_array($va['id'], $deleteids)) {
                        $dontdeleteids[] = $va['id'];
                    }
                }
            }
        }
        if (!empty($dontdeleteids)) {
            foreach ($deleteids as $in => &$va) {
                if (in_array($va, $dontdeleteids)) {
                    unset($deleteids[$in]);
                }
            }
        }


        if (!empty($deleteids)) {
            $this->deleteAll(array(
                'Book.id' => $deleteids
            ), false);
        }
    }
}
