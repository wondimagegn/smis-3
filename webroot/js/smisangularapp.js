var dashboardApp = angular.module('dashboardApp', []);

// Loading directive with conditional visibility
dashboardApp.directive('loadingAjax', function () {
    return {
        restrict: 'E',
        template: '<img src="/img/busy.gif" class="displayed" ng-if="isLoading">',
        scope: {
            isLoading: '=' // Bind to a scope variable
        }
    };
});

// Remove loading service (optional, kept for compatibility but not used in new approach)
dashboardApp.service('removeLoading', function () {
    this.removeLoadingElement = function (elem) {
        console.warn('removeLoading service is deprecated. Use ng-if with isLoading instead.');
        var myEl = angular.element(document.getElementById(elem).getElementsByTagName('loading-ajax'));
        myEl.remove();
    };
});

// Number of pages service (unchanged, as it’s unrelated but functional)
dashboardApp.service('numberOfPageService', function () {
    this.numberOfPagesValue = function (jsondata, pageSize) {
        if (jsondata && jsondata.length) {
            return Math.ceil(jsondata.length / pageSize);
        }
        return 0;
    };
});

// Grade change controller (aligned with provided HTML)
dashboardApp.controller('gradeChangeController', ['$scope', '$http', '$filter', function ($scope, $http, $filter) {
    // Initialize scope variables
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.exam_grade_change_requests = 0;
    $scope.exam_grade_changes_for_college_approval = 0;
    $scope.makeup_exam_grades = 0;
    $scope.rejected_makeup_exams = 0;
    $scope.rejected_supplementary_exams = 0;
    $scope.fm_exam_grade_change_requests = 0;
    $scope.fm_makeup_exam_grades = 0;
    $scope.fm_rejected_makeup_exams = 0;
    $scope.fm_rejected_supplementary_exams = 0;
    $scope.reg_exam_grade_change_requests = 0;
    $scope.reg_makeup_exam_grades = 0;
    $scope.reg_supplementary_exam_grades = 0;

    // Function to check if a value is not zero or undefined/null
    $scope.isNotZeroOrUndefined = function (value) {
        return angular.isDefined(value) && value !== null && !isNaN(value) && Number(value) > 0;
    };

    // Function to check if all counts are zero or undefined
    $scope.isEmpty = function () {
        return !$scope.isNotZeroOrUndefined($scope.exam_grade_change_requests) &&
            !$scope.isNotZeroOrUndefined($scope.exam_grade_changes_for_college_approval) &&
            !$scope.isNotZeroOrUndefined($scope.makeup_exam_grades) &&
            !$scope.isNotZeroOrUndefined($scope.rejected_makeup_exams) &&
            !$scope.isNotZeroOrUndefined($scope.rejected_supplementary_exams) &&
            !$scope.isNotZeroOrUndefined($scope.fm_exam_grade_change_requests) &&
            !$scope.isNotZeroOrUndefined($scope.fm_makeup_exam_grades) &&
            !$scope.isNotZeroOrUndefined($scope.fm_rejected_makeup_exams) &&
            !$scope.isNotZeroOrUndefined($scope.fm_rejected_supplementary_exams) &&
            !$scope.isNotZeroOrUndefined($scope.reg_exam_grade_change_requests) &&
            !$scope.isNotZeroOrUndefined($scope.reg_makeup_exam_grades) &&
            !$scope.isNotZeroOrUndefined($scope.reg_supplementary_exam_grades);
    };

    // Fetch grade change data
    $scope.getAll = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/getApprovalRejectGradeChange'
        }).then(function (response) {
            // Initialize to 0 to avoid undefined errors
            $scope.exam_grade_change_requests = Number(response.data.exam_grade_change_requests) || 0;
            $scope.exam_grade_changes_for_college_approval = Number(response.data.exam_grade_changes_for_college_approval) || 0;
            $scope.makeup_exam_grades = Number(response.data.makeup_exam_grades) || 0;
            $scope.rejected_makeup_exams = Number(response.data.rejected_makeup_exams) || 0;
            $scope.rejected_supplementary_exams = Number(response.data.rejected_supplementary_exams) || 0;
            $scope.fm_exam_grade_change_requests = Number(response.data.fm_exam_grade_change_requests) || 0;
            $scope.fm_makeup_exam_grades = Number(response.data.fm_makeup_exam_grades) || 0;
            $scope.fm_rejected_makeup_exams = Number(response.data.fm_rejected_makeup_exams) || 0;
            $scope.fm_rejected_supplementary_exams = Number(response.data.fm_rejected_supplementary_exams) || 0;
            $scope.reg_exam_grade_change_requests = Number(response.data.reg_exam_grade_change_requests) || 0;
            $scope.reg_makeup_exam_grades = Number(response.data.reg_makeup_exam_grades) || 0;
            $scope.reg_supplementary_exam_grades = Number(response.data.reg_supplementary_exam_grades) || 0;
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load grade change requests. Please try again later.';
            console.error('Error fetching grade change requests:', error);
        });
    };

    // Call getAll on initialization
    $scope.getAll();
}]);

