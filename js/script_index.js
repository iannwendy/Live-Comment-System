document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const commentsArea = document.getElementById('commentsArea');
    const loadingElement = document.getElementById('loadingComments');
    
    // Store the last comment ID to fetch only new comments
    let lastCommentId = 0;
    
    // Initialize: Load comments when page loads
    loadComments();
    
    // Set up polling to check for new comments every 5 seconds
    setInterval(loadComments, 5000);
    
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
                        commentsArea.innerHTML = '<div class="no-comments">Chưa có bình luận nào.</div>';
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