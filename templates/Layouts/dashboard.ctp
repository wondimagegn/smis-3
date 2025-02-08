<!doctype html>
<html class="no-js">

<head>
	<?= $this->Html->charset(); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<!-- PAGE TITLE -->
    <title><?= Configure::read('ApplicationShortName') . ' ' . Configure::read('ApplicationVersionShort'); ?><?= !empty($this->fetch('title_details')) ? ' |'. $this->fetch('title_details') : (!empty($this->request->params['controller']) ? ' | ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : ''); ?><?= ' - '. Configure::read('ApplicationTitleExtra'); ?></title>

	<!-- STYLESHEETS -->
	<link rel="stylesheet" href="/css/foundation.css" />

	<?php
	if (Configure::read('debug') || true) { ?>
		<link rel="stylesheet" href="/css/dashboard.css">
		<link rel="stylesheet" href="/css/style.css">
		<link rel="stylesheet" href="/css/dripicon.css">
		<link rel="stylesheet" href="/css/typicons.css" />
		<link rel="stylesheet" href="/css/font-awesome.css" />
		<link rel="stylesheet" href="/sass/css/theme.css">
		<link href="/css/pace-theme-flash.css" rel="stylesheet" />
		<link rel="stylesheet" href="/css/slicknav.css" />
		<!-- <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->

		<link rel="stylesheet" type="text/css" href="/css/common1.css" media="screen" />
		<!-- <link href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
		<?php 
	} else {
		echo $this->AssetCompress->css('internal.css', array('full' => true));
	} ?>

	<script type="text/javascript" src="/js/jquery.js"></script>
	<script src="/js/vendor/modernizr.js"></script>

	<!-- To ckeck and reload opened tabs when the user session is no longer active -->
	<?php 
	$user_id = $this->Session->read('Auth.User.id');
	$is_logged_in = $this->Session->read('User.is_logged_in'); 
    ?>

	<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
            var userId = <?php echo json_encode($user_id); ?>;
            var checkInterval = 10000; // Check every 10 seconds to minimize server checks

            function checkSession() {
                fetch('/users/check_session')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.is_logged_in) {
                            // User logged out in another tab, reload this tab
                            window.location.reload();
                        }
                    });
            }

            if (isLoggedIn) {
                setInterval(checkSession, checkInterval);
            }
        });

        // more efficient: Increased Interval and Page Visibility Check but still there is a probability of submitting not submitted forms

       	/*  document.addEventListener('DOMContentLoaded', function() {
            var userId = <?php echo json_encode($user_id); ?>;
            var checkInterval = 10000; // Check every 10 seconds

            function checkSession() {
                if (document.hidden) {
                    return; // Skip check if the page is not visible
                }
                fetch('/users/check_session')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.is_logged_in) {
                            // User logged out in another tab, reload this tab
                            window.location.reload();
                        }
                    });
            }

            setInterval(checkSession, checkInterval);
        }); */
    </script>
	<!-- End ckeck and reload opened tabs when the user session is no longer active -->

</head>

