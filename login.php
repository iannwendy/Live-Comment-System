<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống bình luận trực tiếp</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Đăng nhập</h3>
                    </div>
                    <div class="card-body">
                        <div id="loginMessage"></div>
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="loginUsername" class="form-label">Tên đăng nhập hoặc Email</label>
                                <input type="text" class="form-control" id="loginUsername" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                            </div>
                        </form>
                        <div class="mt-3 text-center">
                            <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
                            <p><a href="index.html">Quay lại trang chủ</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const messageDiv = document.getElementById('loginMessage');
            
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form values
                const username = document.getElementById('loginUsername').value.trim();
                const password = document.getElementById('loginPassword').value;
                
                // Basic validation
                if (!username || !password) {
                    showMessage('error', 'Vui lòng điền đầy đủ thông tin đăng nhập');
                    return;
                }
                
                // Disable submit button during submission
                const submitButton = loginForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
                
                // Prepare data for sending
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);
                
                // Send login data to server
                fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', 'Đăng nhập thành công! Đang chuyển hướng...');
                        // Redirect to home page after 1 second
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 1000);
                    } else {
                        showMessage('error', 'Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('error', 'Đã xảy ra lỗi khi đăng nhập!');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Đăng nhập';
                });
            });
            
            // Function to show messages
            function showMessage(type, message) {
                messageDiv.innerHTML = `<div class="alert alert-${type === 'error' ? 'danger' : 'success'}">${message}</div>`;
                // Scroll to message
                messageDiv.scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>