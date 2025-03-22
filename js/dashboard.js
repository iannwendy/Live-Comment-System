document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements for Comments Tab
    const commentForm = document.getElementById('commentForm');
    const commentContentInput = document.getElementById('commentContent');
    const commentsArea = document.getElementById('commentsArea');
    const loadingElement = document.getElementById('loadingComments');
    
    // DOM Elements for Profile Tab
    const profileForm = document.getElementById('profileForm');
    const profileMessage = document.getElementById('profileMessage');
    
    // DOM Elements for Password Tab
    const passwordForm = document.getElementById('passwordForm');
    const passwordMessage = document.getElementById('passwordMessage');
    
    // Store the last comment ID to fetch only new comments
    let lastCommentId = 0;
    
    // Variables for notification
    let notificationPermission = false;
    let previousCommentsCount = 0;
    let notificationEnabled = true;
    
    // Initialize: Load comments when page loads
    loadComments();
    
    // Set up polling to check for new comments every 5 seconds
    setInterval(loadComments, 5000);
    
    // Request notification permission
    requestNotificationPermission();
    
    // Add notification toggle button to the UI
    addNotificationToggle();
    
    // Handle comment form submission
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const content = commentContentInput.value.trim();
        
        // Validate input
        if (!content) {
            alert('Vui lòng nhập nội dung bình luận!');
            return;
        }
        
        // Disable submit button during submission
        const submitButton = commentForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...';
        
        // Prepare data for sending
        const formData = new FormData();
        formData.append('content', content);
        
        // Send comment to server
        fetch('api/add_comment_auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear form
                commentContentInput.value = '';
                
                // Load the updated comments
                loadComments();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi gửi bình luận!');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = 'Gửi bình luận';
        });
    });
    
    // Handle profile form submission
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const username = document.getElementById('profileUsername').value.trim();
        
        // Validate input
        if (!username || username.length < 3) {
            showMessage(profileMessage, 'error', 'Tên đăng nhập phải có ít nhất 3 ký tự');
            return;
        }
        
        // Disable submit button during submission
        const submitButton = profileForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
        
        // Prepare data for sending
        const formData = new FormData();
        formData.append('username', username);
        
        // Send profile data to server
        fetch('api/update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(profileMessage, 'success', 'Cập nhật thông tin thành công!');
            } else {
                showMessage(profileMessage, 'error', 'Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage(profileMessage, 'error', 'Đã xảy ra lỗi khi cập nhật thông tin!');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = 'Cập nhật thông tin';
        });
    });
    
    // Handle password form submission
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmNewPassword = document.getElementById('confirmNewPassword').value;
        
        // Validate input
        if (!currentPassword || !newPassword || !confirmNewPassword) {
            showMessage(passwordMessage, 'error', 'Vui lòng điền đầy đủ thông tin');
            return;
        }
        
        if (newPassword.length < 6) {
            showMessage(passwordMessage, 'error', 'Mật khẩu mới phải có ít nhất 6 ký tự');
            return;
        }
        
        if (newPassword !== confirmNewPassword) {
            showMessage(passwordMessage, 'error', 'Mật khẩu xác nhận không khớp');
            return;
        }
        
        // Disable submit button during submission
        const submitButton = passwordForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
        
        // Prepare data for sending
        const formData = new FormData();
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        
        // Send password data to server
        fetch('api/change_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(passwordMessage, 'success', 'Đổi mật khẩu thành công!');
                // Clear form
                passwordForm.reset();
            } else {
                showMessage(passwordMessage, 'error', 'Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage(passwordMessage, 'error', 'Đã xảy ra lỗi khi đổi mật khẩu!');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = 'Đổi mật khẩu';
        });
    });
    
    // Function to load comments from server
    function loadComments() {
        // Show loading indicator only on first load
        if (lastCommentId === 0) {
            loadingElement.style.display = 'block';
        }
        
        // Fetch comments from server
        fetch(`api/get_comments.php?last_id=${lastCommentId}`)
            .then(response => response.json())
            .then(data => {
                // Hide loading indicator
                loadingElement.style.display = 'none';
                
                if (data.success) {
                    // If this is the first load and there are no comments
                    if (lastCommentId === 0 && data.comments.length === 0) {
                        commentsArea.innerHTML = '<div class="no-comments">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</div>';
                        return;
                    }
                    
                    // If there are new comments
                    if (data.comments.length > 0) {
                        // Update the last comment ID
                        lastCommentId = data.comments[0].id;
                        
                        // Add new comments to the top of the list
                        const newCommentsHtml = data.comments.map(comment => {
                            return createCommentHtml(comment);
                        }).join('');
                        
                        // If this is the first load, replace the content
                        if (commentsArea.querySelector('.no-comments') || commentsArea.querySelector('#loadingComments')) {
                            commentsArea.innerHTML = newCommentsHtml;
                            previousCommentsCount = data.comments.length;
                        } else {
                            // Otherwise, prepend new comments
                            commentsArea.insertAdjacentHTML('afterbegin', newCommentsHtml);
                            
                            // Highlight new comments
                            const newComments = commentsArea.querySelectorAll('.comment-item:not(.seen)');
                            newComments.forEach(comment => {
                                comment.classList.add('new-comment');
                                // Mark as seen after animation
                                setTimeout(() => {
                                    comment.classList.add('seen');
                                }, 2000);
                            });
                            
                            // Show notification for new comments if not the first load
                            if (previousCommentsCount > 0 && notificationEnabled) {
                                // Pass the first (newest) comment to the notification function
                                showNotification(data.comments[0]);
                            }
                            
                            // Update the previous comments count
                            previousCommentsCount = data.comments.length;
                        }
                    }
                } else {
                    console.error('Error loading comments:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingElement.style.display = 'none';
                commentsArea.innerHTML = '<div class="alert alert-danger">Không thể tải bình luận. Vui lòng thử lại sau.</div>';
            });
    }
    
    // Function to create HTML for a comment
    function createCommentHtml(comment) {
        const date = new Date(comment.created_at);
        const formattedDate = date.toLocaleString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        return `
            <div class="comment-item" data-id="${comment.id}">
                <div class="comment-header">
                    <span class="comment-username">${escapeHtml(comment.username)}</span>
                    <span class="comment-time">${formattedDate}</span>
                </div>
                <div class="comment-content">${escapeHtml(comment.content)}</div>
            </div>
        `;
    }
    
    // Function to show messages
    function showMessage(element, type, message) {
        element.innerHTML = `<div class="alert alert-${type === 'error' ? 'danger' : 'success'}">${message}</div>`;
        // Scroll to message
        element.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Helper function to escape HTML to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    // Function to request notification permission
    function requestNotificationPermission() {
        if (!('Notification' in window)) {
            console.log('Trình duyệt này không hỗ trợ thông báo');
            return;
        }
        
        if (Notification.permission === 'granted') {
            notificationPermission = true;
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    notificationPermission = true;
                }
            });
        }
    }
    
    // Function to show notification
    function showNotification(comment) {
        if (!notificationPermission) return;
        
        const notificationTitle = 'Bình luận mới';
        const notificationOptions = {
            body: `Có một bình luận mới từ "${comment.username}"
Nội dung: ${comment.content.length > 50 ? comment.content.substring(0, 50) + '...' : comment.content}`,
            icon: '/favicon.ico', // Thay đổi đường dẫn nếu có icon khác
            tag: 'new-comment'
        };
        
        const notification = new Notification(notificationTitle, notificationOptions);
        
        // Tự động đóng thông báo sau 5 giây
        setTimeout(() => notification.close(), 5000);
        
        // Khi người dùng click vào thông báo
        notification.onclick = function() {
            window.focus();
            notification.close();
        };
    }
    
    // Function to add notification toggle button
    function addNotificationToggle() {
        // Tạo nút toggle thông báo
        const toggleButton = document.createElement('button');
        toggleButton.className = 'btn btn-sm btn-outline-secondary notification-toggle';
        toggleButton.innerHTML = notificationEnabled 
            ? '<i class="bi bi-bell"></i> Tắt thông báo' 
            : '<i class="bi bi-bell-slash"></i> Bật thông báo';
        toggleButton.style.marginBottom = '10px';
        
        // Thêm sự kiện click
        toggleButton.addEventListener('click', function() {
            notificationEnabled = !notificationEnabled;
            
            // Cập nhật trạng thái nút
            this.innerHTML = notificationEnabled 
                ? '<i class="bi bi-bell"></i> Tắt thông báo' 
                : '<i class="bi bi-bell-slash"></i> Bật thông báo';
            
            // Nếu bật thông báo, yêu cầu quyền
            if (notificationEnabled && !notificationPermission) {
                requestNotificationPermission();
            }
        });
        
        // Thêm nút vào trước khu vực bình luận
        commentsArea.parentNode.insertBefore(toggleButton, commentsArea);
    }
});