<body>
	<div id="preloader">
		<div id="status">&nbsp;</div>
	</div>
	<!--   
	<div id="busy_indicator">
	<img src="/img/busy.gif" alt="" class="displayed" />	     
    </div> -->

	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">
			<div id="skin-select">
				<!-- Toggle sidemenu icon button -->
				<a id="toggle"> <span class="fa icon-menu"></span> </a>
				<!--  End of Toggle sidemenu icon button -->

				<div class="skin-part">
					<div id="tree-wrap">
						<div class="profile">
							<a href="/">
								<img alt="" class="" src="/img/<?= Configure::read('logo'); ?>">
								<h3><?= Configure::read('ApplicationShortName'); ?> <small><?= Configure::read('ApplicationVersionShort'); ?></small></h3>
							</a>
						</div>

						<div class="side-bar">
							<?= $this->element('mainmenu/mainmenuOptimized'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="wrap-fluid" id="paper-bg">
			<div class="top-bar-nest">
				<nav class="top-bar" data-topbar role="navigation" data-options="is_hover: false">
					<ul class="title-area left">
						<!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
						<li class="toggle-topbar menu-icon"><a href="#"><span></span></a>
						</li>
					</ul>
					<section class="top-bar-section ">
						<?php 
						if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
							<ul class="left">
								<li>
									<span class="bg-green"> <a style="color:white;line-height: 27px;" href="/alumni/add">Fill Alumni Survey</a> </span>
								</li>
							</ul>
							<?php 
						} ?>

						<?= $this->element('mainmenu/top-menu'); ?>

					</section>
				</nav>
			</div>

			<!-- Container Begin -->
			<div class="row" style="margin-top:-20px">
				<div class="large-12 columns">
					<div class="row">
						<div class="large-12 columns">
							<div class="box">
								<?php
                                if ($this->Session->flash('Message.auth')) {
                                    echo '<div style="margin-top: 40px;">'. $this->Session->flash('auth').'</div>';
                                }
                                if ($this->Session->check('Message.flash')) {
                                    echo '<div style="margin-top: 40px;">'. $this->Session->flash().'</div>';
                                }
                                ?>
							</div>
						</div>
					</div>

					<?= $this->fetch('content'); ?>

				</div>
			</div>
			<!-- Container End -->
			
			<footer>
				<div id="footer">
					Copyright &copy; <?= Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y'); ?> <?= Configure::read('CopyRightCompany'); ?>
				</div>
			</footer>
		</div>
	</div>

	<?php
	if (Configure::read('debug') || true) { ?>
		<!-- main javascript library -->
		<script type="text/javascript" src="/js/waypoints.min.js"></script>
		<script type='text/javascript' src='/js/preloader-script.js'></script>

		<!-- foundation javascript -->
		<script type='text/javascript' src="/js/foundation.min.js"></script>
		
		<script type='text/javascript' src="js/foundation/foundation.abide.js"></script>

		<!-- main edumix javascript -->
		<script type='text/javascript' src='/js/slimscroll/jquery.slimscroll.js'></script>
		<script type='text/javascript' src='/js/slicknav/jquery.slicknav.js'></script>
		<script type='text/javascript' src='/js/sliding-menu.js'></script>
		<script type='text/javascript' src='/js/scriptbreaker-multiple-accordion-1.js'></script>
		<script type="text/javascript" src="/js/number/jquery.counterup.min.js"></script>
		<script type="text/javascript" src="/js/circle-progress/jquery.circliful.js"></script>

		<!-- additional javascript -->
		<script type='text/javascript' src="/js/number-progress-bar/jquery.velocity.min.js"></script>
		<script type='text/javascript' src="/js/number-progress-bar/number-pb.js"></script>

		<script type='text/javascript' src='/js/app.js'></script>
		<script type='text/javascript' src="/js/loader/loader.js"></script>
		<script type='text/javascript' src="/js/loader/demo.js"></script>
		<?php 
	} else {
		echo $this->AssetCompress->script('mainjslib.js', array('full' => true));
		echo $this->AssetCompress->script('foundation.js', array('full' => true));
		echo $this->AssetCompress->script('maininternaledu.js', array('full' => true));
		echo $this->AssetCompress->script('additionaljavascript.js', array('full' => true));
		echo $this->AssetCompress->script('floatjavascript.js', array('full' => true));
	} ?>

	<script src="/js/angular.min.js"></script>

	<script type="text/javascript">
		$(function() {
			$(document).foundation();
		});
		$(document).ready(function() {
			$('#select-all').click(function(event) { //on click 
				if (this.checked) { // check select status
					$('.checkbox1').each(function() { //loop through each checkbox
						this.checked = true; //select all checkboxes with class "checkbox1"               
					});
				} else {
					$('.checkbox1').each(function() { //loop through each checkbox
						this.checked = false; //deselect all checkboxes with class "checkbox1"                       
					});
				}
			});

			$('.checkbox1').click(function(event) {
				//on click 
				if (!this.checked) {
					// check select status    
					$('#select-all').attr('checked', false);
				}
			});
		});

		/* 
		function newMessageNotification() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#AutoMessageDashBoard").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/getMessageAjax",
				cache: false,
				success: function(data) {
					$("#AutoMessageDashBoard").empty();
					$("#AutoMessageDashBoard").append(data);
				}
			});

		}

		function studentRank() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#StudentRankDashBoard").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/getRankAjax",
				cache: false,
				success: function(data) {
					$("#StudentRankDashBoard").empty();
					$("#StudentRankDashBoard").append(data);
					rank_stand_retrieved = true;
				},
				error: function(xhr, ajaxOptions, thrownError) {
					$("#StudentRankDashBoard").empty();
				}
			});
		}

		function studentStudentAssignedDorm() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#StudentDormDashBoard").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/getStudentAssignedDormitory",
				cache: false,
				success: function(data) {
					$("#StudentDormDashBoard").empty();
					$("#StudentDormDashBoard").append(data);
					rank_stand_retrieved = true;
				}
			});
		}

		function gradeChangeApproval() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#GradeChangeApproval").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/getApprovalRejectGradeChange",
				cache: false,
				success: function(data) {
					$("#GradeChangeApproval").empty();
					$("#GradeChangeApproval").append(data);
				}
			});
		}

		function gradeApprovalConfirmation() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#GradeConfiramationApproval").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/getApprovalRejectGrade",
				cache: false,
				success: function(data) {
					$("#GradeConfiramationApproval").empty();
					$("#GradeConfiramationApproval").append(data);
				}
			});
		}

		function getDispatchedAndAssignedCourse() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#DispatchedAndAssignedCourseID").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/disptachedAssignedCourseList",
				cache: false,
				success: function(data) {
					$("#DispatchedAndAssignedCourseID").empty();
					$("#DispatchedAndAssignedCourseID").append(data);
				}
			});
		}

		function getAddDropRequest() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#AddDropRequest").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/addDropRequestList",
				cache: false,
				success: function(data) {
					$("#AddDropRequest").empty();
					$("#AddDropRequest").append(data);
				}
			});
		}

		function getClearanceWithdrawSubRequest() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#WithdrawClearnceSubRequest").empty().html('<img src="/img/busy.gif" class="displayed" >');
			$.ajax({
				url: "/dashboard/clearanceWithdrawSubRequest",
				cache: false,
				success: function(data) {
					$("#WithdrawClearnceSubRequest").empty();
					$("#WithdrawClearnceSubRequest").append(data);
				}
			});
		}

		function getProfileNotComplet() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#ProfileNotComplete").empty().html('<img src="/img/busy.gif" class="displayed">');
			$.ajax({
				url: "/dashboard/getProfileNotComplete",
				cache: false,
				success: function(data) {
					$("#ProfileNotComplete").empty();
					$("#ProfileNotComplete").append(data);
				}
			});
		}

		function getBackupAccountRequest() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#BackupAccountRequest").empty().html('<img src="/img/busy.gif" class="displayed">');
			$.ajax({
				url: "/dashboard/getBackupAccountRequest",
				cache: false,
				success: function(data) {
					$("#BackupAccountRequest").empty();
					$("#BackupAccountRequest").append(data);
				}
			});
		}

		function getAcademicCalender() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#AcademicCalender").empty().html('<img src="/img/busy.gif" class="displayed">');
			$.ajax({
				url: "/dashboard/getAcademicCalender",
				cache: false,
				success: function(data) {
					$("#AcademicCalender").empty();
					$("#AcademicCalender").append(data);
				}
			});
		}

		function getCourseSchedule() {
			var image = new Image();
			image.src = '/img/busy.gif';
			$("#CourseSchedule").empty().html('<img src="/img/busy.gif" class="displayed">');
			$.ajax({
				url: "/dashboard/getCourseSchedule",
				cache: false,
				success: function(data) {
					$("#CourseSchedule").empty();
					$("#CourseSchedule").append(data);
				},
				error: function(xhr, status, error) {
					// handle error
					alert(error);
				}
			});
		} */
	</script>
	<script src="/js/smisangularapp.js"></script>
	<script>
		/* $(document).ready(function() {
			$("a").click(function() {

				//https://enricopiccini.com/en/kb/AngularJs_Abort_all_pending_ajax_requests_before_change_route-635
				$scope.getApprovalRejectGrade.unsubscribe();
			});
		}); */
	</script>
	<?php //echo $this->Js->writeBuffer();  ?>
	<?= $this->fetch('script'); ?>
	<?= $this->element('sql_dump'); ?>
</body>

</html>