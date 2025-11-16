<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <!-- META CHARS -->
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= \Cake\Core\Configure::read('ApplicationMetaDescription') ?>" />
    <meta name="keywords" content="<?= \Cake\Core\Configure::read('ApplicationMetaKeywords') ?>">
    <meta name="author" content="<?= \Cake\Core\Configure::read('ApplicationMetaAuthor') ?>">
    <!-- Refresh the page every 15 MINUTES (in seconds) and redirect back to login -->
    <meta http-equiv="refresh" content="900;url=<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>">
    <title>Login<?= ' - ' . \Cake\Core\Configure::read('ApplicationTitleExtra') ?></title>
    <!-- Bootstrap & Font Awesome -->
    <?= $this->Html->css('/bootstrap-5.3.7-dist/css/bootstrap.min.css') ?>
    <?= $this->Html->css('/fontawesome-6.5.0/css/all.min.css') ?>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            padding-bottom: 20px;
        }

        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        body.dark-mode .navbar,
        body.dark-mode footer {
            background-color: #1f1f1f !important;
            color: #eee;
            border-color: #444;
        }

        body.dark-mode .overlay-right,
        body.dark-mode .featured-item {
            background-color: #2c2c2c;
            color: #e0e0e0;
        }

        body.dark-mode .featured-item:hover {
            box-shadow: 0 0 12px rgba(255,255,255,0.15);
        }

        body.dark-mode {
            color: #e0e0e0;
        }
        body.dark-mode .navbar a,
        body.dark-mode .navbar .dropdown-menu a,
        body.dark-mode a,
        body.dark-mode h3,
        body.dark-mode h5,
        body.dark-mode h6 {
            color: #f1f1f1 !important;
        }
        body.dark-mode .dropdown-menu {
            background-color: #2a2a2a;
        }
        body.dark-mode .dropdown-menu a:hover {
            background-color: #3a3a3a;
            color: #ffffff !important;
        }
        body.dark-mode .btn-success,
        body.dark-mode .form-control {
            background-color: #3a3a3a;
            color: #fff;
            border-color: #555;
        }
        body.dark-mode .form-control::placeholder {
            color: #bbb;
        }
        .navbar-brand img {
            object-fit: contain;
        }
        .carousel-container {
            position: relative;
            height: 50vh;
            overflow: hidden;
        }
        .carousel-item img {
            width: 100vw;
            height: 100vh;
            object-fit: cover; /* crop and fill */
            object-position: center; /* center the focal point */
            filter: brightness(80%);
            opacity: 0.85;
        }
        .overlay-left {
            position: absolute;
            top: 15%;
            left: 10%;
            color: white;
            max-width: 45%;
            z-index: 10;
        }
        .overlay-right {
            position: absolute;
            top: 10%;
            right: 10%;
            z-index: 10;
            width: 400px;
            background: rgba(255, 255, 255, 0.15); /* transparent white */
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.25); /* soft shadow */
            backdrop-filter: blur(1px); /* frost effect */
            -webkit-backdrop-filter: blur(1px); /* Safari support */
            border: 1px solid rgba(255, 255, 255, 0.2); /* subtle border */
            transition: all 0.3s ease;
        }
        .navbar .dropdown-menu .dropdown-item:hover {
            background-color: #0d6efd !important; /* Bootstrap Primary Blue */
            color: #fff !important; /* Ensures contrast */
        }
        @media (max-width: 768px) {
            .overlay-right,
            .overlay-left {
                position: absolute; /* Keep it over the carousel */
                width: 90%;
                left: 5%;
                right: 5%;
                top: auto;
                bottom: 5%;
                z-index: 10;
                padding: 1rem;
                text-align: left;
                transform: translateY(0);
            }
            .overlay-left {
                top: 5%;
                max-width: 100%;
            }
            .overlay-right {
                top: auto;
                bottom: 5%;
                background-color: rgba(255, 255, 255, 0.85);
                background: rgba(255, 255, 255, 0.15); /* transparent white */
                border-radius: 0.75rem;
                padding: 2rem;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.25); /* soft shadow */
                backdrop-filter: blur(1px); /* frost effect */
                -webkit-backdrop-filter: blur(1px); /* Safari support */
                border: 1px solid rgba(255, 255, 255, 0.2); /* subtle border */
                transition: all 0.3s ease;
            }
            .carousel-container {
                height: 100vh; /* maintain height for background */
            }
            .featured-grid .featured-item {
                max-width: none;
                flex: 0 0 45%;
                margin: 1%;
            }
            .featured-grid .row {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            .position-fixed.top-0.start-50.translate-middle-x {
                width: 90%;
                left: 0;
                transform: none;
                justify-content: center !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            #dynamicToast {
                width: 100% !important;
                max-width: 100% !important;
            }
            .navbar .dropdown-menu {
                background-color: transparent !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0.25rem 0 !important;
            }
            .navbar .dropdown-menu .dropdown-item {
                padding: 0.5rem 1rem;
                background: none !important;
            }
            .toast-container {
                padding: 0.5rem 1rem !important; /* px-3 equivalent */
                justify-content: center !important;
                pointer-events: none; /* Prevent toast container from hijacking taps */
            }
            .toast-container .toast {
                pointer-events: auto; /* Re-enable interaction only on the toast */
            }
        }
        .featured-grid {
            padding: 2rem;
        }
        .featured-grid .row {
            row-gap: 1rem;
            column-gap: 1rem;
            justify-content: center;
        }
        .featured-item {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
            padding: 1rem;
            max-width: 220px;
            margin: auto;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .featured-item:hover {
            transform: scale(1.05);
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: #ffffff;
            color: #333;
            text-align: right;
            border-top: 1px solid #ddd;
            z-index: 999;
        }

        .toast {
            background-color: rgba(0, 0, 0, 0.85);
            border-radius: 0.5rem;
            overflow: hidden;
            min-width: 300px;
            max-width: 480px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .toast-content {
            display: flex;
            align-items: center;
        }
        .toast-icon i {
            font-size: 1.5rem;
        }
        .toast-message {
            font-size: 0.95rem;
            text-align: justify;
        }
        .toast-progress {
            height: 4px;
            background: rgba(255,255,255,0.2);
        }
        #dynamicToast {
            background-color: rgba(0, 0, 0, 0.85);
            border-radius: 0.5rem;
            overflow: hidden;
            width: 100%;
            max-width: 620px; /* Controls overall width */
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .blink-badge {
            animation: pulse 1.2s infinite ease-in-out;
        }
    </style>
    <script type="text/javascript">
        history.pushState(null, null, location.href);
        window.addEventListener('popstate', () => {
            history.pushState(null, null, location.href);
            window.location.href = '<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>';
            window.location.reload();
        });
    </script>
</head>
<body>
<!-- Toast -->
<div class="position-fixed top-0 start-50 translate-middle-x d-flex justify-content-end toast-container p-5" style="z-index: 1055;">
    <div id="dynamicToast" class="toast text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-content d-flex align-items-center justify-content-between px-3 py-2">
            <div class="toast-icon">
                <i class="fas fa-info-circle me-3 fs-5" id="toastIcon"></i>
            </div>
            <div class="toast-message flex-grow-1 text-center">
                <span id="toastMessage">Placeholder</span>
            </div>
            <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-progress w-100">
            <div id="toastProgressBar" style="height: 4px; width: 100%; background: #fff;"></div>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top px-3">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Extras</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= \Cake\Core\Configure::read('ESHE_WEB_URL') ?>" target="_blank">eSHE Portal</a></li>
                    <li><a class="dropdown-item" href="<?= \Cake\Core\Configure::read('MOODLE_SITE_URL') ?>" target="_blank">eLearning Portal</a></li>
                    <li><a class="dropdown-item" href="<?= \Cake\Core\Configure::read('OTP_OFFICE_365_MAIN_URL') ?>" target="_blank">Office 365</a></li>
                    <li><a class="dropdown-item" href="<?= \Cake\Core\Configure::read('OTP_OFFICE_365_OUTLOOK_URL') ?>" target="_blank">Check Email</a></li>
                    <li><a class="dropdown-item" href="<?= \Cake\Core\Configure::read('UNIVERSITY_WEBSITE') ?>" target="_blank">AMU Website</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Announcements</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'announcement']) ?>">Latest News</a></li>
                    <li><a class="dropdown-item" href="#">Events</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Calendar</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'academic_calender']) ?>">Academic Calendar</a></li>
                    <li><a class="dropdown-item" href="#">Exam Schedule</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Admission</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'admission']) ?>">Apply Online</a></li>
                    <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'online_admission_tracking']) ?>">Track Status</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Transcript</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'official_transcript_request']) ?>">Request Transcript</a></li>
                    <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'official_request_tracking']) ?>">Track Request Status</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Alumni</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Alumni', 'action' => 'member_registration']) ?>">Register</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
