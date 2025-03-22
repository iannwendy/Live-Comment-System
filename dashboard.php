<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hệ thống bình luận trực tiếp</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        // Redirect to login page if not logged in
        header('Location: login.php');
        exit;
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Dashboard</h3>
                            <div>
                                <span class="me-3">Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <a href="api/logout.php" class="btn btn-sm btn-light">Đăng xuất</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" id="dashboardTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab" aria-controls="comments" aria-selected="true">Bình luận</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Thông tin tài khoản</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="dashboardTabContent">
                            <!-- Comments Tab -->
                            <div class="tab-pane fade show active" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                                <!-- Comment Form -->
                                <form id="commentForm" class="mb-4">
                                    <div class="mb-3">
                                        <label for="commentContent" class="form-label">Bình luận của bạn</label>
                                        <textarea class="form-control" id="commentContent" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                                </form>
                                
                                <!-- Comments Display Area -->
                                <div class="comments-container">
                                    <h4 class="mb-3">Bình luận gần đây</h4>
                                    <div id="commentsArea" class="comments-list">
                                        <!-- Comments will be loaded here -->
                                        <div class="text-center" id="loadingComments">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Tab -->
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div id="profileMessage"></div>
                                <form id="profileForm" class="mb-4">
                                    <div class="mb-3">
                                        <label for="profileUsername" class="form-label">Tên đăng nhập</label>
                                        <input type="text" class="form-control" id="profileUsername" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="profileEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="profileEmail" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required readonly>
                                        <div class="form-text">Email không thể thay đổi</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                                </form>
                                
                                <hr>
                                
                                <h5 class="mb-3">Đổi mật khẩu</h5>
                                <div id="passwordMessage"></div>
                                <form id="passwordForm">
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Mật khẩu hiện tại</label>
                                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">Mật khẩu mới</label>
                                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                        <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmNewPassword" class="form-label">Xác nhận mật khẩu mới</label>
                                        <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>