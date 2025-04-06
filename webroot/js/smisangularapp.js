
var dashboardApp = angular.module('dashboardApp', []);
// adding loading effect

dashboardApp.directive("loadingAjax", function () {
    return {
        restrict: "E",
        template: '<img src="/img/busy.gif" class="displayed" >',

    };
});

// removing loading effect
dashboardApp.service('removeLoading', function () {
    this.removeLoadingElement = function (elem) {
        var myEl = angular.element(document.getElementById(elem).getElementsByTagName('loading-ajax'));
        myEl.remove();
    }
});

// removing loading effect
dashboardApp.service('numberOfPageService', function () {
    this.numberOfPagesValue = function (jsondata, pageSize) {
        if (jsondata && jsondata.length) {
            return Math.ceil(jsondata.length / pageSize);
        }
        return 0;
    }
});

dashboardApp.controller('gradeApprovalConfirmation', ['$scope', '$http', '$filter', '$timeout', 'removeLoading', 'numberOfPageService',
    function ($scope, $http, $filter, $timeout, removeLoading, numberOfPageService) {

        $scope.currentPage = 0;
        $scope.pageSize = 1;
        $scope.courses_for_registrar_approval = [];
        $scope.courses_for_dpt_approvals = [];
        $scope.courses_for_freshman_approvals = [];

        $scope.numberOfPages = function () {

            console.log($scope.courses_for_registrar_approval);
            console.log($scope.courses_for_dpt_approvals);
            console.log($scope.courses_for_freshman_approvals);

            if ($scope.courses_for_registrar_approval.length > 0) {
                return numberOfPageService.numberOfPagesValue($scope.courses_for_registrar_approval, $scope.pageSize);
            } else if ($scope.courses_for_dpt_approvals.length > 0) {
                return numberOfPageService.numberOfPagesValue($scope.courses_for_dpt_approvals, $scope.pageSize);
            } else if ($scope.courses_for_freshman_approvals.length > 0) {
                return numberOfPageService.numberOfPagesValue($scope.courses_for_freshman_approvals, $scope.pageSize);
            }

            return 0;
        }

        $scope.getApprovalRejectGrade = function () {
            $http({
                method: "GET",
                url: "/dashboard/getApprovalRejectGrade"
            }).then(
                function mySucces(response) {
                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("gradeBox");
                    } else {
                        //$scope.courses_for_registrar_approval = response.data.courses_for_registrar_approval;
                    }

                    if (!angular.isUndefined(response.data.courses_for_registrar_approval) || response.data.courses_for_registrar_approval != null) {
                        $scope.courses_for_registrar_approval = response.data.courses_for_registrar_approval;
                    } else if (!angular.isUndefined(response.data.courses_for_dpt_approvals) || response.data.courses_for_dpt_approvals != null) {
                        $scope.courses_for_dpt_approvals = response.data.courses_for_dpt_approvals;
                    } else if (!angular.isUndefined(response.data.courses_for_freshman_approvals) || response.data.courses_for_freshman_approvals != null) {
                        $scope.courses_for_freshman_approvals = response.data.courses_for_freshman_approvals;
                    }

                    removeLoading.removeLoadingElement("gradeBox");
                },

                function myError(response) {
                    if (!angular.isUndefined(response.statusText) || response.statusText != null) {
                        $scope.courses_for_registrar_approval = response.statusText;
                    } else if (!angular.isUndefined(response.statusText) || response.statusText != null) {
                        $scope.courses_for_dpt_approvals = response.statusText;
                    } else if (!angular.isUndefined(response.statusText) || response.statusText != null) {
                        $scope.courses_for_freshman_approvals = response.statusText;
                    }

                    removeLoading.removeLoadingElement("gradeBox");
                }
            );
        }

        $scope.getApprovalRejectGrade();
    }
]);


