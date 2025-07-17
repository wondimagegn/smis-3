<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Books Table
 */
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

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'LEFT',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('title')
            ->requirePresence('title', 'create')
            ->notEmptyString('title', 'Please provide book title, it is required.');

        return $validator;
    }

    /**
     * Deletes books for a course except those specified in data
     *
     * @param int|null $courseId Course ID
     * @param array|null $data Book data with IDs to keep
     * @return void
     */
    public function deleteBookList($courseId = null, $data = null)
    {
        if (!$courseId) {
            return;
        }

        $deleteIds = $this->find('list')
            ->where(['Books.course_id' => $courseId])
            ->toArray();

        if (empty($deleteIds)) {
            return;
        }

        $dontDeleteIds = [];
        if (!empty($data['Book'])) {
            foreach ($data['Book'] as $book) {
                if (!empty($book['id']) && in_array($book['id'], $deleteIds)) {
                    $dontDeleteIds[] = $book['id'];
                }
            }
        }

        $idsToDelete = array_diff($deleteIds, $dontDeleteIds);

        if (!empty($idsToDelete)) {
            $this->deleteAll(['Books.id IN' => $idsToDelete]);
        }
    }
}
