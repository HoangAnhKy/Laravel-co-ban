`Collections` trong Laravel là một lớp mạnh mẽ được xây dựng trên lớp `Illuminate\Support\Collection`. Collections giúp xử lý dữ liệu, đặc biệt là các mảng và kết quả của các câu truy vấn, một cách hiệu quả và tiện lợi hơn so với việc sử dụng mảng thông thường. Collections có rất nhiều phương thức hữu ích giúp thao tác dữ liệu dễ dàng, nhanh chóng và mạch lạc.

# Tạo Collection

```php
$collection = collect([1,2,3,4,5]);
```
# Các Phương Thức Collection Trong Laravel

## 1. Phương Thức Cơ Bản (Basic Methods)
Các phương thức dùng để truy xuất, đếm và lấy thông tin tổng quan từ Collection.

- `all()`: Trả về tất cả các phần tử của collection dưới dạng mảng.

  ```php
  $allItems = $collection->all(); // [1, 2, 3, 4, 5]
  ```

- `average()` / `avg()`: Tính giá trị trung bình của các phần tử.

  ```php
  $average = $collection->average(); // 3
  ```

- `count()`: Đếm số lượng phần tử trong collection.

  ```php
  $count = $collection->count(); // 5
  ```

- `first()`: Lấy phần tử đầu tiên của collection.

  ```php
  $firstItem = $collection->first(); // 1
  ```

- `last()`: Lấy phần tử cuối cùng của collection.

  ```php
  $lastItem = $collection->last(); // 5
  ```

- `keys()`: Lấy tất cả các khóa của collection.

  ```php
  $keys = $collection->keys(); // [0, 1, 2, 3, 4]
  ```

- `values()`: Lấy tất cả các giá trị của collection.

  ```php
  $values = $collection->values(); // [1, 2, 3, 4, 5]
  ```

- `get()`: Lấy giá trị của phần tử dựa trên khóa.

  ```php
  $value = $collection->get(1); // 2
  ```

## 2. Phương Thức Xử Lý Dữ Liệu (Data Manipulation Methods)
Các phương thức dùng để biến đổi hoặc thay đổi dữ liệu trong Collection.

- `chunk()` và `chunkWhile()`: Chia collection thành các phần nhỏ hơn với số lượng phần tử nhất định hoặc theo điều kiện.

  ```php
  $chunks = $collection->chunk(2); // [[1, 2], [3, 4], [5]]
  ```

- `map()`: Biến đổi từng phần tử trong collection và trả về collection mới.

  ```php
  $multiplied = $collection->map(function ($item) {
      return $item * 2;
  });
  // Kết quả: [2, 4, 6, 8, 10]
  ```

- `filter()`: Lọc các phần tử thỏa mãn điều kiện.

  ```php
  $filtered = $collection->filter(function ($item) {
      return $item > 2;
  });
  // Kết quả: [3, 4, 5]
  ```

- `reduce()`: Giảm collection xuống còn một giá trị duy nhất bằng cách áp dụng hàm lên từng phần tử.

  ```php
  $sum = $collection->reduce(function ($carry, $item) {
      return $carry + $item;
  }, 0);
  // Kết quả: 15
  ```

- `duplicates()` và `duplicatesStrict()`: Trả về các giá trị trùng lặp trong collection.

  ```php
  $duplicates = collect([1, 2, 2, 3, 4, 4])->duplicates();
  // Kết quả: [1 => 2, 4 => 4]
  ```

- `flatMap()`: Biến đổi và làm phẳng collection thành một collection mới.

  ```php
  $flatMapped = $collection->flatMap(function ($item) {
      return [$item, $item * 2];
  });
  // Kết quả: [1, 2, 2, 4, 3, 6, 4, 8, 5, 10]
  ```

- `flatten()`: Làm phẳng một collection đa cấp thành một chiều.

  ```php
  $flattened = collect([[1, 2], [3, 4], [5]])->flatten();
  // Kết quả: [1, 2, 3, 4, 5]
  ```

- `merge()` / `mergeRecursive()`: Trộn collection với một mảng hoặc collection khác.

  ```php
  $merged = $collection->merge([6, 7]);
  // Kết quả: [1, 2, 3, 4, 5, 6, 7]
  ```

- `replace()` / `replaceRecursive()`: Thay thế các giá trị trong collection.

  ```php
  $replaced = $collection->replace([1 => 10, 2 => 20]);
  // Kết quả: [1, 10, 20, 4, 5]
  ```

- `reverse()`: Đảo ngược thứ tự của các phần tử trong collection.

  ```php
  $reversed = $collection->reverse();
  // Kết quả: [5, 4, 3, 2, 1]
  ```

