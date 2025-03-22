<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - Hệ thống bình luận trực tiếp</title>
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
                        <h3 class="mb-0">Đăng ký tài khoản</h3>
                    </div>
                    <div class="card-body">
                        <div id="registerMessage"></div>
                        <form id="registerForm">
                            <div class="mb-3">
                                <label for="regUsername" class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="regUsername" name="username" required>
                                <div class="form-text">Tên đăng nhập phải có ít nhất 3 ký tự</div>
                            </div>
                            <div class="mb-3">
                                <label for="regEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="regEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="regPassword" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="regPassword" name="password" required>
                                <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                            </div>
                            <div class="mb-3">
                                <label for="regConfirmPassword" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="regConfirmPassword" name="confirm_password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Đăng ký</button>
                            </div>
                        </form>
                        <div class="mt-3 text-center">
                            <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
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
            const registerForm = document.getElementById('registerForm');
            const messageDiv = document.getElementById('registerMessage');
            
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form values
                const username = document.getElementById('regUsername').value.trim();
                const email = document.getElementById('regEmail').value.trim();
                const password = document.getElementById('regPassword').value;
                const confirmPassword = document.getElementById('regConfirmPassword').value;
                
                // Basic validation
                if (username.length < 3) {
                    showMessage('error', 'Tên đăng nhập phải có ít nhất 3 ký tự');
                    return;
                }
                
                if (password.length < 6) {
                    showMessage('error', 'Mật khẩu phải có ít nhất 6 ký tự');
                    return;
                }
                
                if (password !== confirmPassword) {
                    showMessage('error', 'Mật khẩu xác nhận không khớp');
                    return;
                }
                
                // Disable submit button during submission
                const submitButton = registerForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
                
                // Prepare data for sending
                const formData = new FormData();
                formData.append('username', username);
                formData.append('email', email);
                formData.append('password', password);
                
                // Send registration data to server
                fetch('api/register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', 'Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...');
                        // Redirect to login page after 2 seconds
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showMessage('error', 'Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('error', 'Đã xảy ra lỗi khi đăng ký!');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Đăng ký';
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