<!-- Carousel -->
<div class="carousel-container">
    <?php
    $dir = new \Cake\Filesystem\Folder(WWW_ROOT . 'img/login-background1');
    $images = $dir->find('.*\.(jpg|jpeg|png|gif)', true);
    ?>
    <div id="bgCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="8000">
        <div class="carousel-inner">
            <?php
            if (!empty($images)) {
                shuffle($images);
                foreach ($images as $index => $imgName) { ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= $this->Url->build('/img/login-background1/' . h($imgName)) ?>" class="d-block w-100" alt="<?= h($imgName) ?>">
                    </div>
                    <?php
                }
            } else { ?>
                <div class="carousel-item active">
                    <img src="<?= $this->Url->build('/img/login-background/1-1366-768.jpg') ?>" class="d-block w-100" alt="Background Image" />
                </div>
                <?php
            } ?>
        </div>
    </div>
    <!-- Overlays -->
    <div class="overlay-left">
        <img src="<?= $this->Url->build('/img/' . \Cake\Core\Configure::read('logo')) ?>" alt="logo" class="img-fluid mb-3" style="max-width: 144px;" />
        <h3 class="fw-bold"><?= \Cake\Core\Configure::read('CompanyName') ?> | Office of the Registrar</h3>
        <h5 class="color-white " style="line-height: 27px;"></h5>
        <p>This is our registrar portal for students, academic staffs and alumni to access different registrar services offered by the office of the university registrar.</p>
    </div>
    <div class="overlay-right">
        <!-- Login Box -->
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
</div>
<!-- Feature Grid -->
<div class="container featured-grid">
    <div class="row text-center">
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'academic_calender']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-calendar-alt fa-2x mb-2" data-bs-toggle="tooltip" title="View academic calendar for the current academic year"></i>
                <h6>Academic<br />Calendar</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'announcement']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-bullhorn fa-2x mb-2" data-bs-toggle="tooltip" title="Read registrar announcements"></i>
                <h6>Registrar<br />Announcements</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'official_transcript_request']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-file-alt fa-2x mb-2" data-bs-toggle="tooltip" title="Apply for official transcript online"></i>
                <h6>Request Official<br />Transcript</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'official_request_tracking']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-search fa-2x mb-2" data-bs-toggle="tooltip" title="Track official transcript status applied online"></i>
                <h6>Check Official<br />Transcript Status</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'admission']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-cloud-upload-alt fa-2x mb-2" data-bs-toggle="tooltip" title="Apply for university admission online"></i>
                <h6>Online<br />Admission</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'online_admission_tracking']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-search fa-2x mb-2" data-bs-toggle="tooltip" title="Track your admission application status"></i>
                <h6>Online Admission<br />Tracking</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Alumni', 'action' => 'member_registration']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-user-graduate fa-2x mb-2" data-bs-toggle="tooltip" title="Register as an alumni member"></i>
                <h6>Alumni<br />Registration</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'check_graduate']) ?>" class="text-decoration-none text-dark">
                <i class="fas fa-shield-alt fa-2x mb-2" data-bs-toggle="tooltip" title="Verify graduation status and detect forgery"></i>
                <h6>Forgery<br />Check</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= \Cake\Core\Configure::read('MOODLE_SITE_URL') ?>" class="text-decoration-none text-dark" target="_blank">
                <i class="fas fa-laptop-code fa-2x mb-2" data-bs-toggle="tooltip" title="Access digital learning resources and courses from AMU Elearning Portal"></i>
                <h6>AMU eLearning<br />Portal</h6>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-4 featured-item">
            <a href="<?= \Cake\Core\Configure::read('ESHE_WEB_URL') ?>" class="text-decoration-none text-dark" target="_blank">
                <i class="fas fa-network-wired fa-2x mb-2" data-bs-toggle="tooltip" title="Access National Digital Education Platform (eSHE)"></i>
                <h6>eSHE<br />Portal</h6>
            </a>
        </div>
    </div>