- `shuffle()`: Xáo trộn các phần tử của collection.

  ```php
  $shuffled = $collection->shuffle();
  // Kết quả: [3, 1, 5, 4, 2] (ngẫu nhiên)
  ```

- `splice()`: Lấy một phần của collection và loại bỏ nó khỏi collection ban đầu.

  ```php
  $spliced = $collection->splice(2);
  // Kết quả: [3, 4, 5]
  ```

- `split()`: Chia collection thành các phần nhỏ hơn.

  ```php
  $split = $collection->split(3);
  // Kết quả: [[1, 2], [3, 4], [5]]
  ```

- `sort()`, `sortBy()`, `sortByDesc()`: Sắp xếp các phần tử trong collection.

  ```php
  $sorted = $collection->sort();
  // Kết quả: [1, 2, 3, 4, 5]
  ```

- `sortKeys()`, `sortKeysDesc()`: Sắp xếp collection dựa trên khóa.

  ```php
  $sortedByKeys = collect([2 => 'b', 1 => 'a', 3 => 'c'])->sortKeys();
  // Kết quả: [1 => 'a', 2 => 'b', 3 => 'c']
  ```

- `take()`: Lấy một số phần tử đầu tiên hoặc cuối cùng từ collection.

  ```php
  $taken = $collection->take(3);
  // Kết quả: [1, 2, 3]
  ```

- `slice()`: Lấy một phần của collection mà không thay đổi collection gốc.

  ```php
  $sliced = $collection->slice(2);
  // Kết quả: [3, 4, 5]
  ```

- `union()`: Hợp nhất collection với một tập giá trị khác, ưu tiên các giá trị từ collection hiện tại.

  ```php
  $union = $collection->union([6, 7]);
  // Kết quả: [1, 2, 3, 4, 5, 6, 7]
  ```

- `unique()`, `uniqueStrict()`: Loại bỏ các giá trị trùng lặp.

  ```php
  $unique = collect([1, 2, 2, 3, 4, 4])->unique();
  // Kết quả: [1, 2, 3, 4]
  ```

- `pad()`: Thêm phần tử vào collection cho đến khi đạt kích thước mong muốn.

  ```php
  $padded = $collection->pad(7, 0);
  // Kết quả: [1, 2, 3, 4, 5, 0, 0]
  ```

## 3. Phương Thức Truy Vấn (Query Methods)
Dùng để truy vấn, tìm kiếm hoặc kiểm tra dữ liệu trong Collection.

- `contains()`, `containsStrict()`: Kiểm tra xem `Collection` có chứa một giá trị hoặc khóa cụ thể.

  ```php
  $contains = $collection->contains(3); // true
  ```

- `find()`: Tìm một phần tử trong `Collection` dựa trên giá trị hoặc điều kiện.

  ```php
  $found = $collection->firstWhere('id', 1);
  ```

- `firstWhere()`: Tìm phần tử đầu tiên thỏa mãn điều kiện cụ thể.

  ```php
  $firstWhere = $collection->firstWhere('name', 'John');
  ```

- `where()`, `whereStrict()`: Lọc các phần tử dựa trên điều kiện cụ thể.

  ```php
  $filtered = $collection->where('age', '>', 18);
  ```

- `whereIn()`, `whereInStrict()`: Kiểm tra xem một giá trị có nằm trong danh sách giá trị cho trước.

  ```php
  $whereIn = $collection->whereIn('id', [1, 2, 3]);
  ```

- `whereBetween()`, `whereNotBetween()`: Lọc các phần tử nằm giữa hoặc không nằm giữa hai giá trị.

  ```php
  $whereBetween = $collection->whereBetween('age', [18, 30]);
  ```

- `whereNotIn()`, `whereNotInStrict()`: Lọc các phần tử không nằm trong danh sách giá trị.

  ```php
  $whereNotIn = $collection->whereNotIn('id', [1, 2, 3]);
  ```

- `whereInstanceOf()`: Lọc các phần tử có kiểu của lớp đã chỉ định.

  ```php
  $instances = $collection->whereInstanceOf(User::class);
  ```

- `search()`: Tìm vị trí của một phần tử trong `Collection`.

  ```php
  $index = $collection->search(4); // 3
  ```

- `some()`: Kiểm tra xem có phần tử nào thỏa mãn điều kiện không.

  ```php
  $some = $collection->some(function ($value) {
      return $value > 3;
  });
  ```

- `every()`: Kiểm tra xem tất cả các phần tử có thỏa mãn điều kiện không.

  ```php
  $every = $collection->every(function ($value) {
      return $value > 0;
  });
  ```

- `has()`: Kiểm tra xem một khóa có tồn tại trong collection hay không.

  ```php
  $has = $collection->has(2); // true
  ```

