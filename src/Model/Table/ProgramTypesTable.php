<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProgramTypesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('program_types');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        // Add associations
        $this->belongsTo('ProgramModalities', [
            'foreignKey' => 'program_modality_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('Offers', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('ProgramTypeTransfers', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('Students', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('Sections', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('ClassPeriods', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('ProgramProgramTypeClassRooms', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('StudentStatusPatterns', [
            'foreignKey' => 'program_type_id',
        ]);

        $this->hasMany('ExamPeriods', [
            'foreignKey' => 'program_type_id',
        ]);
        $this->hasMany('AcademicCalendars', [
            'foreignKey' => 'program_type_id',
        ]);

        // Add timestamp behavior
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Program type name is required.')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Program type name must be unique.',
            ]);

        return $validator;
    }

    public function getParentProgramType($programTypeId = null)
    {
        $programTypes = $this->find('all')->toArray();

        foreach ($programTypes as $programType) {
            $equivalentToId = unserialize($programType->equivalent_to_id);
            if (is_array($equivalentToId) && in_array($programTypeId, $equivalentToId)) {
                return $programType->id;
            }
        }

        return $programTypeId;
    }
}
