<?php

use Cake\Core\Configure;

/** Statuses for the request communications and system modules. */

Configure::write('Status', [
    'STATUS_CREATED' => 'STATUS_CREATED',
    'STATUS_UPDATED' => 'STATUS_UPDATED',
    'STATUS_SENT' => 'STATUS_SENT'
]);

/**Roles ID can be used for quick reference in the code ***/

Configure::write('Roles', [
    'SYSADMIN' => 1,
    'INSTRUCTOR' => 2,
    'STUDENT' => 3,
    'REGISTRAR' => 4,
    'COLLEGE' => 5,
    'DEPARTMENT' => 6,
    'MEAL' => 7,
    'ACCOMODATION' => 9,
    'CONTINUINGANDDISTANCEEDUCTIONPROGRAM' => 10,
    'GENERAL' => 11,
    'CLEARANCE' => 12,
    'MANAGEMENT' => 13,
]);


/**Program Types ***/
Configure::write('ProgramType', [
    'REGULAR' => 1,
    'EVENING' => 2,
    'SUMMER' => 3,
    'ADVANCE_STANDING' => 4,
    'PART_TIME' => 5,
    'DISTANCE' => 6,
    'ON_LINE' => 7,
    'WEEKEND' => 8
]);
/**Program  ***/

Configure::write('Program', [
    'UNDEGRADUATE' => 1,
    'GRADUATE' => 2,
    'PhD' => 3,
    'PROGRAM_PGDT' => 4,
    'PROGRAM_REMEDIAL' => 5
]);
Configure::write('BackEntry', [
    'ACY_BACK_COURSE_ADD_DROP_APPROVAL' => 3,
    'ACY_BACK_GRADE_APPROVAL_DASHBOARD' => 2,
    'ACY_BACK_COURSE_REGISTRATION' => 5,
    'RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION' => 1
]);
Configure::write('PermissionByRole.allowedRoleIds', [2, 3]);
