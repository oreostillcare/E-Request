<?php include('main_header/header.php');?>
        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
       <?php include('left_sidebar/sidebar.php');?>
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="container-fluid  dashboard-content">
               <!-- ============================================================== -->
                <!-- pageheader -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                             <h2 class="pageheader-title"><i class="fa fa-fw fa-user"></i> Profiles </h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Profiles</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader -->
                <!-- ============================================================== -->
                    <?php
                        $student_id = (int) $_SESSION['student_id'];
                        $conn = new class_model();
                        $user = $conn->student_profile($student_id);
                        $escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
                        $firstName = trim((string) ($user['first_name'] ?? ''));
                        $middleName = trim((string) ($user['middle_name'] ?? ''));
                        $lastName = trim((string) ($user['last_name'] ?? ''));
                        $fullName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));
                        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                        $initials = $initials !== '' ? $initials : 'ST';
                        $profilePhotoUrl = $conn->student_profile_photo_url($student_id);
                        $createdAt = trim((string) ($user['date_created'] ?? ''));
                        $createdTimestamp = $createdAt !== '' ? strtotime($createdAt) : false;
                        $joinedDate = $createdTimestamp !== false ? date('M d, Y', $createdTimestamp) : 'Not available';
                    ?>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card influencer-profile-data profile-card-modern">
                                <div class="card-body p-0">
                                    <section class="profile-overview">
                                        <div class="profile-photo-panel">
                                            <div class="profile-photo-preview" id="profilePhotoPreview">
                                                <span class="profile-photo-initials"><?= $escape($initials); ?></span>
                                                <img id="profilePhotoImage" src="<?= $escape($profilePhotoUrl); ?>" alt="<?= $escape($fullName !== '' ? $fullName . ' profile photo' : 'Student profile photo'); ?>" onerror="this.classList.add('d-none')">
                                                <span class="profile-photo-camera"><i class="fas fa-camera"></i></span>
                                            </div>
                                            <form id="profilePhotoForm" enctype="multipart/form-data">
                                                <input class="profile-photo-input" type="file" id="profilePhotoInput" name="profile_photo" accept="image/jpeg,image/png,image/webp">
                                                <label class="btn profile-photo-select" for="profilePhotoInput"><i class="fas fa-image mr-2"></i>Choose Photo</label>
                                                <button class="btn profile-photo-upload d-none" id="profilePhotoUpload" type="submit"><i class="fas fa-cloud-upload-alt mr-2"></i>Upload Photo</button>
                                                <p class="profile-photo-help">JPG, PNG, or WebP &middot; Max 5 MB</p>
                                                <div id="photoMessage" aria-live="polite"></div>
                                            </form>
                                        </div>

                                        <div class="profile-details">
                                            <div class="profile-name-row">
                                                <div>
                                                    <span class="profile-eyebrow">Student profile</span>
                                                    <h2 class="profile-student-name"><span id="firstName"><?= $escape(ucfirst($firstName)); ?></span><?= $middleName !== '' ? ' ' . $escape(ucfirst($middleName)) : ''; ?> <span id="lastName"><?= $escape(ucfirst($lastName)); ?></span></h2>
                                                </div>
                                                <span class="profile-status"><span></span> Active</span>
                                            </div>

                                            <div class="profile-meta-grid">
                                                <div class="profile-meta-item">
                                                    <span class="profile-meta-icon"><i class="fas fa-map-marker-alt"></i></span>
                                                    <div><small>Address</small><strong><?= $escape($user['complete_address'] ?? 'Not available'); ?></strong></div>
                                                </div>
                                                <div class="profile-meta-item">
                                                    <span class="profile-meta-icon"><i class="fas fa-calendar-alt"></i></span>
                                                    <div><small>Joined</small><strong><?= $escape($joinedDate); ?></strong></div>
                                                </div>
                                                <div class="profile-meta-item">
                                                    <span class="profile-meta-icon"><i class="fas fa-user"></i></span>
                                                    <div><small>Gender</small><strong><?= $escape($user['gender'] ?? 'Not available'); ?></strong></div>
                                                </div>
                                                <div class="profile-meta-item">
                                                    <span class="profile-meta-icon"><i class="fas fa-graduation-cap"></i></span>
                                                    <div><small>Strand / Grade</small><strong><?= $escape(trim((string) ($user['strand'] ?? '') . ' / ' . (string) ($user['grade_level'] ?? ''), ' /')); ?></strong></div>
                                                </div>
                                            </div>

                                            <div class="profile-contact-row">
                                                <span><i class="fas fa-envelope"></i><?= $escape($user['email_address'] ?? 'Not available'); ?></span>
                                                <span><i class="fas fa-phone"></i><?= $escape($user['mobile_number'] ?? 'Not available'); ?></span>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="profile-account-section">
                                        <div class="profile-section-heading">
                                            <span class="profile-section-icon"><i class="fas fa-shield-alt"></i></span>
                                            <div><h3>Account Security</h3><p>Update the password used to access your account.</p></div>
                                        </div>
                                        <form id="validationform" data-parsley-validate="" novalidate="" method="POST">
                                            <div id="message"></div>
                                            <div class="profile-account-grid">
                                                <div class="form-group">
                                                    <label for="profileUsername">Username</label>
                                                    <input id="profileUsername" type="text" name="username" value="<?= $escape($user['username'] ?? ''); ?>" class="form-control" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="profilePassword">New Password</label>
                                                    <div class="profile-password-field">
                                                        <input id="profilePassword" type="password" name="password" required="" placeholder="Enter a new password" class="form-control" autocomplete="new-password">
                                                        <button type="button" id="toggleProfilePassword" aria-label="Show password"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input name="student_id" value="<?= $student_id; ?>" type="hidden">
                                            <div class="profile-account-actions">
                                                <button type="button" class="btn btn-primary" id="btn-change"><i class="fas fa-save mr-2"></i>Save Password</button>
                                                <button type="reset" class="btn btn-light">Cancel</button>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
           
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <script src="../assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../assets/vendor/parsley/parsley.js"></script>
    <script src="../assets/libs/js/main-js.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var photoForm = document.getElementById('profilePhotoForm');
        var photoInput = document.getElementById('profilePhotoInput');
        var photoImage = document.getElementById('profilePhotoImage');
        var uploadButton = document.getElementById('profilePhotoUpload');
        var photoMessage = document.getElementById('photoMessage');
        var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        var maximumSize = 5 * 1024 * 1024;

        function setPhotoMessage(message, type) {
            photoMessage.className = message ? 'profile-photo-message ' + type : '';
            photoMessage.textContent = message;
        }

        photoInput.addEventListener('change', function () {
            var file = photoInput.files[0];
            setPhotoMessage('', '');
            uploadButton.classList.add('d-none');
            if (!file) {
                return;
            }
            if (allowedTypes.indexOf(file.type) === -1) {
                photoInput.value = '';
                setPhotoMessage('Please choose a JPG, PNG, or WebP image.', 'is-error');
                return;
            }
            if (file.size > maximumSize) {
                photoInput.value = '';
                setPhotoMessage('Photo must be 5 MB or smaller.', 'is-error');
                return;
            }

            photoImage.src = URL.createObjectURL(file);
            photoImage.classList.remove('d-none');
            uploadButton.classList.remove('d-none');
        });

        photoForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!photoInput.files[0]) {
                setPhotoMessage('Please choose a photo first.', 'is-error');
                return;
            }

            var originalButtonText = uploadButton.innerHTML;
            uploadButton.disabled = true;
            uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
            setPhotoMessage('', '');

            fetch('../init/controllers/upload_profile_photo.php', {
                method: 'POST',
                body: new FormData(photoForm),
                credentials: 'same-origin'
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Upload failed.');
                        }
                        return data;
                    });
                })
                .then(function (data) {
                    photoImage.src = data.photo_url;
                    photoImage.classList.remove('d-none');

                    var headerAvatar = document.getElementById('profileImage');
                    var headerImage = headerAvatar ? headerAvatar.querySelector('.profile-photo-image') : null;
                    if (headerAvatar && !headerImage) {
                        headerImage = document.createElement('img');
                        headerImage.className = 'profile-photo-image';
                        headerImage.alt = 'Profile photo';
                        headerAvatar.appendChild(headerImage);
                    }
                    if (headerImage) {
                        headerImage.src = data.photo_url;
                    }

                    photoInput.value = '';
                    uploadButton.classList.add('d-none');
                    setPhotoMessage(data.message, 'is-success');
                })
                .catch(function (error) {
                    setPhotoMessage(error.message, 'is-error');
                })
                .finally(function () {
                    uploadButton.disabled = false;
                    uploadButton.innerHTML = originalButtonText;
                });
        });

        var passwordInput = document.getElementById('profilePassword');
        var passwordToggle = document.getElementById('toggleProfilePassword');
        passwordToggle.addEventListener('click', function () {
            var showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            passwordToggle.innerHTML = showPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            passwordToggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
        });

        var accountForm = document.getElementById('validationform');
        var saveButton = document.getElementById('btn-change');
        saveButton.addEventListener('click', function () {
            var password = passwordInput.value.trim();
            var accountMessage = document.getElementById('message');
            if (password === '') {
                accountMessage.innerHTML = '<div class="alert alert-danger">Enter a new password.</div>';
                passwordInput.focus();
                return;
            }

            var originalSaveText = saveButton.innerHTML;
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            fetch('../init/controllers/change_password.php', {
                method: 'POST',
                body: new FormData(accountForm),
                credentials: 'same-origin'
            })
                .then(function (response) { return response.text(); })
                .then(function (html) {
                    accountMessage.innerHTML = html;
                    passwordInput.value = '';
                })
                .catch(function () {
                    accountMessage.innerHTML = '<div class="alert alert-danger">Could not save the password. Please try again.</div>';
                })
                .finally(function () {
                    saveButton.disabled = false;
                    saveButton.innerHTML = originalSaveText;
                });
        });
    });
    </script>
</body>
 
</html>
