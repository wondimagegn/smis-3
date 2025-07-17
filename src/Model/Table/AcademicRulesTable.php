<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

/**
 * AcademicRules Table
 */
class AcademicRulesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('academic_rules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AcademicStands', [
            'foreignKey' => 'academic_stand_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('scmo')
            ->maxLength('scmo', 4, 'SCMO must be at most 4 characters.')
            ->requirePresence('scmo', 'create')
            ->notEmptyString('scmo', 'SCMO is required.')
            ->numeric('sgpa')
            ->greaterThanOrEqual('sgpa', 0, 'SGPA must be greater than or equal to zero.')
            ->requirePresence('sgpa', 'create')
            ->notEmptyString('sgpa', 'SGPA is required.')
            ->scalar('operatorI')
            ->maxLength('operatorI', 3, 'Operator I must be at most 3 characters.')
            ->requirePresence('operatorI', 'create')
            ->notEmptyString('operatorI', 'Operator I is required.')
            ->scalar('ccmo')
            ->maxLength('ccmo', 4, 'CCMO must be at most 4 characters.')
            ->requirePresence('ccmo', 'create')
            ->notEmptyString('ccmo', 'CCMO is required.')
            ->numeric('cgpa')
            ->greaterThanOrEqual('cgpa', 0, 'CGPA must be greater than or equal to zero.')
            ->requirePresence('cgpa', 'create')
            ->notEmptyString('cgpa', 'CGPA is required.')
            ->scalar('operatorII')
            ->maxLength('operatorII', 3, 'Operator II must be at most 3 characters.')
            ->requirePresence('operatorII', 'create')
            ->notEmptyString('operatorII', 'Operator II is required.')
            ->boolean('tcw', 'TCW must be a boolean.')
            ->requirePresence('tcw', 'create')
            ->notEmptyString('tcw', 'TCW is required.')
            ->scalar('operatorIII')
            ->maxLength('operatorIII', 3, 'Operator III must be at most 3 characters.')
            ->requirePresence('operatorIII', 'create')
            ->notEmptyString('operatorIII', 'Operator III is required.')
            ->boolean('pfw', 'PFW must be a boolean.')
            ->requirePresence('pfw', 'create')
            ->notEmptyString('pfw', 'PFW is required.');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['academic_stand_id'], 'AcademicStands'), [
            'errorField' => 'academic_stand_id',
            'message' => 'The specified academic stand does not exist.'
        ]);

        return $rules;
    }

    /**
     * Checks the exclusiveness of grade rules for a given academic year
     *
     * @param string|null $academicYear Academic year
     * @return bool True if rules are exclusive, false if duplicates or overlapping ranges exist
     */
    public function checkExclusivenessOfGradeRule(?string $academicYear = null): bool
    {
        if (!$academicYear) {
            return false;
        }

        $rules = $this->find()
            ->contain(['AcademicStands' => function ($q) use ($academicYear) {
                return $q->where(['AcademicStands.academic_year_from' => $academicYear]);
            }])
            ->toArray();

        // Check for duplicate academic_stand_id or overlapping SGPA/CGPA ranges
        $standIds = [];
        $ranges = [];
        foreach ($rules as $rule) {
            if (!$rule->academic_stand || !$rule->academic_stand->id) {
                continue;
            }

            $standId = $rule->academic_stand->id;
            if (in_array($standId, $standIds)) {
                return false; // Duplicate academic stand found
            }
            $standIds[] = $standId;

            // Check for overlapping SGPA/CGPA ranges
            $sgpaRange = $this->getRange($rule->scmo, $rule->sgpa);
            $cgpaRange = $this->getRange($rule->ccmo, $rule->cgpa);

            foreach ($ranges as $existing) {
                if (
                    $this->rangesOverlap($sgpaRange, $existing['sgpa']) ||
                    $this->rangesOverlap($cgpaRange, $existing['cgpa'])
                ) {
                    return false; // Overlapping ranges found
                }
            }

            $ranges[] = ['sgpa' => $sgpaRange, 'cgpa' => $cgpaRange];
        }

        return true; // No duplicates or overlapping ranges found
    }

    /**
     * Helper method to determine range based on operator and value
     *
     * @param string $operator Operator (e.g., '>=', '<=')
     * @param float $value Value (e.g., SGPA or CGPA)
     * @return array Range array with min and max
     */
    private function getRange(string $operator, float $value): array
    {
        switch ($operator) {
            case '>=':
                return ['min' => $value, 'max' => PHP_FLOAT_MAX];
            case '<=':
                return ['min' => 0, 'max' => $value];
            case '>':
                return ['min' => $value + 0.0001, 'max' => PHP_FLOAT_MAX];
            case '<':
                return ['min' => 0, 'max' => $value - 0.0001];
            case '=':
                return ['min' => $value, 'max' => $value];
            default:
                return ['min' => $value, 'max' => $value];
        }
    }

    /**
     * Helper method to check if two ranges overlap
     *
     * @param array $range1 First range with min and max
     * @param array $range2 Second range with min and max
     * @return bool True if ranges overlap, false otherwise
     */
    private function rangesOverlap(array $range1, array $range2): bool
    {
        return $range1['min'] <= $range2['max'] && $range2['min'] <= $range1['max'];
    }
}