// Grade approval confirmation controller (fixed for consistency)
dashboardApp.controller('gradeApprovalConfirmation', ['$scope', '$http', '$filter', '$timeout', 'numberOfPageService', function ($scope, $http, $filter, $timeout, numberOfPageService) {
    $scope.currentPage = 0;
    $scope.pageSize = 1;
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.courses_for_registrar_approval = [];
    $scope.courses_for_dpt_approvals = [];
    $scope.courses_for_freshman_approvals = [];

    $scope.numberOfPages = function () {
        console.log('Registrar approvals:', $scope.courses_for_registrar_approval);
        console.log('Department approvals:', $scope.courses_for_dpt_approvals);
        console.log('Freshman approvals:', $scope.courses_for_freshman_approvals);
        if ($scope.courses_for_registrar_approval.length > 0) {
            return numberOfPageService.numberOfPagesValue($scope.courses_for_registrar_approval, $scope.pageSize);
        } else if ($scope.courses_for_dpt_approvals.length > 0) {
            return numberOfPageService.numberOfPagesValue($scope.courses_for_dpt_approvals, $scope.pageSize);
        } else if ($scope.courses_for_freshman_approvals.length > 0) {
            return numberOfPageService.numberOfPagesValue($scope.courses_for_freshman_approvals, $scope.pageSize);
        }
        return 0;
    };

    $scope.getApprovalRejectGrade = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/getApprovalRejectGrade'
        }).then(function (response) {
            if (angular.isUndefined(response.data) || response.data == null) {
                $scope.courses_for_registrar_approval = [];
                $scope.courses_for_dpt_approvals = [];
                $scope.courses_for_freshman_approvals = [];
            } else {
                $scope.courses_for_registrar_approval = response.data.courses_for_registrar_approval || [];
                $scope.courses_for_dpt_approvals = response.data.courses_for_dpt_approvals || [];
                $scope.courses_for_freshman_approvals = response.data.courses_for_freshman_approvals || [];
            }
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load approval/reject grades. Please try again later.';
            console.error('Error fetching approval/reject grades:', error);
        });
    };

    // Call on initialization
    $scope.getApprovalRejectGrade();
}]);

// Other controllers (kept as-is with minor improvements for consistency)
dashboardApp.controller('messageController', ['$scope', '$http', '$filter', '$timeout', '$sce', function ($scope, $http, $filter, $timeout, $sce) {
    $scope.$sce = $sce;
    $scope.auto_messages = [];
    $scope.loading = true;
    $scope.errorMessage = null;

    $scope.getAll = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/getMessageAjax'
        }).then(function (response) {
            if (angular.isUndefined(response.data) || response.data == null || response.data.status !== 'success') {
                $scope.auto_messages = [];
            } else {
                $scope.auto_messages = response.data.auto_messages || [];
            }
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load messages. Please try again later.';
            console.error('Error fetching messages:', error);
        });
    };

    $scope.markAsUnread = function (id) {
        $http({
            method: 'PUT',
            url: '/auto-messages/mark-as-unread/' + id + '.json'
        }).then(function (response) {
            if (response.data && response.data.auto_messages) {
                $scope.getAll(); // Refresh on success
            } else {
                console.error('Error marking as unread:', response.data);
            }
        }, function (error) {
            console.error('Request failed:', error);
        });
    };

    var pagesShown = 1;
    var pageSize = 1;
    $scope.paginationLimit = function (data) {
        return pageSize * pagesShown;
    };
    $scope.hasMoreItemsToShow = function () {
        return pagesShown < ($scope.auto_messages.length / pageSize);
    };
    $scope.showMoreItems = function () {
        pagesShown = pagesShown + 1;
    };

    $scope.getAll();
}]);

// Remaining controllers (minimal changes for consistency)
dashboardApp.controller('studentRankController', ['$scope', '$http', function ($scope, $http) {
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.getAll = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/getRankAjax'
        }).then(function (response) {
            $scope.rank = response.data && response.data.rank ? response.data.rank : null;
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load rank. Please try again later.';
            console.error('Error fetching rank:', error);
        });
    };
    $scope.getAll();
}]);

dashboardApp.controller('studentDormDashBoardController', ['$scope', '$http', function ($scope, $http) {
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.getAll = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/getStudentAssignedDormitory'
        }).then(function (response) {
            $scope.dormAssignedStudent = response.data && response.data.dormAssignedStudent ? response.data.dormAssignedStudent : null;
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load dormitory assignments. Please try again later.';
            console.error('Error fetching dormitory assignments:', error);
        });
    };
    $scope.getAll();
}]);