dashboardApp.controller('gradeChangeController', ['$scope', '$http', '$filter', 'removeLoading',
    function ($scope, $http, $filter, removeLoading) {
        $scope.getAll = function () {
            $http({
                method: "GET",
                url: "/dashboard/getApprovalRejectGradeChange"
            }).then(
                function mySucces(response) {

                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("GradeChangeApproval");
                    } else {

                        $scope.exam_grade_changes_for_college_approval = response.data.exam_grade_changes_for_college_approval;
                        $scope.makeup_exam_grades = response.data.makeup_exam_grades;
                        $scope.rejected_makeup_exams = response.data.rejected_makeup_exams;
                        $scope.rejected_supplementary_exams = response.data.rejected_supplementary_exams;
                        $scope.exam_grade_change_requests = response.data.exam_grade_change_requests;

                        $scope.reg_exam_grade_change_requests = response.data.reg_exam_grade_change_requests;
                        $scope.reg_makeup_exam_grades = response.data.reg_makeup_exam_grades;
                        $scope.reg_supplementary_exam_grades = response.data.reg_supplementary_exam_grades;

                        $scope.fm_exam_grade_change_requests = response.data.fm_exam_grade_change_requests;
                        $scope.fm_makeup_exam_grades = response.data.fm_makeup_exam_grades;
                        $scope.fm_rejected_makeup_exams = response.data.fm_rejected_makeup_exams;
                        $scope.fm_rejected_supplementary_exams = response.data.fm_rejected_supplementary_exams;
                    }

                    removeLoading.removeLoadingElement("GradeChangeApproval");
                },

                function myError(response) {
                    removeLoading.removeLoadingElement("GradeChangeApproval");
                    $scope.approvalRejectGrade = response.statusText;
                }

            );
        };

        $scope.isNotZeroOrUndefined = function (value) {
            if (value == 0) {
                return false;
            } else if (angular.isUndefined(value)) {
                return false;
            }
            return true;
        }
    }
]);

dashboardApp.controller('messageController', ['$scope', '$http', '$filter', '$timeout',
    'removeLoading', '$sce',
    function ($scope, $http, $filter, $timeout, removeLoading, $sce) {
        $scope.$sce = $sce;
        $scope.auto_messages = [];
        // get all  messages
        $scope.getAll = function () {
            $http({
                method: "GET",
                url: "/dashboard/getMessageAjax"
            }).then(
                function mySucces(response) {
                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("AutoMessageDashBoard");
                        return;
                    }

                    // Ensure correct response handling
                    if (response.data.status === "success") {
                        $scope.auto_messages = response.data.auto_messages;
                    } else {
                        $scope.auto_messages = []; // Handle "no_data" scenario
                    }

                    removeLoading.removeLoadingElement("AutoMessageDashBoard");

                },

                function myError(response) {
                    removeLoading.removeLoadingElement("AutoMessageDashBoard");
                    $scope.auto_messages = response.statusText;
                }
            );
        };

        // Mark message as unread
        $scope.markAsUnread = function (id) {
            $http({
                method: "PUT",
                url: "/auto-messages/mark-as-unread/" + id + ".json"
            }).then(
                function mySucces(response) {
                    if (response.data && response.data.auto_messages) {
                        $scope.getAll(); // Refresh on success
                    } else {
                        console.error("Error:", response.data);
                    }
                },
                function myError(response) {
                    console.error("Request failed:", response.statusText);
                }
            );
        };

        //show more functionality

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

    }
]);


dashboardApp.controller('studentRankController', ['$scope', '$http', 'removeLoading',
    function ($scope, $http, removeLoading) {
        // get rank
        $scope.getAll = function () {
            $http({
                method: "GET",
                url: "/dashboard/getRankAjax"
            }).then(
                function mySucces(response) {

                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("StudentRankDashBoard");
                    }

                    $scope.rank = response.data.rank;

                    removeLoading.removeLoadingElement("StudentRankDashBoard");
                },

                function myError(response) {
                    removeLoading.removeLoadingElement("StudentRankDashBoard");
                    $scope.rank = response.statusText;
                }
            );
        };

    }
]);

dashboardApp.controller('studentDormDashBoardController', ['$scope', '$http', 'removeLoading',
    function ($scope, $http, removeLoading) {
        // get dormitory assignments
        $scope.getAll = function () {
            $http({
                method: "GET",
                url: "/dashboard/getStudentAssignedDormitory"
            }).then(
                function mySucces(response) {

                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("StudentDormDashBoard");
                    }

                    $scope.dormAssignedStudent = response.data.dormAssignedStudent;

                    removeLoading.removeLoadingElement("StudentDormDashBoard");
                },

                function myError(response) {
                    removeLoading.removeLoadingElement("StudentDormDashBoard");

                }
            );
        };

    }
]);

dashboardApp.controller('dispatchedNotYetAssignedCourseController', ['$scope', '$http', '$filter', '$timeout', 'removeLoading', 'numberOfPageService',
    function ($scope, $http, $filter, $timeout, removeLoading, numberOfPageService) {

        $scope.currentPage = 0;
        $scope.pageSize = 1;
        $scope.dispatched_course_list = [];
        $scope.dispatched_course_not_assigned = [];

        $scope.numberOfPages = function () {
            return numberOfPageService.numberOfPagesValue($scope.dispatched_course_list, $scope.pageSize) + numberOfPageService.numberOfPagesValue($scope.dispatched_course_not_assigned, $scope.pageSize);
        }

        $scope.disptachedAssignedCourse = function () {
            $http({
                method: "GET",
                url: "/dashboard/disptachedAssignedCourseList"
            }).then(
                function mySucces(response) {

                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("DispatchedAndAssignedCourseID");
                    }

                    $scope.dispatched_course_list = response.data.dispatched_course_list;
                    $scope.dispatched_course_not_assigned = response.data.dispatched_course_not_assigned;

                    removeLoading.removeLoadingElement("DispatchedAndAssignedCourseID");
                },

                function myError(response) {
                    removeLoading.removeLoadingElement("DispatchedAndAssignedCourseID");
                }
            );
        };

        $scope.disptachedAssignedCourse();
    }
]);

