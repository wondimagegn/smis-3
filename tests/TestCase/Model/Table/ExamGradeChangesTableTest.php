<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ExamGradeChangesTableTest extends TestCase
{
    protected $ExamGradeChanges;

    public function setUp()
    {
        parent::setUp();
        $this->ExamGradeChanges = TableRegistry::getTableLocator()->get('ExamGradeChanges');
    }

    public function testGetListOfGradeChangeForRegistrarApproval()
    {
        $result = $this->ExamGradeChanges->getListOfGradeChangeForRegistrarApproval([131], null, null, null);
        $this->assertIsArray($result, 'Result should be an array');
        if (!empty($result)) {
            $first = reset(reset(reset(reset($result))));
            $this->assertArrayHasKey('ExamGradeChange', $first, 'Result should include ExamGradeChange data');
            $this->assertArrayHasKey('Student', $first, 'Result should include Student data');
            $this->assertArrayHasKey('ExamGrade', $first, 'Result should include ExamGrade data');
            if (!empty($first['ExamGrade'])) {
                $examGrade = reset($first['ExamGrade']);
                $this->assertArrayHasKey('department_approved_by_name', $examGrade, 'Should include department_approved_by_name');
                $this->assertArrayHasKey('registrar_approved_by_name', $examGrade, 'Should include registrar_approved_by_name');
            }
        }
    }
    public function testGetListOfMakeupGradeChangeForRegistrarApproval()
    {
        $result = $this->ExamGradeChanges->getListOfMakeupGradeChangeForRegistrarApproval([131], null, null, null);
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('summary', $result, 'Result should have a summary key');
        $this->assertArrayHasKey('count', $result, 'Result should have a count key');
        $this->assertIsInt($result['count'], 'Count should be an integer');

        if (!empty($result['summary'])) {
            $first = reset(reset(reset(reset($result['summary']))));
            $this->assertArrayHasKey('ExamGradeChange', $first, 'Result should include ExamGradeChange data');
            $this->assertArrayHasKey('Student', $first, 'Result should include Student data');
            $this->assertArrayHasKey('ExamGrade', $first, 'Result should include ExamGrade data');
            $this->assertArrayHasKey('ExamCourse', $first, 'Result should include ExamCourse data');
            $this->assertArrayHasKey('ExamSection', $first, 'Result should include ExamSection data');
            $this->assertArrayHasKey('MakeupExam', $first, 'Result should include MakeupExam data');
            if (!empty($first['ExamGrade'])) {
                $examGrade = reset($first['ExamGrade']);
                $this->assertArrayHasKey('department_approved_by_name', $examGrade, 'Should include department_approved_by_name');
                $this->assertArrayHasKey('registrar_approved_by_name', $examGrade, 'Should include registrar_approved_by_name');
            }
            // Verify count matches the number of entries
            $count = 0;
            foreach ($result['summary'] as $college) {
                foreach ($college as $department) {
                    foreach ($department as $program) {
                        foreach ($program as $programType) {
                            $count += count($programType);
                        }
                    }
                }
            }
            $this->assertEquals($result['count'], $count, 'Count should match the number of summary entries');
        } else {
            $this->assertEquals(0, $result['count'], 'Count should be 0 when summary is empty');
        }
    }

    public function testGetListOfMakeupGradeChangeByDepartmentForRegistrarApproval()
    {
        $result = $this->ExamGradeChanges->getListOfMakeupGradeChangeByDepartmentForRegistrarApproval([131], null, null, null);
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('summary', $result, 'Result should have a summary key');
        $this->assertArrayHasKey('count', $result, 'Result should have a count key');
        $this->assertIsInt($result['count'], 'Count should be an integer');

        if (!empty($result['summary'])) {
            $first = reset(reset(reset(reset($result['summary']))));
            $this->assertArrayHasKey('ExamGradeChange', $first, 'Result should include ExamGradeChange data');
            $this->assertArrayHasKey('Student', $first, 'Result should include Student data');
            $this->assertArrayHasKey('ExamGrade', $first, 'Result should include ExamGrade data');
            $this->assertArrayHasKey('Course', $first, 'Result should include Course data');
            $this->assertArrayHasKey('Section', $first, 'Result should include Section data');
            if (!empty($first['ExamGrade'])) {
                $examGrade = reset($first['ExamGrade']);
                $this->assertArrayHasKey('department_approved_by_name', $examGrade, 'Should include department_approved_by_name');
                $this->assertArrayHasKey('registrar_approved_by_name', $examGrade, 'Should include registrar_approved_by_name');
            }
            // Verify count matches the number of entries
            $count = 0;
            foreach ($result['summary'] as $college) {
                foreach ($college as $department) {
                    foreach ($department as $program) {
                        foreach ($program as $programType) {
                            $count += count($programType);
                        }
                    }
                }
            }
            $this->assertEquals($result['count'], $count, 'Count should match the number of summary entries');
        } else {
            $this->assertEquals(0, $result['count'], 'Count should be 0 when summary is empty');
        }
    }
}
