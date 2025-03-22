document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const commentForm = document.getElementById('commentForm');
    const usernameInput = document.getElementById('username');
    const commentContentInput = document.getElementById('commentContent');
    const commentsArea = document.getElementById('commentsArea');
    const loadingElement = document.getElementById('loadingComments');
    
    // Store the last comment ID to fetch only new comments
    let lastCommentId = 0;
    
    // Initialize: Load comments when page loads
    loadComments();
    
    // Set up polling to check for new comments every 5 seconds
    setInterval(loadComments, 5000);
    
    // Handle form submission
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const username = usernameInput.value.trim();
        const content = commentContentInput.value.trim();
        
        // Validate input
        if (!username || !content) {
            alert('Vui lòng điền đầy đủ thông tin!');
            return;
        }
        
        // Disable submit button during submission
        const submitButton = commentForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...';
        
        // Prepare data for sending
        const formData = new FormData();
        formData.append('username', username);
        formData.append('content', content);
        
        // Send comment to server
        fetch('api/add_comment.php', {
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
    
    // Helper function to escape HTML to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});