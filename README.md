# ✈️ TravelNice - Hệ Thống Đặt Tour Du Lịch Toàn Diện

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-Cache-DC382D?style=for-the-badge&logo=redis&logoColor=white)

TravelNice là đồ án hệ thống website đặt tour du lịch hiện đại, được xây dựng với kiến trúc chuẩn MVC, chú trọng vào tối ưu hiệu năng (Optimization), bảo mật hệ thống và tích hợp các công nghệ tiên tiến như AI, cổng thanh toán trực tuyến.

## ✨ Tính năng nổi bật

* **🚀 Hiệu năng cao (Optimization):** * Áp dụng kỹ thuật `Eager Loading Constraints` giải quyết triệt để lỗi N+1 Query.
  * Tích hợp **Redis Cache** (phân mảnh dữ liệu tĩnh 1 ngày, băm MD5 cho bộ lọc tìm kiếm) giúp thời gian phản hồi (TTFB) đạt mức tối ưu (< 50ms).
* **🤖 Tích hợp Trí tuệ nhân tạo (AI):** * Tư vấn tour du lịch tự động với `GeminiService` API, tích hợp cơ chế Fallback (dự phòng) an toàn khi rớt mạng.
* **💳 Thanh toán trực tuyến:** * Tích hợp **VNPay** và **MoMo** Sandbox, xử lý chữ ký điện tử (Secure Hash) bảo mật.
* **🔒 Bảo mật & Phân quyền:** * Hệ thống Role & Permission đa tầng sử dụng `spatie/laravel-permission`.
  * Bảo vệ tuyệt đối trước SQL Injection, XSS, và trang bị CSRF Token. Đạt chuẩn bảo mật qua công cụ phân tích tĩnh `enlightn`.
* **⚙️ Tự động hóa (Automated Testing):** * Độ phủ Test (Coverage) cao với hơn 40+ bài test (Unit & Feature) chạy độc lập trên cơ sở dữ liệu ảo `sqlite :memory:`.

## 🛠️ Yêu cầu hệ thống

Để chạy dự án này trên máy cá nhân, bạn chỉ cần cài đặt 2 phần mềm sau (không cần cài PHP hay MySQL trên máy thật):
* [Docker Desktop](https://www.docker.com/products/docker-desktop)
* [Git](https://git-scm.com/)

## 🚀 Hướng dẫn cài đặt (Chỉ với 1 lệnh Docker)

Dự án đã được cấu hình sẵn Docker Compose bao gồm các container: `app` (Laravel), `nginx`, `db` (MySQL), `redis`, và `mailhog` (bắt test email).

**Bước 1:** Clone dự án về máy
git clone [https://github.com/YuanAnh/TravelNice-Lararel.git](https://github.com/YuanAnh/TravelNice-Lararel.git)
cd TravelNice-Lararel

**Bước 2:** Khởi tạo file biến môi trường
cp .env.example .env

**Bước 3:** Khởi động hệ thống Docker
docker-compose up -d --build

**Bước 4:** Cài đặt thư viện và khởi tạo Database

Truy cập vào container app và chạy các lệnh chuẩn bị:

docker-compose exec app composer install

docker-compose exec app php artisan key:generate

docker-compose exec app php artisan migrate --seed

🎉 Hoàn thành! Bây giờ bạn có thể truy cập dự án tại:

Giao diện người dùng: http://localhost:8000

Giao diện chặn Email (Mailhog): http://localhost:8025

🧪 Chạy Kiểm thử tự động (Testing)

Dự án bao gồm bộ kiểm thử tự động nghiêm ngặt kiểm tra logic tính toán giá, tạo mã đơn hàng, tích hợp API và phân quyền. Để chạy kiểm thử, sử dụng lệnh:

docker-compose exec app php artisan test

📂 Kiến trúc Thư mục trọng tâm

app/Http/Controllers/: Xử lý logic điều hướng, tối ưu truy vấn (Ví dụ: TourController).

app/Models/: Thiết lập quan hệ (Relationships) chuẩn Eloquent.

app/Services/: Chứa các module giao tiếp API bên thứ 3 (VNPayService, GeminiService).

tests/: Chứa kịch bản Unit Test và Feature Test chạy trên RAM.

Đồ án tốt nghiệp - Thực hiện bởi [Nguyễn Tuấn Anh]