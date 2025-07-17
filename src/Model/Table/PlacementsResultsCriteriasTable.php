<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PlacementsResultsCriteriaTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placements_results_criteria');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('ReservedPlaces', [
            'foreignKey' => 'placements_results_criteria_id',
            'dependent' => false,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('result_from', 'Enter number required')
            ->notEmptyString('result_from', 'Enter number required')
            ->numeric('result_to', 'Enter number required')
            ->notEmptyString('result_to', 'Enter number required')
            ->notEmptyString('admissionyear', 'Select Academic Year.')
            ->notEmptyString('name', 'Enter name.');

        return $validator;
    }

    public function isPlacementResultRecorded(?string $academicyear = null, ?int $college_id = null): int
    {
        if ($college_id) {
            $isRecorded = $this->find('count')
                ->where([
                    'PlacementsResultsCriteria.college_id' => $college_id,
                    'PlacementsResultsCriteria.admissionyear LIKE' => $academicyear . '%'
                ])
                ->disableHydration()
                ->count();

            return $isRecorded ? 1 : 0;
        }

        return 0;
    }

    public function isReservedPlaceRecorded(?string $academicyear = null, ?int $college_id = null): int
    {
        if ($college_id) {
            $isRecorded = $this->ReservedPlaces->find()
                ->where([
                    'ReservedPlaces.college_id' => $college_id,
                    'ReservedPlaces.academicyear LIKE' => $academicyear . '%'
                ])
                ->disableHydration()
                ->count();

            return $isRecorded ? 1 : 0;
        }

        return 0;
    }

    public function isParticipationgDepartmentRecorded(?string $academicyear = null, ?int $college_id = null): int
    {
        if ($college_id) {
            $isRecorded = TableRegistry::getTableLocator()->get('ParticipatingDepartments')->find('count')
                ->where([
                    'ParticipatingDepartments.college_id' => $college_id,
                    'ParticipatingDepartments.academic_year LIKE' => $academicyear . '%'
                ])
                ->disableHydration()
                ->count();

            return $isRecorded ? 1 : 0;
        }

        return 0;
    }

    public function isPrepartoryResult(?string $academicyear = null, ?int $college_id = null): int
    {
        if ($college_id) {
            $isDefined = $this->find('first')
                ->where([
                    'PlacementsResultsCriteria.admissionyear LIKE' => $academicyear . '%',
                    'PlacementsResultsCriteria.college_id' => $college_id
                ])
                ->disableHydration()
                ->first();

            $isPrepartory = $this->find('count')
                ->where([
                    'PlacementsResultsCriteria.admissionyear LIKE' => $academicyear . '%',
                    'PlacementsResultsCriteria.college_id' => $college_id,
                    'PlacementsResultsCriteria.prepartory_result' => 1
                ])
                ->disableHydration()
                ->count();

            if ($isPrepartory) {
                return 1;
            }

            return !empty($isDefined) ? 0 : 1;
        }

        return 1;
    }

    public function isPrepartoryResult2(?string $academicyear = null, ?int $college_id = null): ?int
    {
        if ($college_id) {
            $isPrepartory = $this->find('first')
                ->where([
                    'PlacementsResultsCriteria.admissionyear LIKE' => $academicyear . '%',
                    'PlacementsResultsCriteria.college_id' => $college_id
                ])
                ->disableHydration()
                ->first();

            if (empty($isPrepartory)) {
                return -1;
            }

            return $isPrepartory['PlacementsResultsCriteria']['prepartory_result'] == 1 ? 1 : 0;
        }

        return null;
    }

    public function reservedPlaceCategory(?string $academicyear = null, ?int $college_id = null, ?int $department_id = null): array
    {
        if ($college_id) {
            return $this->ReservedPlaces->find()
                ->where([
                    'ReservedPlaces.college_id' => $college_id,
                    'ReservedPlaces.academicyear LIKE' => $academicyear . '%',
                    'ReservedPlaces.participating_department_id' => $department_id
                ])
                ->contain(['PlacementsResultsCriteria'])
                ->disableHydration()
                ->all()
                ->toArray();
        }

        return [];
    }

    public function reservedPlaceForDepartmentByGradeRange(?string $academicyear = null, ?int $college_id = null, ?int $department_id = null): array
    {
        if ($college_id) {
            return $this->ReservedPlaces->find()
                ->where([
                    'ReservedPlaces.college_id' => $college_id,
                    'ReservedPlaces.academicyear LIKE' => $academicyear . '%',
                    'ReservedPlaces.participating_department_id' => $department_id
                ])
                ->contain(['PlacementsResultsCriteria'])
                ->disableHydration()
                ->all()
                ->toArray();
        }

        return [];
    }

    public function resultCategoryInput(?array $data = null, ?float $max = null, ?float $min = null): bool
    {
        $maxtmp = 0;
        $mintmp = 0;

        if (!$max || !$min) {
            $result_type = $data['prepartory_result'];
            $is_preparatory = $result_type ? 'EHEECE_total_results' : 'freshman_result';

            $maxtmp = TableRegistry::getTableLocator()->get('AcceptedStudents')->find()
                ->select(["MAX({$is_preparatory})"])
                ->where([
                    'AcceptedStudents.college_id' => $data['college_id'],
                    'AcceptedStudents.academicyear' => $data['admissionyear']
                ])
                ->disableHydration()
                ->first()[0]["MAX({$is_preparatory})"] ?? 0;

            $mintmp = TableRegistry::getTableLocator()->get('AcceptedStudents')->find()
                ->select(["MIN({$is_preparatory})"])
                ->where([
                    'AcceptedStudents.college_id' => $data['college_id'],
                    'AcceptedStudents.academicyear' => $data['admissionyear']
                ])
                ->disableHydration()
                ->first()[0]["MIN({$is_preparatory})"] ?? 0;
        } else {
            $maxtmp = $max;
            $mintmp = $min;
        }

        if (empty($data['name'])) {
            $this->validationErrors['result_criteria_name'] = 'Please enter the name for the result category.';
            return false;
        } elseif (empty($data['result_from'])) {
            $this->validationErrors['result_from'] = 'Please enter the result from.';
            return false;
        } elseif (empty($data['result_to'])) {
            $this->validationErrors['result_to'] = 'Please enter the result to';
            return false;
        } elseif (!$this->checkUnique($data)) {
            $this->validationErrors['result_criteria_name'] = 'The name should be unique, please change to other name.';
            return false;
        }

        if (!empty($data['result_from']) && !empty($data['result_to'])) {
            if ($maxtmp != '' && $mintmp != '') {
                if ($data['result_from'] > $maxtmp || $data['result_from'] < $mintmp) {
                    $this->validationErrors['result_from_problem'] = "The 'result from' should be less than or equal to {$maxtmp} result and greater than or equal to {$mintmp}.";
                    return false;
                }

                if ($data['result_to'] > $maxtmp || $data['result_to'] < $mintmp) {
                    $this->validationErrors['result_to_problem'] = "The 'result to' should be less than or equal to {$maxtmp} and greater than or equal to {$mintmp}.";
                    return false;
                }
            }

            if ($data['result_to'] < $data['result_from']) {
                $this->validationErrors['result_from_to'] = "The 'result from' should be less than the 'result to'.";
                return false;
            }

            $check_no_entry = $this->find('count')
                ->where([
                    'PlacementsResultsCriteria.college_id' => $data['college_id'],
                    'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
                ])
                ->disableHydration()
                ->count();

            if ($check_no_entry != 0) {
                $already_recorded_range = $this->find()
                    ->where([
                        'PlacementsResultsCriteria.college_id' => $data['college_id'],
                        'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
                    ])
                    ->disableHydration()
                    ->all()
                    ->toArray();

                foreach ($already_recorded_range as $sr) {
                    $sr = $sr['PlacementsResultsCriteria'];
                    if (
                        ($data['result_from'] <= $sr['result_from'] && $sr['result_from'] <= $data['result_to']) ||
                        ($data['result_from'] <= $sr['result_to'] && $sr['result_to'] <= $data['result_to']) ||
                        ($sr['result_from'] <= $data['result_from'] && $data['result_to'] <= $sr['result_to']) ||
                        ($data['result_from'] <= $sr['result_from'] && $sr['result_to'] <= $data['result_to'])
                    ) {
                        $this->validationErrors['result_from_to'] = "The given grade range is not unique. Please make sure that 'result from' and/or 'result to' is not already recorded.";
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getListOfGradeCategory(?string $academicyear = null, ?int $college_id = null): array
    {
        return $this->find()
            ->select([
                'PlacementsResultsCriteria.id',
                'PlacementsResultsCriteria.result_from',
                'PlacementsResultsCriteria.result_to'
            ])
            ->where([
                'PlacementsResultsCriteria.college_id' => $college_id,
                'PlacementsResultsCriteria.admissionyear LIKE' => $academicyear . '%'
            ])
            ->disableHydration()
            ->all()
            ->toArray();
    }

    public function gradeRangeContinuty(?array $data = null): bool
    {
        $check_no_entry = $this->find('count')
            ->where([
                'PlacementsResultsCriteria.college_id' => $data['college_id'],
                'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
            ])
            ->disableHydration()
            ->count();

        if ($check_no_entry != 0) {
            $min_from_all = $this->find()
                ->select(['MIN(result_from)'])
                ->where([
                    'PlacementsResultsCriteria.college_id' => $data['college_id'],
                    'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
                ])
                ->disableHydration()
                ->first();

            $min = $min_from_all[0]['MIN(result_from)'] ?? 0;

            if ($data['result_to'] < $min && $data['result_to'] > $data['result_from']) {
                return true;
            }

            $this->validationErrors['grade_range_continuty'] = "The result To should be less than {$min}.";
            return false;
        }

        return true;
    }

    public function checkUnique(?array $data = null): bool
    {
        $count = $this->find('count')
            ->where([
                'PlacementsResultsCriteria.college_id' => $data['college_id'],
                'PlacementsResultsCriteria.admissionyear' => $data['admissionyear'],
                'PlacementsResultsCriteria.name' => $data['name']
            ])
            ->disableHydration()
            ->count();

        return $count === 0;
    }

    public function getPlacementResultCriteria(int $college_id, string $academicYear): array
    {
        $placementResultCriteria = $this->find()
            ->where([
                'PlacementsResultsCriteria.college_id' => $college_id,
                'PlacementsResultsCriteria.admissionyear' => $academicYear
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $resultList = ['all' => 'All'];
        foreach ($placementResultCriteria as $v) {
            $resultList[$v['id']] = "{$v['name']} ({$v['result_from']}-{$v['result_to']})";
        }

        return $resultList;
    }
}