dashboardApp.controller('dispatchedNotYetAssignedCourseController', ['$scope', '$http', '$filter', '$timeout', 'numberOfPageService', function ($scope, $http, $filter, $timeout, numberOfPageService) {
    $scope.currentPage = 0;
    $scope.pageSize = 1;
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.dispatched_course_list = [];
    $scope.dispatched_course_not_assigned = [];

    $scope.numberOfPages = function () {
        return numberOfPageService.numberOfPagesValue($scope.dispatched_course_list, $scope.pageSize) +
            numberOfPageService.numberOfPagesValue($scope.dispatched_course_not_assigned, $scope.pageSize);
    };

    $scope.disptachedAssignedCourse = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/disptachedAssignedCourseList'
        }).then(function (response) {
            $scope.dispatched_course_list = response.data && response.data.dispatched_course_list ? response.data.dispatched_course_list : [];
            $scope.dispatched_course_not_assigned = response.data && response.data.dispatched_course_not_assigned ? response.data.dispatched_course_not_assigned : [];
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load dispatched courses. Please try again later.';
            console.error('Error fetching dispatched courses:', error);
        });
    };

    $scope.disptachedAssignedCourse();
}]);

dashboardApp.controller('clearnceWithdrawSubController', ['$scope', '$http', '$filter', '$timeout', function ($scope, $http, $filter, $timeout) {
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.clearanceWithdrawSubRequestC = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/clearanceWithdrawSubRequest'
        }).then(function (response) {
            $scope.clearance_request = response.data && response.data.clearance_request ? response.data.clearance_request : null;
            $scope.substitution_request = response.data && response.data.substitution_request ? response.data.substitution_request : null;
            $scope.exemption_request = response.data && response.data.exemption_request ? response.data.exemption_request : null;
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load clearance/withdrawal requests. Please try again later.';
            console.error('Error fetching clearance/withdrawal requests:', error);
        });
    };

    $scope.isNotZeroOrUndefined = function (value) {
        return angular.isDefined(value) && value !== null && !isNaN(value) && Number(value) > 0;
    };

    $scope.clearanceWithdrawSubRequestC();
}]);

dashboardApp.controller('addDropRequestController', ['$scope', '$http', '$filter', '$timeout', function ($scope, $http, $filter, $timeout) {
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.addDropRequestListC = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/addDropRequestList'
        }).then(function (response) {
            $scope.drop_request = response.data && response.data.drop_request ? response.data.drop_request : null;
            $scope.add_request = response.data && response.data.add_request ? response.data.add_request : null;
            $scope.drop_request_dpt = response.data && response.data.drop_request_dpt ? response.data.drop_request_dpt : null;
            $scope.add_request_dpt = response.data && response.data.add_request_dpt ? response.data.add_request_dpt : null;
            $scope.forced_drops = response.data && response.data.forced_drops ? response.data.forced_drops : null;
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load add/drop requests. Please try again later.';
            console.error('Error fetching add/drop requests:', error);
        });
    };

    $scope.addDropRequestListC();
}]);

dashboardApp.controller('backupController', ['$scope', '$http', '$filter', '$timeout', function ($scope, $http, $filter, $timeout) {
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.getBackupAccountRequestB = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/getBackupAccountRequest'
        }).then(function (response) {
            $scope.latest_backups = response.data && response.data.latest_backups ? response.data.latest_backups : null;
            $scope.password_reset_confirmation_request = response.data && response.data.password_reset_confirmation_request ? response.data.password_reset_confirmation_request : null;
            $scope.admin_cancelation_confirmation_request = response.data && response.data.admin_cancelation_confirmation_request ? response.data.admin_cancelation_confirmation_request : null;
            $scope.admin_assignment_confirmation_request = response.data && response.data.admin_assignment_confirmation_request ? response.data.admin_assignment_confirmation_request : null;
            $scope.confirmed_taskss = response.data && response.data.confirmed_taskss ? response.data.confirmed_taskss : null;
            $scope.role_change_confirmation_request = response.data && response.data.role_change_confirmation_request ? response.data.role_change_confirmation_request : null;
            $scope.deactivation_confirmation_request = response.data && response.data.deactivation_confirmation_request ? response.data.deactivation_confirmation_request : null;
            $scope.activation_confirmation_request = response.data && response.data.activation_confirmation_request ? response.data.activation_confirmation_request : null;
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load backup account requests. Please try again later.';
            console.error('Error fetching backup account requests:', error);
        });
    };

    $scope.getBackupAccountRequestB();
}]);

dashboardApp.controller('profileNotCompleteController', ['$scope', '$http', '$filter', '$timeout', function ($scope, $http, $filter, $timeout) {
    $scope.loading = true;
    $scope.errorMessage = null;
    $scope.getProfileNotCompleteP = function () {
        $scope.loading = true;
        $scope.errorMessage = null;
        $http({
            method: 'GET',
            url: '/dashboard/getProfileNotComplete'
        }).then(function (response) {
            $scope.profile_not_buildc = response.data && response.data.profile_not_buildc ? response.data.profile_not_buildc : null;
            $scope.loading = false;
        }, function (error) {
            $scope.loading = false;
            $scope.errorMessage = 'Failed to load profile completion data. Please try again later.';
            console.error('Error fetching profile completion data:', error);
        });
    };

    $scope.getProfileNotCompleteP();
}]);

// Filters (unchanged, as they are functional)
dashboardApp.filter('startFrom', function () {
    return function (input, start) {
        if (input && input.length) {
            start = +start; // Parse to int
            return input.slice(start);
        }
        return [];
    };
});

dashboardApp.filter('dateToISO', function () {
    return function (input) {
        return new Date(input).toISOString();
    };
});