</div>
<!-- Footer -->
<footer>
    <small>Copyright &copy; <?= \Cake\Core\Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y') ?> <?= \Cake\Core\Configure::read('CopyRightCompany') ?></small>
</footer>
<!-- Bootstrap JS & Tooltip Init -->
<?= $this->Html->script('/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
        const loginBtn = document.getElementById('loginButton');
        const text1 = document.getElementById('Text1');
        const text2 = document.getElementById('Text2');
        const usernameMinLength = <?= (is_numeric(\Cake\Core\Configure::read('MINIMUM_USERNAME_LENGTH')) && \Cake\Core\Configure::read('MINIMUM_USERNAME_LENGTH') >= 3 ? \Cake\Core\Configure::read('MINIMUM_USERNAME_LENGTH') : 3) ?>;
        const passwordMinLength = <?= (is_numeric(\Cake\Core\Configure::read('GENERATE_PASSWORD_LENGTH')) && \Cake\Core\Configure::read('GENERATE_PASSWORD_LENGTH') >= 5 ? \Cake\Core\Configure::read('GENERATE_PASSWORD_LENGTH') : 5) ?>;
        function validateMinLength(input, fieldName, minLength = 3) {
            const value = input.value.trim();
            const isValid = value.length >= minLength;
            input.classList.remove('is-valid', 'is-invalid');
            input.classList.add(isValid ? '' : 'is-invalid');
            if (!isValid) {
                showToast(`${fieldName} is too short.`, 'error', 3000);
            }
            return isValid;
        }
        if (loginBtn && text1 && text2) {
            loginBtn.addEventListener('click', function (e) {
                const validText2 = validateMinLength(text2, 'Password', passwordMinLength);
                if (!validText2) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
            ['input', 'blur'].forEach(event => {
                text1.addEventListener(event, () => {
                    if (text1.value.trim().length >= usernameMinLength) {
                        text1.classList.remove('is-invalid');
                    }
                });
                text2.addEventListener(event, () => {
                    if (text2.value.trim().length >= passwordMinLength) {
                        text2.classList.remove('is-invalid');
                    }
                });
            });
        }
    });
    localStorage.setItem('theme', 'light');
    const toggle = document.getElementById('themeToggle');
    const prefersDark = window.matchMedia('(prefers-color-scheme: light)').matches;
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
        //document.body.classList.add('dark-mode');
    }
    if (toggle) {
        toggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            toggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        });
    }
    function showToast(message, type = 'info', delay = 5000) {
        const toastEl = document.getElementById('dynamicToast');
        const messageEl = document.getElementById('toastMessage');
        const iconEl = document.getElementById('toastIcon');
        const progressBar = document.getElementById('toastProgressBar');
        toastEl.className = 'toast text-white border-0';
        iconEl.className = 'fas me-3 fs-5';
        progressBar.style.width = '100%';
        progressBar.style.transition = 'none';
        switch (type) {
            case 'success':
                toastEl.classList.add('bg-success');
                iconEl.classList.add('fa-check-circle');
                break;
            case 'error':
                toastEl.classList.add('bg-danger');
                iconEl.classList.add('fa-times-circle');
                break;
            case 'warning':
                toastEl.classList.add('bg-warning', 'text-dark');
                iconEl.classList.add('fa-exclamation-triangle');
                break;
            default:
                toastEl.classList.add('bg-info');
                iconEl.classList.add('fa-info-circle');
        }
        messageEl.textContent = message;
        new bootstrap.Toast(toastEl, { delay: delay, autohide: true }).show();
        const allButtons = document.querySelectorAll('.btn');
        allButtons.forEach(btn => {
            if (btn.tagName === 'A') {
                btn.classList.add('disabled');
                btn.setAttribute('aria-disabled', 'true');
                btn.style.pointerEvents = 'none';
            } else {
                btn.disabled = true;
            }
        });
        setTimeout(() => {
            allButtons.forEach(btn => {
                if (btn.tagName === 'A') {
                    btn.classList.remove('disabled');
                    btn.removeAttribute('aria-disabled');
                    btn.style.pointerEvents = '';
                } else {
                    btn.disabled = false;
                }
            });
        }, delay);
        setTimeout(() => {
            progressBar.style.transition = `width ${delay}ms linear`;
            progressBar.style.width = '0%';
        }, 100);
    }
</script>
</body>
</html>
