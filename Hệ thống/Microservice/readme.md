là một kiến trúc phần mềm trong đó một ứng dụng lớn được chia nhỏ thành nhiều dịch vụ độc lập, mỗi dịch vụ phụ trách một chức năng cụ thể, có thể phát triển, triển khai và mở rộng tách biệt.


Đặc điểm chính của microservice

- `Tách biệt chức năng`: Mỗi service chỉ làm một nhiệm vụ rõ ràng (ví dụ: service User, service Order, service Payment).
- `Độc lập triển khai`: Có thể cập nhật hoặc triển khai lại một service mà không ảnh hưởng đến toàn bộ hệ thống.
- `Giao tiếp qua API`: Các service thường giao tiếp với nhau qua HTTP/REST, gRPC hoặc message broker (Kafka, RabbitMQ...).
- `Công nghệ đa dạng`: Mỗi service có thể được viết bằng ngôn ngữ/libraries/framework khác nhau, miễn là tuân theo chuẩn giao tiếp.
- `Khả năng mở rộng`: Chỉ cần scale service nào đang bị tải nặng, thay vì scale cả hệ thống nguyên khối.


Demo cơ bản với [API](./Demo%20service%20api.md)

# Kết luận 
Nói cách khác: `Microservice` = `tách code` + `tách DB` + `giao tiếp qua API/Event.`