dashboardApp.controller('clearnceWithdrawSubController', ['$scope', '$http', '$filter', '$timeout', 'removeLoading',
    function ($scope, $http, $filter, $timeout, removeLoading) {

        $scope.clearanceWithdrawSubRequestC = function () {
            $http({
                method: "GET",
                url: "/dashboard/clearanceWithdrawSubRequest"
            }).then(
                function (response) {

                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("ClearnceAndWithdraw");
                    }

                    $scope.clearance_request = response.data.clearance_request;
                    $scope.substitution_request = response.data.substitution_request;
                    $scope.exemption_request = response.data.exemption_request;

                    removeLoading.removeLoadingElement("ClearnceAndWithdraw");
                }
            );
        };

        $scope.clearanceWithdrawSubRequestC();

        $scope.isNotZeroOrUndefined = function (value) {
            if (value == 0) {
                return false;
            } else if (angular.isUndefined(value)) {
                return false;
            }
            return true;
        }
    }
]);

dashboardApp.controller('addDropRequestController', ['$scope', '$http', '$filter', '$timeout', 'removeLoading',
    function ($scope, $http, $filter, $timeout, removeLoading) {
        $scope.addDropRequestListC = function () {
            $http({
                method: "GET",
                url: "/dashboard/addDropRequestList"
            }).then(
                function (response) {

                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("AddDropRequest");
                    }

                    $scope.drop_request = response.data.drop_request;
                    $scope.add_request = response.data.add_request;
                    $scope.drop_request_dpt = response.data.drop_request_dpt;
                    $scope.add_request_dpt = response.data.add_request_dpt;
                    $scope.forced_drops = response.data.forced_drops;

                    removeLoading.removeLoadingElement("AddDropRequest");
                }
            );
        };

        $scope.addDropRequestListC();
    }
]);

dashboardApp.controller('backupController', ['$scope', '$http', '$filter', '$timeout', 'removeLoading',
    function ($scope, $http, $filter, $timeout, removeLoading) {
        $scope.getBackupAccountRequestB = function () {
            $http({
                method: "GET",
                url: "/dashboard/getBackupAccountRequest"
            }).then(
                function (response) {

                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("BackupAccountRequest");
                    }

                    $scope.latest_backups = response.data.latest_backups;
                    $scope.password_reset_confirmation_request = response.data.password_reset_confirmation_request;
                    $scope.admin_cancelation_confirmation_request = response.data.admin_cancelation_confirmation_request;
                    $scope.admin_assignment_confirmation_request = response.data.admin_assignment_confirmation_request;
                    $scope.confirmed_taskss = response.data.confirmed_taskss;
                    $scope.role_change_confirmation_request = response.data.role_change_confirmation_request;
                    $scope.deactivation_confirmation_request = response.data.deactivation_confirmation_request;
                    $scope.activation_confirmation_request = response.data.activation_confirmation_request;

                    removeLoading.removeLoadingElement("BackupAccountRequest");
                }
            );
        };

        $scope.getBackupAccountRequestB();
    }
]);

dashboardApp.controller('profileNotCompleteController', ['$scope', '$http', '$filter', '$timeout', 'removeLoading',
    function ($scope, $http, $filter, $timeout, removeLoading) {
        $scope.getProfileNotCompleteP = function () {
            $http({
                method: "GET",
                url: "/dashboard/getProfileNotComplete"
            }).then(
                function (response) {
                    if (angular.isUndefined(response.data) || response.data == null) {
                        removeLoading.removeLoadingElement("ProfileNotComplete");
                    } else {
                        $scope.profile_not_buildc = response.data.profile_not_buildc;
                    }
                    removeLoading.removeLoadingElement("ProfileNotComplete");
                },

                function myError(response) {
                    removeLoading.removeLoadingElement("ProfileNotComplete");
                    $scope.auto_messages = response.statusText;
                }
            );
        };

        $scope.getProfileNotCompleteP();
    }
]);

dashboardApp.filter('startFrom', function () {
    return function (input, start) {
        if (input && input.length) {
            start = +start; //parse to int
            return input.slice(start);
        }
        return;
    }
});

dashboardApp.filter('dateToISO', function () {
    return function (input) {
        return new Date(input).toISOString();
    };
});