- `isEmpty()`, `isNotEmpty()`: Kiểm tra collection có rỗng hay không.

  ```php
  $isEmpty = $collection->isEmpty(); // false
  ```

## 4. Phương Thức Tổng Hợp Và Phân Tích (Aggregation Methods)
Dùng để tổng hợp hoặc tính toán dữ liệu trong Collection.

- `sum()`: Tính tổng của các phần tử.

  ```php
  $sum = $collection->sum(); // 15
  ```

- `max()`: Tìm giá trị lớn nhất.

  ```php
  $max = $collection->max(); // 5
  ```

- `min()`: Tìm giá trị nhỏ nhất.

  ```php
  $min = $collection->min(); // 1
  ```

- `median()`: Tính trung vị của collection.

  ```php
  $median = $collection->median(); // 3
  ```

- `mode()`: Tìm giá trị xuất hiện nhiều nhất trong collection.

  ```php
  $mode = $collection->mode(); // [2, 4]
  ```

- `countBy()`: Đếm số lần xuất hiện của các giá trị khác nhau trong collection.

  ```php
  $countBy = $collection->countBy();
  ```

## 5. Phương Thức Chuyển Đổi (Conversion Methods)
Dùng để chuyển đổi collection sang định dạng khác.

- `toArray()`: Chuyển đổi collection thành mảng.

  ```php
  $array = $collection->toArray();
  ```

- `toJson()`: Chuyển đổi collection thành chuỗi JSON.

  ```php
  $json = $collection->toJson();
  ```

- `implode()`: Nối các giá trị thành một chuỗi với một ký tự phân cách.

  ```php
  $imploded = $collection->implode(', '); // "1, 2, 3, 4, 5"
  ```

## 6. Phương Thức Lặp Và Tiện Ích (Iteration and Utility Methods)
Các phương thức hỗ trợ lặp qua từng phần tử hoặc thực hiện các hành động tiện ích.

- `each()`, `eachSpread()`: Lặp qua từng phần tử và áp dụng một hàm lên chúng.

  ```php
  $collection->each(function ($item) {
      echo $item;
  });
  ```

- `tap()`: Thực hiện một hành động trên collection mà không làm thay đổi nó.

  ```php
  $collection->tap(function ($col) {
      // Do something with $col
  });
  ```

- `dump()`, `dd()`: Hiển thị và dừng thực thi, hữu ích cho mục đích gỡ lỗi.

  ```php
  $collection->dd();
  ```

- `times()`: Tạo một collection với số lượng phần tử xác định và áp dụng hàm cho mỗi phần tử.

  ```php
  $times = Collection::times(5, function ($number) {
      return $number * 2;
  });
  ```

- `macro()`: Đăng ký macro để mở rộng các chức năng của collection.

  ```php
  Collection::macro('toUpper', function () {
      return $this->map(function ($item) {
          return strtoupper($item);
      });
  });
  ```

- `make()`: Tạo một collection từ một giá trị cho trước.

  ```php
  $collection = Collection::make([1, 2, 3]);
  ```

- `pipe()`: Chuyển collection vào một hàm và trả về kết quả.

  ```php
  $result = $collection->pipe(function ($col) {
      return $col->sum();
  });
  ```

- `unwrap()`, `wrap()`: Bọc hoặc mở một giá trị thành collection.

  ```php
  $wrapped = Collection::wrap('Hello');
  ```

- `shift()`: Lấy và xóa phần tử đầu tiên của collection.

  ```php
  $firstItem = $collection->shift();
  // Kết quả của $firstItem: 1
  // Kết quả của collection: [2, 3, 4, 5]
  ```

- `pop()`: Lấy và xóa phần tử cuối cùng của collection.

  ```php
  $lastItem = $collection->pop();
  // Kết quả của $lastItem: 5
  // Kết quả của collection: [1, 2, 3, 4]
  ```

- `prepend()`: Thêm một phần tử vào đầu collection.

  ```php
  $collection->prepend(0);
  // Kết quả: [0, 1, 2, 3, 4, 5]
  ```

- `push()`: Thêm một phần tử vào cuối collection.

  ```php
  $collection->push(6);
  // Kết quả: [1, 2, 3, 4, 5, 6]
  ```

- `put()`: Thêm hoặc cập nhật một phần tử với khóa cụ thể.

  ```php
  $collection = collect(['name' => 'John']);
  $collection->put('age', 30);
  // Kết quả: ['name' => 'John', 'age' => 30]
  ```

- `set()`: Tương tự như `put()`, được sử dụng để đặt giá trị cho một khóa.

  ```php
  $collection->set('location', 'Hanoi');
  // Kết quả: ['name' => 'John', 'age' => 30, 'location' => 'Hanoi']
  ```

