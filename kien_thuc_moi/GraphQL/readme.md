# Khái niệm GraphQL

- Chỉ có một `endpoind` duy nhất gửi tới serve

- Điểm khác nhau của `GrapQL` và `Resfull API`

  | Resfull API                                                                                          | GrapQL                                                                       |
  | ---------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------- |
  | sẽ cần nhiều `request` để lấy chi tiết một thông tin nào đó                                          | sẽ chỉ cần 1 cái duy nhất để lấy                                             |
  | Khi lấy dữ liệu sẽ bị thừa thông tin nếu bên `backend` ko dùng `select` để lấy dữ liệu cột cần thiết | tự động lấy những cột mình muốn                                              |
  | thêm yêu cầu thay đổi nào đó thì sẽ phải đợi bên `backend` code                                      | tự động thêm dữ liệu mong muốn tại `frontend`                                |
  | thường sẽ hướng về phía `server`                                                                     | hướng về phía `client`                                                       |
  | có thể trả về nhiều kiểu dữ liệu như là `JSON`, `XML`, `YML`                                         | Trả về mỗi `JSON`                                                            |
  | có caching mặc định, dùng khi tải lại api                                                            | không có, nếu muốn thì phải cài thư viện hoặc lập trình hệ thống caching này |
  |                                                                                                      | dễ dàng bị tấn công `ddos` nếu có lỗ hổng nào đấy                            |

- Có 3 loại:

  - [Query(Truy vấn dữ liệu)](cách%20dùng%20với%20query.md)
  - [Mutation(Thay đổi dữ liệu)](./cách%20dùng%20với%20muntation.md)
  - Subcription(Lắng nghe sự kiện real-time)

# Gói cài đặt

```sh
composer require rebing/graphql-laravel
composer require nuwave/lighthouse
```

- Nếu ưu tiên tính linh hoạt, hiệu suất cao và muốn tận dụng tích hợp chặt chẽ với Eloquent, Lighthouse là lựa chọn phù hợp. Tuy nhiên, cần sẵn sàng đầu tư thời gian để làm quen với SDL và cách tiếp cận của gói.​

- Nếu muốn một giải pháp dễ tiếp cận hơn, sử dụng cú pháp PHP thuần và không cần các tính năng nâng cao, Rebing GraphQL có thể là lựa chọn tốt.

### [ví dụ dùng rebing](./rebing.md), chỉ có thể dùng query với mutation

### [ví dụ dùng lighthouse](./lighthouse.md), dùng được cả 3
