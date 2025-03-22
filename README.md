# Hệ thống bình luận trực tiếp (Live Comment System)

Hệ thống bình luận trực tiếp là một ứng dụng web cho phép người dùng đăng và xem các bình luận theo thời gian thực. Hệ thống hỗ trợ đăng ký, đăng nhập và quản lý tài khoản người dùng.

## Tính năng

- **Bình luận thời gian thực**: Bình luận mới sẽ hiển thị ngay lập tức mà không cần tải lại trang
- **Hệ thống tài khoản**: Đăng ký, đăng nhập và quản lý thông tin tài khoản
- **Giao diện người dùng thân thiện**: Thiết kế responsive sử dụng Bootstrap 5
- **Bảo mật**: Mật khẩu được mã hóa, xác thực người dùng an toàn
- **Tự động tạo cơ sở dữ liệu**: Hệ thống tự động tạo cơ sở dữ liệu và bảng cần thiết

## Yêu cầu hệ thống

- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Web server (Apache, Nginx, hoặc PHP built-in server)

## Cài đặt

### Sử dụng XAMPP

1. Cài đặt [XAMPP](https://www.apachefriends.org/index.html)
2. Clone repository này vào thư mục `htdocs` của XAMPP:
   ```
   git clone https://github.com/your-username/livecmt.git
   ```
3. Khởi động Apache và MySQL từ XAMPP Control Panel
4. Mở trình duyệt và truy cập: `http://localhost/livecmt`

### Cấu hình cơ sở dữ liệu

Mặc định, ứng dụng sẽ tự động tạo cơ sở dữ liệu và các bảng cần thiết. Nếu bạn muốn thay đổi thông tin kết nối cơ sở dữ liệu, hãy chỉnh sửa file `api/config.php`:

```php
// Database configuration
$host = 'localhost';
$dbname = 'livecmt_db';
$username = 'root';
$password = 'your-password';
```

## Sử dụng

### Trang chủ (Không đăng nhập)

- Truy cập `http://localhost/livecmt` để xem các bình luận gần đây
- Người dùng chưa đăng nhập chỉ có thể xem bình luận

### Đăng ký và đăng nhập

- Nhấp vào nút "Đăng ký" để tạo tài khoản mới
- Nhấp vào nút "Đăng nhập" để đăng nhập với tài khoản hiện có

### Dashboard (Đã đăng nhập)

- Sau khi đăng nhập, bạn sẽ được chuyển hướng đến trang Dashboard
- Từ Dashboard, bạn có thể:
  - Đăng bình luận mới
  - Xem các bình luận gần đây
  - Quản lý thông tin tài khoản
  - Thay đổi mật khẩu

## Cấu trúc dự án

```
├── api/                  # API endpoints
│   ├── add_comment_auth.php  # Thêm bình luận (yêu cầu đăng nhập)
│   ├── change_password.php   # Thay đổi mật khẩu
│   ├── config.php            # Cấu hình cơ sở dữ liệu
│   ├── delete_comment.php    # Xóa bình luận
│   ├── edit_comment.php      # Chỉnh sửa bình luận
│   ├── get_comments.php      # Lấy danh sách bình luận
│   ├── login.php             # Xử lý đăng nhập
│   ├── logout.php            # Xử lý đăng xuất
│   ├── register.php          # Xử lý đăng ký
│   └── update_profile.php    # Cập nhật thông tin tài khoản
├── css/                  # CSS files
│   └── style.css             # Custom styles
├── js/                   # JavaScript files
│   ├── dashboard.js          # Dashboard functionality
│   ├── script.js             # Main script for authenticated users
│   └── script_index.js       # Script for non-authenticated users
├── dashboard.php         # Dashboard page (authenticated users)
├── index.html           # Home page (non-authenticated users)
├── login.php            # Login page
└── register.php         # Registration page
```

## Bảo mật

- Mật khẩu được mã hóa bằng hàm `password_hash()` của PHP
- Dữ liệu đầu vào được kiểm tra và làm sạch để ngăn chặn SQL injection
- Sử dụng PDO với prepared statements cho các truy vấn cơ sở dữ liệu
- Xác thực phiên làm việc cho các tính năng yêu cầu đăng nhập

## Đóng góp

Nếu bạn muốn đóng góp cho dự án, vui lòng:

1. Fork repository
2. Tạo branch mới (`git checkout -b feature/your-feature`)
3. Commit thay đổi của bạn (`git commit -m 'Add some feature'`)
4. Push lên branch (`git push origin feature/your-feature`)
5. Tạo Pull Request mới

## Giấy phép

Dự án này được phân phối dưới giấy phép MIT. Xem file `LICENSE` để biết thêm thông tin.