- `forget()`: Xóa một phần tử khỏi collection bằng khóa của nó.

  ```php
  $collection->forget('age');
  // Kết quả: ['name' => 'John', 'location' => 'Hanoi']
  ```

- `pull()`: Lấy ra và xóa một phần tử khỏi collection.

  ```php
  $value = $collection->pull('name');
  // Kết quả của $value: 'John'
  // Kết quả của collection: ['location' => 'Hanoi']
  ```

## 7. Phương Thức Điều Kiện (Conditional Methods)
Dùng để thực thi các thao tác dựa trên điều kiện.

- `when()`, `whenEmpty()`, `whenNotEmpty()`: Thực hiện hành động dựa trên điều kiện hoặc tình trạng rỗng của collection.

  ```php
  $collection->when(true, function ($col) {
      return $col->push(6);
  });
  ```

- `unless()`, `unlessEmpty()`, `unlessNotEmpty()`: Tương tự như `when()`, nhưng là ngược lại.

  ```php
  $collection->unless(false, function ($col) {
      return $col->push(7);
  });
  ```

## 8. Phương Thức Khác (Miscellaneous Methods)
Các phương thức khác không thuộc các nhóm trên.

- `concat()`: Nối collection với một tập giá trị khác.

  ```php
  $concatenated = $collection->concat([6, 7, 8]);
  ```

- `forPage()`: Chia collection thành các trang dựa trên số lượng phần tử mỗi trang.

  ```php
  $forPage = $collection->forPage(2, 2);
  // Kết quả: [3, 4]
  ```

- `keyBy()`: Đặt lại khóa của collection dựa trên kết quả của hàm cho trước.

  ```php
  $keyed = $collection->keyBy('id');
  ```

- `pluck()`: Lấy ra một cột hoặc một tập hợp giá trị dựa trên khóa.

  ```php
  $plucked = $collection->pluck('name');
  ```

## 9. Phương Thức Phân Vùng (Partitioning Methods)
Dùng để chia collection thành các phần khác nhau.

- `partition()`: Chia collection thành hai phần dựa trên kết quả của hàm cho trước.

  ```php
  [$under18, $above18] = $collection->partition(function ($item) {
      return $item['age'] < 18;
  });
  ```

## 10. Phương Thức Nhóm (Grouping Methods)
Dùng để nhóm các phần tử lại với nhau dựa trên một tiêu chí.

- `groupBy()`: Nhóm các phần tử trong collection lại với nhau.

  ```php
  $grouped = $collection->groupBy('type');
  ```

- `mapToGroups()`: Biến đổi và nhóm các phần tử dựa trên kết quả của hàm cho trước.

  ```php
  $grouped = $collection->mapToGroups(function ($item, $key) {
      return [$item['category'] => $item['name']];
  });
  ```

## 11. Phương Thức Nối (Join Methods)
Dùng để nối dữ liệu trong collection.

- `join()`: Nối các phần tử thành một chuỗi, với dấu phân cách tùy chỉnh.

  ```php
  $joined = $collection->join(', ');
  // Kết quả: "1, 2, 3, 4, 5"
  ```

## 12. Phương Thức Cập Nhật Và Thêm Phần Tử (Update and Add Methods)
Dùng để cập nhật hoặc thêm phần tử mới vào collection.

- `push()`: Thêm một phần tử vào cuối collection.

  ```php
  $collection->push(6);
  // Kết quả: [1, 2, 3, 4, 5, 6]
  ```

- `put()`: Đặt một cặp khóa - giá trị vào collection.

  ```php
  $collection->put(10, 'new value');
  // Kết quả: [1, 2, 3, 4, 5, 10 => 'new value']
  ```

- `prepend()`: Thêm một phần tử vào đầu collection.

  ```php
  $collection->prepend(0);
  // Kết quả: [0, 1, 2, 3, 4, 5]
  ```

- `add()`: Alias của `push()`.

  ```php
  $collection->add(7);
  // Kết quả: [1, 2, 3, 4, 5, 7]
  ```

- `forget()`: Xóa một phần tử dựa trên khóa.

  ```php
  $collection->forget(1);
  // Kết quả: [0 => 1, 2 => 3, 3 => 4, 4 => 5]
  ```

- `set()`: Cập nhật giá trị cho một khóa cụ thể.

  ```php
  $collection->set(2, 'updated value');
  // Kết quả: [1, 'updated value', 3, 4, 5]
  ```

- `update()`: Cập nhật nhiều giá trị trong collection.

  ```php
  $collection = collect(['name' => 'John', 'age' => 30]);
  $updated = $collection->update(['age' => 31, 'city' => 'New York']);
  // Kết quả: ['name' => 'John', 'age' => 31, 'city' => 'New York']
  ```