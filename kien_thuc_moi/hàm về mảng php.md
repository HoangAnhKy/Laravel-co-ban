# Một số hàm liên quan đến mảng trong PHP

## 1. Thao tác với phần tử trong mảng:
- `array_push($array, $value)` - Thêm phần tử vào cuối mảng.
- `array_pop($array)` - Xóa và trả về phần tử cuối của mảng.
- `array_shift($array)` - Xóa và trả về phần tử đầu tiên của mảng.
- `array_unshift($array, $value)` - Thêm một hoặc nhiều phần tử vào đầu mảng.
- `array_splice($array, $offset, $length, $replacement)` - Xóa hoặc thay thế phần tử từ vị trí nhất định.

## 2. Thao tác kết hợp, chia nhỏ mảng:
- `array_merge($array1, $array2)` - Gộp hai hoặc nhiều mảng thành một.
- `array_combine($keys, $values)` - Tạo mảng mới từ hai mảng: một mảng khóa và một mảng giá trị.
- `array_slice($array, $offset, $length)` - Lấy một phần của mảng.
- `array_chunk($array, $size)` - Chia mảng thành các mảng con có kích thước `$size`.

## 3. Tìm kiếm và kiểm tra trong mảng:
- `in_array($value, $array)` - Kiểm tra xem giá trị có tồn tại trong mảng.
- `array_search($value, $array)` - Tìm khóa của giá trị trong mảng.
- `array_key_exists($key, $array)` - Kiểm tra xem khóa có tồn tại trong mảng.

## 4. Trích xuất khóa, giá trị:
- `array_keys($array)` - Lấy tất cả khóa của mảng.
- `array_values($array)` - Lấy tất cả giá trị của mảng.
- `array_flip($array)` - Đảo ngược khóa và giá trị của mảng.

## 5. Đếm và sắp xếp mảng:
- `count($array)` - Đếm số lượng phần tử trong mảng.
- `array_count_values($array)` - Đếm số lần xuất hiện của từng giá trị trong mảng.
- `sort($array)` - Sắp xếp mảng theo giá trị tăng dần.
- `rsort($array)` - Sắp xếp mảng theo giá trị giảm dần.
- `ksort($array)` - Sắp xếp mảng theo khóa tăng dần.
- `krsort($array)` - Sắp xếp mảng theo khóa giảm dần.

## 6. Thao tác lọc và biến đổi mảng:
- `array_map($callback, $array)` - Áp dụng một hàm vào tất cả các phần tử của mảng.
- `array_filter($array, $callback)` - Lọc các phần tử của mảng theo điều kiện của hàm `$callback`.
- `array_reduce($array, $callback, $initial)` - Giảm mảng thành một giá trị duy nhất bằng cách sử dụng hàm `$callback`.

## 7. Chia, nối và so sánh mảng:
- `explode($delimiter, $string)` - Chuyển chuỗi thành mảng, dựa trên ký tự phân tách.
- `implode($glue, $array)` - Chuyển mảng thành chuỗi, sử dụng `$glue` để nối.
- `array_diff($array1, $array2)` - So sánh mảng và trả về sự khác biệt.
- `array_intersect($array1, $array2)` - Trả về phần tử chung của hai mảng.

## 8. Các hàm bổ sung:
- `array_reverse($array)` - Đảo ngược thứ tự phần tử trong mảng.
- `array_unique($array)` - Loại bỏ các phần tử trùng lặp.
- `array_sum($array)` - Tính tổng giá trị của các phần tử trong mảng.
- `array_product($array)` - Tính tích giá trị của các phần tử trong mảng.
- `array_walk($array, $callback)` - Duyệt qua mảng và áp dụng một hàm vào mỗi phần tử.

