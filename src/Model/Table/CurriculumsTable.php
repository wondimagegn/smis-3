<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Datasource\EntityInterface;

class CurriculumsTable extends Table
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

        $this->setTable('curriculums');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        // Associations
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DepartmentStudyPrograms', [
            'foreignKey' => 'department_study_program_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Courses', [
            'foreignKey' => 'curriculum_id',
            'dependent' => false,
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'curriculum_id',
            'dependent' => false,
        ]);

        $this->hasMany('Sections', [
            'foreignKey' => 'curriculum_id',
            'dependent' => false,
        ]);


        $this->hasMany('Attachments', [
            'foreignKey' => 'foreign_key',
            'conditions' => ['Attachments.model' => 'Curriculum'],
            'sort' => ['Attachments.created' => 'DESC'],
            'dependent' => true,
        ]);
        $this->hasMany('CourseCategories', [
            'foreignKey' => 'curriculum_id',
            'dependent' => true,
        ]);

        // Behaviors
        // Placeholder for Tools.Logable (requires a CakePHP 3.x compatible version)
        // $this->addBehavior('Tools.Logable', [
        //     'change' => 'full',
        //     'description_ids' => true,
        //     'displayField' => 'username',
        //     'foreignKey' => 'foreign_key'
        // ]);
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
            ->scalar('name')
            ->notEmptyString('name', 'Please provide curriculum name, it is required.');

        $validator
            ->scalar('certificate_name')
            ->notEmptyString('certificate_name', 'Please provide certificate name, it is required.');

        $validator
            ->scalar('year_introduced')
            ->notEmptyString('year_introduced', 'Please provide curriculum introduced year, it is required.');

        $validator
            ->scalar('type_credit')
            ->notEmptyString('type_credit', 'Please provide type of credit, it is required.');

        $validator
            ->scalar('amharic_degree_nomenclature')
            ->notEmptyString('amharic_degree_nomenclature', 'Please provide amharic degree nomenclature, it is required.');

        $validator
            ->scalar('english_degree_nomenclature')
            ->notEmptyString('english_degree_nomenclature', 'Please provide english degree nomenclature, it is required.');

        $validator
            ->integer('program_id')
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Please select program, it is required.');

        $validator
            ->integer('minimum_credit_points')
            ->requirePresence('minimum_credit_points', 'create')
            ->notEmptyString('minimum_credit_points', 'Please provide minimum credit points, it is required.')
            ->greaterThanOrEqual('minimum_credit_points', 0, 'Please provide valid minimum credit point, greater than or equal to zero.')
            ->add('minimum_credit_points', 'sumCreditCurriculum', [
                'rule' => [$this, 'sumCreditCurriculum'],
                'message' => 'The minimum credit point should be less than or equal to the sum of the course category total credit, please adjust.'
            ])
            ->add('minimum_credit_points', 'sumMandatoryCredit', [
                'rule' => [$this, 'sumMandatoryCredit'],
                'message' => 'The sum of the mandatory credit should be equal to minimum credit points, please adjust.'
            ]);

        return $validator;
    }

    /**
     * Virtual field equivalent for curriculum_detail
     *
     * @param EntityInterface $entity The entity to compute the virtual field for.
     * @return string|null
     */
    public function _getCurriculumDetail(EntityInterface $entity): ?string
    {
        return $entity->name . ' - ' . $entity->year_introduced;
    }

    /**
     * Custom validation rule for sumCreditCurriculum
     *
     * @param mixed $value The value to validate.
     * @param array $context The validation context.
     * @return bool
     */
    public function sumCreditCurriculum($value, array $context): bool
    {
        $sumCourseCategory = 0;
        if (!empty($context['data']['CourseCategory'])) {
            foreach ($context['data']['CourseCategory'] as $category) {
                $sumCourseCategory += $category['total_credit'] ?? 0;
            }
        }
        return $sumCourseCategory >= $value;
    }

    /**
     * Custom validation rule for sumMandatoryCredit
     *
     * @param mixed $value The value to validate.
     * @param array $context The validation context.
     * @return bool
     */
    public function sumMandatoryCredit($value, array $context): bool
    {
        $sumCourseCategory = 0;
        if (!empty($context['data']['CourseCategory'])) {
            foreach ($context['data']['CourseCategory'] as $category) {
                $sumCourseCategory += $category['mandatory_credit'] ?? 0;
            }
        }
        return $sumCourseCategory == $value;
    }

    /**
     * Checks if a curriculum can be deleted
     *
     * @param int|null $curriculumId The curriculum ID to check.
     * @return bool
     */
    public function canItBeDeleted(?int $curriculumId = null): bool
    {
        if ($curriculumId === null) {
            return false;
        }

        if ($this->Courses->find()->where(['Courses.curriculum_id' => $curriculumId])->count() > 0) {
            return false;
        }
        if ($this->Students->find()->where(['Students.curriculum_id' => $curriculumId])->count() > 0) {
            return false;
        }
        // Commented out as in original
        /*
        if ($this->Attachments->find()->where(['Attachments.model' => 'Curriculum', 'Attachments.foreign_key' => $curriculumId])->count() > 0) {
            return false;
        }
        if ($this->CourseCategories->find()->where(['CourseCategories.curriculum_id' => $curriculumId])->count() > 0) {
            return false;
        }
        */
        return true;
    }

    /**
     * Organizes courses by year and semester
     *
     * @param array|null $data The curriculum data including courses.
     * @return array|null
     */
    public function organizedCourseOfCurriculumByYearSemester(?array $data = null): ?array
    {

        if (empty($data['id'])) {
            return $data;
        }

        $coursesOrganizedByYear = [];
        foreach ($data['courses'] as $course) {

            if (empty($course['courses'])) {
                $yearName = $course['year_level']['name'] ?? 'Unknown';
                $semester = $course['semester'] ?? 'Unknown';
                $coursesOrganizedByYear[$yearName][$semester][] = $course;
            }
        }

        $data['courses'] = $coursesOrganizedByYear;


        return $data;
    }

    /**
     * Prepares attachment data for saving
     *
     * @param array|null $data The data containing attachments.
     * @return array|null
     */
    public function preparedAttachment(?array $data = null): ?array
    {
        if (!empty($data['Attachment'])) {
            foreach ($data['Attachment'] as $index => &$attachment) {
                if (empty($attachment['file']['name']) && empty($attachment['file']['type']) && empty($attachment['file']['tmp_name'])) {
                    unset($data['Attachment'][$index]);
                } else {
                    $attachment['model'] = 'Curriculum';
                    $attachment['group'] = 'attachment';
                }
            }
        }
        return $data;
    }

    /**
     * Gets department study program details
     *
     * @param int|null $departmentId The department ID.
     * @param int|null $programModalityId The program modality ID.
     * @param int|null $qualificationId The qualification ID.
     * @return array
     */
    public function getDepartmentStudyProgramDetails(?int $departmentId = null, ?int $programModalityId = null, ?int $qualificationId = null): array
    {
        $conditions = [];
        if ($departmentId) {
            $conditions['DepartmentStudyPrograms.department_id'] = $departmentId;
        }
        if ($programModalityId) {
            $conditions['DepartmentStudyPrograms.program_modality_id'] = $programModalityId;
        }
        if ($qualificationId) {
            $conditions['DepartmentStudyPrograms.qualification_id'] = $qualificationId;
        }

        $departmentStudyProgramDetails = [];
        $departmentStudyProgramListForSelect = [];

        if (!empty($conditions)) {
            $departmentStudyProgramDetails = $this->DepartmentStudyPrograms->find()
                ->select([
                    'DepartmentStudyPrograms.id',
                    'DepartmentStudyPrograms.study_program_id',
                    'StudyPrograms.id',
                    'StudyPrograms.study_program_name',
                    'StudyPrograms.code',
                    'ProgramModalities.id',
                    'ProgramModalities.modality',
                    'ProgramModalities.code',
                    'Qualifications.id',
                    'Qualifications.qualification',
                    'Qualifications.code'
                ])
                ->contain([
                    'StudyPrograms' => ['fields' => ['id', 'study_program_name', 'code']],
                    'ProgramModalities' => ['fields' => ['id', 'modality', 'code']],
                    'Qualifications' => ['fields' => ['id', 'qualification', 'code']]
                ])
                ->where($conditions)
                ->toArray();
        }

        foreach ($departmentStudyProgramDetails as $dsp) {
            $departmentStudyProgramListForSelect[$dsp->id] = sprintf(
                '%s (%s) => %s (%s) => %s (%s)',
                $dsp->study_program->study_program_name,
                $dsp->study_program->code,
                $dsp->program_modality->modality,
                $dsp->program_modality->code,
                $dsp->qualification->qualification,
                $dsp->qualification->code
            );
        }

        return $departmentStudyProgramListForSelect;
    }
}
