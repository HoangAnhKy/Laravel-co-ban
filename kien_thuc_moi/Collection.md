`Collections` trong Laravel là một lớp mạnh mẽ được xây dựng trên lớp `Illuminate\Support\Collection`. Collections giúp xử lý dữ liệu, đặc biệt là các mảng và kết quả của các câu truy vấn, một cách hiệu quả và tiện lợi hơn so với việc sử dụng mảng thông thường. Collections có rất nhiều phương thức hữu ích giúp thao tác dữ liệu dễ dàng, nhanh chóng và mạch lạc.

# Tạo Collection

```php
$collection = collect([1,2,3,4,5]);
```

# Phân Loại Các Hàm Theo Ngữ Cảnh Sử Dụng

Dưới đây là phân loại các hàm dựa trên việc chúng thường được sử dụng với **mảng/collections** hay với **cơ sở dữ liệu (database)**, kèm theo mô tả ngắn gọn cho từng hàm. Một số hàm có thể được sử dụng trong cả hai ngữ cảnh tùy thuộc vào cách thức áp dụng.

## 1. Hàm Thường Dùng Với Mảng / Collections (Arrays/Collections)

Các hàm này thường được sử dụng để thao tác trực tiếp trên các mảng hoặc collections trong bộ nhớ (in-memory). Chúng giúp xử lý, biến đổi và quản lý dữ liệu một cách hiệu quả.

### Lọc và Kiểm Tra:

- **`after`**: Lấy tất cả các phần tử sau phần tử đầu tiên thỏa mãn điều kiện.

  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $collection->after(3); // 4
  // Dùng chế độ nghiêm ngặt
  $collection->after('4', strict: true); // false
  // Callback
  $collection->after(function (int $value, int $key) {
      return $value == 3;
  }); // 4
  ```

- **`before`**: Lấy tất cả các phần tử trước phần tử đầu tiên thỏa mãn điều kiện.

  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $collection->before(3); // 2
  // Dùng chế độ nghiêm ngặt
  $collection->before('4', strict: true); // false
  // Callback
  $collection->before(function (int $value, int $key) {
      return $value == 3;
  }); // 2
  ```

- **`contains`** và **`some`**: Kiểm tra xem mảng có chứa một giá trị cụ thể hay không.

  ```php
    $collection = collect([1, 2, 3, 4, 5]);
    // Callback
    $collection->contains(function (int $value, int $key) {
        return $value > 5;
    }); // false

    // check string
    $collection = collect(['name' => 'Desk', 'price' => 100]);
    $collection->contains('Desk');// true
    $collection->contains('New York'); // false'

    // check key value;
    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
    ]);

    $collection->contains('product', 'Bookcase');
  ```

- **`containsOneItem`**: Kiểm tra xem mảng có chứa đúng một phần tử hay không.

  ```php
  collect([])->containsOneItem(); // false

  collect(['1'])->containsOneItem(); // true

  collect(['1', '2'])->containsOneItem(); // false
  ```

- **`containsStrict`**: Kiểm tra xem mảng có chứa một giá trị cụ thể với kiểu dữ liệu chính xác hay không. Cách dùng giống như `contains`.
- **`doesntContain`**: Kiểm tra xem mảng **không chứa** một giá trị cụ thể. Ngược lại với `Contain`

  ```php
  // Call Back
  $collection = collect([1, 2, 3, 4, 5]);
  $collection->doesntContain(function (int $value, int $key) {
      return $value < 5;
  }); // false

  // Check String value
  $collection = collect(['name' => 'Desk', 'price' => 100]);
  $collection->doesntContain('Table');// true
  $collection->doesntContain('Desk');// false

  // Check key value
    $collection = collect([
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Chair', 'price' => 100],
    ]);
    $collection->doesntContain('product', 'Bookcase');// true
  ```

- **`every`**: Kiểm tra xem tất cả các phần tử trong mảng có thỏa mãn điều kiện không.

  ```php

  collect([1, 2, 3, 4])->every(function (int $value, int $key) {
      return $value > 2;
  }); // false

  ```

- **`filter`**: Lọc các phần tử trong mảng dựa trên điều kiện cho trước.

  ```php
  $collection = collect([1, 2, 3, 4]);
  // callback
  $filtered = $collection->filter(function (int $value, int $key) {
      return $value > 2;
  });

  $filtered->all() // [3, 4]

  // Khi không dùng callback thì những gì tương đương với "False" sẽ bị xóa
  $collection = collect([1, 2, 3, null, false, '', 0, []]);
  $collection->filter()->all();// [1, 2, 3]
  ```

- **`reject`**: Loại bỏ các phần tử trong mảng dựa trên điều kiện cho trước. Ngược lại với `filter`

  ```php
  $collection = collect([1, 2, 3, 4]);

  $filtered = $collection->reject(function (int $value, int $key) {
      return $value > 2;
  });

  $filtered->all(); // [1, 2]
  ```

- **`isEmpty`**: Kiểm tra xem mảng có rỗng hay không.
  ```php
  collect([])->isEmpty(); // true
  ```
- **`isNotEmpty`**: Kiểm tra xem mảng có không rỗng hay không.

  ```php
  collect([])->isNotEmpty(); // false
  ```

- **`has`**: Kiểm tra xem mảng có chứa một khóa(**Key**) cụ thể hay không.
  ```php
  $collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);
  $collection->has('product');// true
  $collection->has(['product', 'amount']);// true
  $collection->has(['amount', 'price']);// false
  ```
- **`hasAny`**: Kiểm tra xem mảng có chứa bất kỳ một trong các khóa (**Key**) cụ thể hay không.

  ```php
  $collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);

  $collection->hasAny(['product', 'price']);// true
  $collection->hasAny(['name', 'price']);// false
  ```

- **`sole`**: Lấy phần tử duy nhất trong mảng thỏa mãn điều kiện, hoặc trả về lỗi nếu không có hoặc nhiều hơn một.
  ```php
  collect([1, 2, 3, 4])->sole(function (int $value, int $key) {
      return $value === 2;
  });// 2
  ```

### Biến Đổi và Chuyển Đổi:

- **`map`**: Áp dụng một hàm cho mỗi phần tử trong mảng và trả về mảng mới với kết quả.

  ```php
  $collection = collect([1, 2, 3, 4, 5]);

  $multiplied = $collection->map(function (int $item, int $key) {
      return $item * 2;
  });

  $multiplied->all(); // [2, 4, 6, 8, 10]
  ```

- **`mapInto`**: Chuyển đổi mỗi phần tử trong mảng thành một đối tượng cụ thể.
  ```php
  class Currency
  {
      /**
       * Create a new currency instance.
       */
      function __construct(
          public string $code,
      ) {}
  }
  $collection = collect(['USD', 'EUR', 'GBP']);
  $currencies = $collection->mapInto(Currency::class);
  $currencies->all(); // [Currency('USD'), Currency('EUR'), Currency('GBP')]
  ```
- **`mapSpread`**: Giải nén các mảng con và áp dụng hàm cho từng phần tử.

  ```php
  $collection = collect([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);

  $chunks = $collection->chunk(2); // chia mảng thành mảng 2 chiều [[1,2], [3,4], [5,6], [7,8], [9,10]]

  $sequence = $chunks->mapSpread(function (int $even, int $odd) {
      return $even + $odd;
  });

  $sequence->all(); // [1, 5, 9, 13, 17]
  ```

- **`mapToGroups`**: Nhóm các phần tử theo một khóa nhất định.

  ```php
  $collection = collect([
      [
          'name' => 'John Doe',
          'department' => 'Sales',
      ],
      [
          'name' => 'Jane Doe',
          'department' => 'Sales',
      ],
      [
          'name' => 'Johnny Doe',
          'department' => 'Marketing',
      ]
  ]);

    $grouped = $collection->mapToGroups(function (array $item, int $key) {
        return [$item['department'] => $item['name']];
    });

    $grouped->all();
    /*
        [
            'Sales' => ['John Doe', 'Jane Doe'],
            'Marketing' => ['Johnny Doe'],
        ]
    */
  ```

- **`mapWithKeys`**: Tạo mảng mới với các khóa và giá trị được xác định bởi hàm.

  ```php
  $collection = collect([
      [
          'name' => 'John',
          'department' => 'Sales',
          'email' => 'john@example.com',
      ],
      [
          'name' => 'Jane',
          'department' => 'Marketing',
          'email' => 'jane@example.com',
      ],
      [
          'name' => 'Jane',
          'department' => 'Marketing2',
          'email' => 'jane@example.com',
      ]
  ]);

  $keyed = $collection->mapWithKeys(function (array $item, int $key) {
      return [
          $item['email'] => $item['name'],
          $item["department"] => $item["email"]
        ];
  });
  $keyed->all();
  /*
    tuy cho 2 case nhưng mảng trả về nó sẽ có mảnh với
    key => value không trùng nhau
    [
      "john@example.com" => "John",
      "Sales" => "john@example.com",
      "jane@example.com" => "Jane",
      "Marketing" => "jane@example.com",
      "Marketing2" => "jane@example.com"
    ]
  */
  ```

- **`flatMap`**: Áp dụng một hàm cho mỗi phần tử và làm phẳng mảng kết quả.

  ```php
  $collection = collect([
      ['name' => 'Sally'],
      ['school' => 'Arkansas'],
      ['age' => 28]
  ]);

  $flattened = $collection->flatMap(function (array $values) {
      return array_map('strtoupper', $values);
  });
  dd($flattened->all()); // ['name' => 'SALLY', 'school' => 'ARKANSAS', 'age' => '28'];
  ```

- **`flatten`**: Làm phẳng mảng đa chiều thành mảng đơn chiều.
  ```php
  $collection = collect([
      'name' => 'taylor',
      'languages' => [
          'php', 'javascript'
      ]
  ]);
  $flattened = $collection->flatten();
  $flattened->all();// ['taylor', 'php', 'javascript'];
  ```
- **`transform`**: Biến đổi mảng bằng cách áp dụng một hàm cho mỗi phần tử. Tuy khá giống `Map` nhưng map là tạo collection mới còn `transform` sửa trực tiếp

  ```php
  $collection = collect([1, 2, 3, 4, 5]);

  $collection->transform(function (int $item, int $key) {
      return $item * 2;
  });

  $collection->all(); // [2, 4, 6, 8, 10]
  ```

- **`dot`**: Chuyển đổi mảng đa chiều thành mảng đơn chiều với khóa dạng "dot notation".

  ```php
  $collection = collect(['products' => ['desk' => ['price' => 100]]]);
  $flattened = $collection->dot();
  $flattened->all // ['products.desk.price' => 100]
  ```

- **`undot`**: Chuyển đổi các khóa dạng "dot notation" thành mảng đa chiều.

  ```php
    $person = collect([
        'name.first_name' => 'Marie',
        'name.last_name' => 'Valentine',
        'address.line_1' => '2992 Eagle Drive',
        'address.line_2' => '',
        'address.suburb' => 'Detroit',
        'address.state' => 'MI',
        'address.postcode' => '48219'
    ]);

    $person = $person->undot();

    $person->toArray();

    /*
        [
            "name" => [
                "first_name" => "Marie",
                "last_name" => "Valentine",
            ],
            "address" => [
                "line_1" => "2992 Eagle Drive",
                "line_2" => "",
                "suburb" => "Detroit",
                "state" => "MI",
                "postcode" => "48219",
            ],
        ]
    */
  ```

- **`flip`**: Hoán đổi khóa và giá trị trong mảng.

  ```php
  $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);

  $flipped = $collection->flip();

  $flipped->all(); // ['taylor' => 'name', 'laravel' => 'framework']
  ```

- **`reverse`**: Đảo ngược thứ tự các phần tử trong mảng.
  ```php
  $collection = collect(['a', 'b', 'c', 'd', 'e']);
  $reversed = $collection->reverse();
  $reversed->all(); ['e', 'd', 'c', 'b', 'a'];
  ```
- **`implode`**: Kết hợp các phần tử trong mảng thành một chuỗi với dấu phân cách.

  ```php
  $collection = collect([
      ['account_id' => 1, 'product' => 'Desk'],
      ['account_id' => 2, 'product' => 'Chair'],
  ]);

  $collection->implode('product', ', '); // Desk, Chair
  ```

- **`pluck`**: Trích xuất giá trị của một khóa cụ thể từ mỗi phần tử trong mảng.

  ```php
  $collection = collect([
      ['product_id' => 'prod-100', 'name' => 'Desk'],
      ['product_id' => 'prod-200', 'name' => 'Chair'],
  ]);

  $plucked = $collection->pluck('name');

  $plucked->all();// ['Desk', 'Chair']
  ```

- **`wrap`**: Bọc giá trị vào trong một collection nếu nó chưa phải là collection.

  ```php
  use Illuminate\Support\Collection;

  $collection = Collection::wrap('John Doe');

  $collection->all();// ['John Doe']
  // dùng mảng
  $collection = Collection::wrap(['John Doe']);

  $collection->all();// ['John Doe']

  // collection
  $collection = Collection::wrap(collect('John Doe'));

  $collection->all();// ['John Doe']
  ```

- **`unwrap`**: Lấy giá trị bên trong collection. Ngược lại với `wrap`

  ```php
  Collection::unwrap(collect('John Doe'));// ['John Doe']

  Collection::unwrap(['John Doe']); // ['John Doe']

  Collection::unwrap('John Doe'); // 'John Doe'
  ```

- **`ensure`**: Đảm bảo rằng một giá trị là một collection.

  ```php
  //  string, int, float, bool, và array
  // $collection->ensure(Users::class); cũng được

  $collection = collect([1, 2, 3]);
  $newCollection = $collection->ensure('string'); // lỗi : Collection should only include [string] items, but 'int' found at position 0.

  $newCollection = $collection->ensure('int');
  var_dump($newCollection->all()); //[1,2,3]
  ```

- **`pipe`**: Truyền collection qua một hàm xử lý.
  ```php
  $collection = collect([1, 2, 3]);
  $piped = $collection->pipe(function (Collection $collection) {
      return $collection->sum();
  });// 6
  ```
- **`pipeInto`**: Truyền collection vào một `class` cụ thể để xử lý.

  ```php
  class ResourceCollection
  {
      /**
       * Create a new ResourceCollection instance.
       */
      public function __construct(
          public Collection $collection,
      ) {}
  }

  $collection = collect([1, 2, 3]);

  $resource = $collection->pipeInto(ResourceCollection::class);

  $resource->collection->all(); // [1, 2, 3]
  ```

- **`pipeThrough`**: Truyền collection qua một chuỗi các class xử lý.

  ```php
  use Illuminate\Support\Collection;

  $collection = collect([1, 2, 3]);

  $result = $collection->pipeThrough([
      function (Collection $collection) {
          return $collection->merge([4, 5]);
      },
      function (Collection $collection) {
          return $collection->sum();
      },
  ]); // 15
  ```

- **`value`**: Phương pháp này lấy một giá trị nhất định từ phần tử đầu tiên của bộ sưu tập:

  ```php
  $collection = collect([
      ['product' => 'Desk', 'price' => 200],
      ['product' => 'Speaker', 'price' => 400],
  ]);

  $value = $collection->value('price');// 200
  ```

- **`values`**: trả về một bộ sưu tập mới với các khóa được đặt lại thành số nguyên liên tiếp

  ```php
  $collection = collect([
      10 => ['product' => 'Desk', 'price' => 200],
      11 => ['product' => 'Desk', 'price' => 200],
  ]);

  $values = $collection->values();

  $values->all();

  /*
      [
          0 => ['product' => 'Desk', 'price' => 200],
          1 => ['product' => 'Desk', 'price' => 200],
      ]
  */
  ```

### Tính Toán và Tổng Hợp:

- **`avg`** và **`average`**: Tính giá trị trung bình của các phần tử trong mảng.

  ```php
  // use key in array
  $average = collect([
      ['foo' => 10],
      ['foo' => 10],
      ['foo' => 20],
      ['foo' => 40]
  ])->avg('foo'); // 20
  
  // not use key
  $average = collect([1, 1, 2, 4])->avg();// 2
  ```
- **`count`**: Đếm số lượng phần tử trong mảng.

  ```php
  $collection = collect([1, 2, 3, 4]);
 
  $collection->count(); // 4
  ```
- **`countBy`**: Đếm số lượng phần tử theo một tiêu chí cụ thể.

  ```php
  $collection = collect([1, 2, 2, 2, 3]);
  $counted = $collection->countBy();  
  $counted->all();// [1 => 1, 2 => 3, 3 => 1]
  ```
- **`max`**: Tìm giá trị lớn nhất trong mảng.

  ```php
  $max = collect([
    ['foo' => 10],
    ['foo' => 20]
  ])->max('foo'); // 20

  $max = collect([1, 2, 3, 4, 5])->max(); // 5
  ```
- **`median`**: Tính giá trị trung vị của các phần tử trong mảng.

  ```php
  $median = collect([
      ['foo' => 10],
      ['foo' => 10],
      ['foo' => 20],
      ['foo' => 40]
  ])->median('foo');// 15
  
  $median = collect([1, 1, 2, 4])->median();// 1.5
  ```
- **`min`**: Tìm giá trị nhỏ nhất trong mảng.

  ```php
  $min = collect([['foo' => 10], ['foo' => 20]])->min('foo');// 10
  $min = collect([1, 2, 3, 4, 5])->min();// 1
  ```
- **`mode`**: Tìm giá trị xuất hiện nhiều nhất trong mảng.

  ```php
  $mode = collect([
    ['foo' => 10],
    ['foo' => 10],
    ['foo' => 20],
    ['foo' => 40]
  ])->mode('foo');// [10]

  $mode = collect([1, 1, 2, 2])->mode();// [1, 2]
  ```
- **`sum`**: Tính tổng các phần tử trong mảng.

  ```php
  collect([1, 2, 3, 4, 5])->sum();// 15

  collect([
      ['foo' => 10],
      ['foo' => 10],
      ['foo' => 20],
      ['foo' => 40]
  ])->sum('foo'); // 80
  ```
- **`multiply`**: Nhân các phần tử trong mảng với nhau.

  ```php
  collect([3])->multiply(3);// [3,3,3]
  ```
- **`percentage`**: Tính tỷ lệ phần trăm của các phần tử trong mảng.
  ```php
  $collection = collect([1, 1, 2, 2, 2, 3]);

  $percentage = $collection->percentage(fn ($value) => $value === 1); // 33.33
  ```

### Nhóm và Tổ Chức:

- **`groupBy`**: Nhóm các phần tử theo một khóa nhất định.

  ```php
  // có thể dùng callable|array|string 

  $collection = collect([
      ['account_id' => 'account-x10', 'product' => 'Chair'],
      ['account_id' => 'account-x10', 'product' => 'Bookcase'],
      ['account_id' => 'account-x11', 'product' => 'Desk'],
  ]);
  
  $grouped = $collection->groupBy('account_id');
  
  /*
    $collection->groupBy(function (array $item, int $key) {
      return substr($item['account_id'], -3);
    });

    dùng cho mảng đa chiều

    $data->groupBy(['skill', function (array $item) {
      return $item['roles'];
    }], preserveKeys: true);

  */

  $grouped->all();
  
  /*
      [
          'account-x10' => [
              ['account_id' => 'account-x10', 'product' => 'Chair'],
              ['account_id' => 'account-x10', 'product' => 'Bookcase'],
          ],
          'account-x11' => [
              ['account_id' => 'account-x11', 'product' => 'Desk'],
          ],
      ]
  */
  ```
- **`keyBy`**: Tạo khóa cho mảng dựa trên một thuộc tính của phần tử.

  ```php
  $collection = collect([
    ['product_id' => 'prod-100', 'name' => 'Desk'],
    ['product_id' => 'prod-200', 'name' => 'Chair'],
  ]);

  $keyed = $collection->keyBy('product_id');

  $keyed->all();

  /*
    [
        'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
        'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
    ]
  */
  ```
### Sắp Xếp và Trật Tự:

- **`sort`**: Sắp xếp các phần tử trong mảng.

  ```php
  $collection = collect([5, 3, 1, 2, 4]);
 
  $sorted = $collection->sort();
  // collection được sắp xếp giữ lại các khóa mảng ban đầu, vì vậy trong ví dụ sau, chúng ta sẽ sử dụng phương `values` pháp để đặt lại các khóa thành các chỉ mục được đánh số liên tiếp
  $sorted->values()->all();// [1, 2, 3, 4, 5]
  ```
- **`sortBy`**: Sắp xếp mảng theo một khóa cụ thể.
  ```php

  $collection = collect([
      ['name' => 'Desk', 'price' => 200],
      ['name' => 'Chair', 'price' => 100],
      ['name' => 'Bookcase', 'price' => 150],
  ]);
  
  $sorted = $collection->sortBy('price');
  
  $sorted->values()->all();
  
  /*
      [
          ['name' => 'Chair', 'price' => 100],
          ['name' => 'Bookcase', 'price' => 150],
          ['name' => 'Desk', 'price' => 200],
      ]
  */
  ```
- **`sortByDesc`**: Sắp xếp mảng theo một khóa cụ thể theo thứ tự giảm dần. Ngược lại của **`sortBy`**

- **`sortDesc`**: Sắp xếp mảng theo thứ tự giảm dần. Ngược lại của **`sort`**

- **`sortKeys`**: Sắp xếp các khóa trong mảng.

  ```php
  $collection = collect([
      'id' => 22345,
      'first' => 'John',
      'last' => 'Doe',
  ]);
  
  $sorted = $collection->sortKeys();
  
  $sorted->all();
  
  /*
      [
          'first' => 'John',
          'id' => 22345,
          'last' => 'Doe',
      ]
  */
  ```
- **`sortKeysDesc`**: Sắp xếp các khóa trong mảng theo thứ tự giảm dần. Ngược lại của **`sortKeys`**

- **`sortKeysUsing`**: Sắp xếp các khóa trong mảng bằng một hàm so sánh tùy chỉnh.

  ```php
  $collection = collect([
      'ID' => 22345,
      'first' => 'John',
      'last' => 'Doe',
  ]);
  
  $sorted = $collection->sortKeysUsing('strnatcasecmp'); // gọi hàm callback strnatcasecmp();
  
  $sorted->all();
  
  /*
      [
          'first' => 'John',
          'ID' => 22345,
          'last' => 'Doe',
      ]
  */
  ```

- **`shuffle`**: Xáo trộn thứ tự các phần tử trong mảng.
  
  ```php
  $collection = collect([1, 2, 3, 4, 5]);
 
  $shuffled = $collection->shuffle();
  
  $shuffled->all();
  
  // [3, 2, 5, 1, 4] - (generated randomly)
  ```

### Phân Chia và Chia Nhỏ:

- **`chunk`**: Chia mảng thành các mảng con có kích thước cố định.
  ```php
  $collection = collect([1, 2, 3, 4, 5, 6, 7]);
 
  $chunks = $collection->chunk(4);
  
  $chunks->all();// [[1, 2, 3, 4], [5, 6, 7]]
  ```
- **`chunkWhile`**: Chia mảng thành các mảng con dựa trên điều kiện cho trước.

  ```php
  $collection = collect(str_split('AABBCCCD'));
 
  $chunks = $collection->chunkWhile(function (string $value, int $key, Collection $chunk) {
      // kiểm tra nếu phần tử hiện tại ($value) bằng phần tử cuối của chunk hiện tại ($chunk->last()), thì tiếp tục thêm vào chunk hiện tại.
      return $value === $chunk->last();
  });
 
  $chunks->all(); // [['A', 'A'], ['B', 'B'], ['C', 'C', 'C'], ['D']]
  ```
- **`partition`**: Phân chia mảng thành hai nhóm dựa trên một điều kiện.
  ```php
  $collection = collect([1, 2, 3, 4, 5, 6]);
 
  [$underThree, $equalOrAboveThree] = $collection->partition(function (int $i) {
      return $i < 3;
  });
  
  $underThree->all();// [1, 2]
  
  $equalOrAboveThree->all();// [3, 4, 5, 6]
  ```
- **`slice`**: Lấy một phần của mảng bắt đầu từ vị trí cụ thể.
  ```php
  $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
  // (Start, end) nếu ko có end sẽ lấy hết
  $slice = $collection->slice(4);
  
  $slice->all();// [5, 6, 7, 8, 9, 10]
  ```
- **`sliding`**: Tạo các cửa sổ trượt từ mảng.

  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $chunks = $collection->sliding(2,  step: 1);
  $chunks->toArray();// [[1, 2], [2, 3], [3, 4], [4, 5]]
  ```

- **`splice`**: Thêm hoặc loại bỏ phần tử từ mảng tại vị trí cụ thể.

  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  // (Start, end) nếu ko có end sẽ lấy hết
  $chunk = $collection->splice(2, 1);
  
  $chunk->all();// [3]
  
  $collection->all();// [1, 2, 4, 5]
  ```
### Kết Nối và Hợp Nhất:

- **`concat`**: Kết hợp hai hoặc nhiều mảng lại với nhau.

  ```php
  $collection = collect(['John Doe']);
 
  $concatenated = $collection->concat(['Jane Doe'])->concat(['name' => 'Johnny Doe']);
  
  $concatenated->all();// ['John Doe', 'Jane Doe', 'Johnny Doe']
  ```
- **`merge`**: Hợp nhất mảng với mảng khác.

  ```php
  $collection = collect(['product_id' => 1, 'price' => 100]);
  
  $merged = $collection->merge(['price' => 200, 'discount' => false]);
  
  $merged->all();// ['product_id' => 1, 'price' => 200, 'discount' => false]
  ```
- **`mergeRecursive`**: Hợp nhất mảng một cách đệ quy. Cùng key sẽ được merge chung với nhau

  ```php
  $collection = collect(['product_id' => 1, 'price' => 100]);
  
  $merged = $collection->mergeRecursive([
      'product_id' => 2,
      'price' => 200,
      'discount' => false
  ]);
  
  $merged->all();
  
  // ['product_id' => [1, 2], 'price' => [100, 200], 'discount' => false]
  ```
- **`union`**: Kết hợp mảng mà không trùng lặp các khóa.
  ```php
  $collection = collect([1 => ['a'], 2 => ['b']]);
 
  $union = $collection->union([3 => ['c'], 1 => ['d']]);

  $union->all();// [1 => ['a'], 2 => ['b'], 3 => ['c']]
  ```

### Truy Cập và Kiểm Tra Phần Tử:


- **`last`**: Lấy phần tử cuối cùng của mảng.
  ```php
  collect([1, 2, 3, 4])->last(function (int $value, int $key) {
      return $value < 3;
  }); // 2
  collect([1, 2, 3, 4])->last();// 4
  collect([])->last();// null
  ```
- **`first`**: Lấy phần tử đầu tiên của mảng.

  ```php
  collect([1, 2, 3, 4])->first(function (int $value, int $key) {
    return $value > 2;
  });// 3
  collect([])->first();// null
  collect([1,2,3])->first();// 1
  ```
- **`firstOrFail`**: Lấy phần tử đầu tiên hoặc trả về lỗi nếu mảng rỗng.

  ```php
  collect([1, 2, 3, 4])->firstOrFail(function (int $value, int $key) {
      return $value > 5;
  });// Throws ItemNotFoundException...

  collect([1, 2, 3, 4])->firstOrFail(function (int $value, int $key) {
    return $value > 2;
  });// 3
  ```

- **`firstWhere`**: Lấy phần tử đầu tiên thỏa mãn điều kiện.

  ```php
  $collection = collect([
      ['name' => 'Regena', 'age' => null],
      ['name' => 'Linda', 'age' => 14],
      ['name' => 'Diego', 'age' => 23],
      ['name' => 'Linda', 'age' => 84],
  ]);
  
  $collection->firstWhere('name', 'Linda');
  
  // ['name' => 'Linda', 'age' => 14]
  ```
- **`get`**: Truy cập giá trị tại một khóa cụ thể.
  ```php
  $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);
 
  $value = $collection->get('name');// taylor
  // "key" "default"
  $value = $collection->get('age', 34); // 34
  $collection->get('email', function () {
    return 'taylor@example.com';
  });// taylor@example.com


  ```
- **`nth`**: Lấy phần tử thứ n trong mảng.
  
  ```php
  $collection = collect(['a', 'b', 'c', 'd', 'e', 'f']);
  // (lấy bước nhảy, bắt đầu từ)
  $collection->nth(4);// ['a', 'e']
  $collection->nth(4,1);// ['b', 'f']
  ```
- **`only`**: Lấy các phần tử chỉ với các khóa được chỉ định.
  ```php
  $collection = collect([
      'product_id' => 1,
      'name' => 'Desk',
      'price' => 100,
      'discount' => false
  ]);
  
  $filtered = $collection->only(['product_id', 'name']);
  
  $filtered->all();
  
  // ['product_id' => 1, 'name' => 'Desk']
  ```
- **`except`**: Loại bỏ các phần tử với các khóa được chỉ định. Ngược lại **`only`**

  ```php
  $collection = collect(['product_id' => 1, 'price' => 100, 'discount' => false]);
  
  $filtered = $collection->except(['price', 'discount']);
  
  $filtered->all();
  
  // ['product_id' => 1]
  ```
- **`all`**: Lấy toàn bộ các phần tử trong collection.
  ```php 
  collect([1, 2, 3])->all();// [1, 2, 3]
  ```
### Quản Lý Phần Tử:

- **`each`**: Thực hiện một hành động cho mỗi phần tử trong mảng.
- **`eachSpread`**: Giải nén các mảng con và thực hiện hành động cho từng phần tử.
- **`tap`**: Thực hiện một hành động trên mảng mà không làm thay đổi nó.
- **`push`**: Thêm một phần tử vào cuối mảng.
- **`pull`**: Lấy và loại bỏ một phần tử khỏi mảng dựa trên khóa.
- **`prepend`**: Thêm một phần tử vào đầu mảng.
- **`pop`**: Lấy và loại bỏ phần tử cuối cùng của mảng.
- **`shift`**: Lấy và loại bỏ phần tử đầu tiên của mảng.

### Các Hàm Hỗ Trợ Khác:

- **`collect`**: Tạo một collection từ một mảng hoặc đối tượng.
- **`make`**: Tạo một collection mới.
- **`macro`**: Định nghĩa một macro mới cho collection.
- **`lazy`**: Tạo một collection theo cách lười (lazy).
- **`times`**: Tạo một collection chứa một số phần tử dựa trên số lần lặp.
- **`range`**: Tạo một collection chứa các số trong khoảng xác định.
- **`forPage`**: Lấy một trang cụ thể của mảng dựa trên số phần tử mỗi trang.
- **`dump`**: Hiển thị nội dung của collection để debug.
- **`dd`**: Dump và kết thúc script (Dump and Die).
- **`toArray`**: Chuyển đổi collection thành mảng.
- **`toJson`**: Chuyển đổi collection thành JSON.

## 2. Hàm Thường Dùng Với Cơ Sở Dữ Liệu (Databases)

Các hàm này thường được sử dụng trong việc xây dựng các truy vấn cơ sở dữ liệu, thường thông qua các ORM như Eloquent trong Laravel. Chúng giúp tương tác với dữ liệu được lưu trữ trong hệ quản trị cơ sở dữ liệu.

### Lọc và Điều Kiện:

- **`where`**: Thêm điều kiện lọc vào truy vấn.
- **`whereStrict`**: Thêm điều kiện lọc với so sánh nghiêm ngặt.
- **`whereBetween`**: Lọc các bản ghi trong khoảng giá trị xác định.
- **`whereIn`**: Lọc các bản ghi có giá trị thuộc một tập hợp xác định.
- **`whereInStrict`**: Lọc các bản ghi có giá trị thuộc một tập hợp xác định với so sánh nghiêm ngặt.
- **`whereNotBetween`**: Lọc các bản ghi không nằm trong khoảng giá trị xác định.
- **`whereNotIn`**: Lọc các bản ghi có giá trị không thuộc một tập hợp xác định.
- **`whereNotInStrict`**: Lọc các bản ghi có giá trị không thuộc một tập hợp xác định với so sánh nghiêm ngặt.
- **`whereNull`**: Lọc các bản ghi có giá trị NULL ở một cột cụ thể.
- **`whereNotNull`**: Lọc các bản ghi có giá trị không NULL ở một cột cụ thể.
- **`whereInstanceOf`**: Lọc các bản ghi thuộc một kiểu đối tượng cụ thể.

### Kết Nối và Hợp Nhất:

- **`join`**: Thực hiện phép kết nối (JOIN) giữa các bảng trong truy vấn.
- **`crossJoin`**: Thực hiện phép kết nối chéo giữa các bảng.
- **`zip`**: Kết hợp các kết quả truy vấn lại với nhau.

### Nhóm và Tổ Chức:

- **`groupBy`**: Nhóm các bản ghi theo một hoặc nhiều cột.

### Tổng Hợp và Đếm:

- **`count`**: Đếm số lượng bản ghi thỏa mãn điều kiện.
- **`countBy`**: Đếm số lượng bản ghi theo một tiêu chí cụ thể.
- **`sum`**: Tính tổng giá trị của một cột cụ thể.
- **`avg`**: Tính giá trị trung bình của một cột cụ thể.
- **`max`**: Tìm giá trị lớn nhất của một cột cụ thể.
- **`min`**: Tìm giá trị nhỏ nhất của một cột cụ thể.

### Truy Cập và Kiểm Tra:

- **`get`**: Lấy tất cả các bản ghi thỏa mãn truy vấn.
- **`first`**: Lấy bản ghi đầu tiên thỏa mãn truy vấn.
- **`firstOrFail`**: Lấy bản ghi đầu tiên hoặc trả về lỗi nếu không tìm thấy.
- **`firstWhere`**: Lấy bản ghi đầu tiên thỏa mãn điều kiện cụ thể.
- **`exists`**: Kiểm tra sự tồn tại của các bản ghi thỏa mãn truy vấn.

### Hàm Debug và Kiểm Tra:

- **`dd`**: Dump và kết thúc script (Dump and Die), thường dùng để kiểm tra truy vấn.

## 3. Hàm Có Thể Dùng Cả Với Mảng và Cơ Sở Dữ Liệu

Một số hàm có thể được sử dụng trong cả hai ngữ cảnh, tùy thuộc vào cách thức áp dụng. Chúng có thể thao tác trên các collections trong bộ nhớ hoặc được sử dụng trong các truy vấn cơ sở dữ liệu.

- **`all`**: Lấy toàn bộ các phần tử trong collection hoặc bản ghi trong DB.
- **`average`**: Tính giá trị trung bình của các phần tử trong mảng hoặc một cột trong DB.
- **`avg`**: Tương tự như `average`.
- **`count`**: Đếm phần tử trong mảng hoặc đếm số bản ghi trong DB.
- **`dd`**: Dump và kết thúc script.
- **`dump`**: Hiển thị nội dung để debug.
- **`filter`**: Lọc mảng hoặc áp dụng điều kiện trong truy vấn.
- **`first`**: Lấy phần tử đầu tiên từ mảng hoặc từ kết quả truy vấn.
- **`firstWhere`**: Lấy phần tử đầu tiên thỏa mãn điều kiện.
- **`get`**: Truy cập giá trị hoặc lấy bản ghi.
- **`groupBy`**: Nhóm mảng hoặc nhóm trong truy vấn.
- **`has`**: Kiểm tra có phần tử trong mảng hoặc trong DB.
- **`hasAny`**: Kiểm tra có bất kỳ phần tử nào trong mảng hoặc trong DB.
- **`join`**: Kết nối mảng hoặc bảng trong truy vấn.
- **`last`**: Lấy phần tử cuối cùng từ mảng hoặc từ kết quả truy vấn.
- **`map`**: Biến đổi mảng hoặc các trường trong truy vấn.
- **`max`**: Tìm giá trị lớn nhất trong mảng hoặc một cột trong DB.
- **`merge`**: Hợp nhất mảng hoặc kết quả truy vấn.
- **`mergeRecursive`**: Hợp nhất mảng một cách đệ quy.
- **`min`**: Tìm giá trị nhỏ nhất trong mảng hoặc một cột trong DB.
- **`pipe`**: Truyền collection hoặc truy vấn qua các hàm xử lý.
- **`pluck`**: Trích xuất giá trị từ mảng hoặc từ kết quả truy vấn.
- **`sort`**: Sắp xếp mảng hoặc sắp xếp kết quả truy vấn.
- **`sortBy`**: Sắp xếp mảng theo khóa hoặc sắp xếp truy vấn theo cột cụ thể.
- **`sortByDesc`**: Sắp xếp mảng theo khóa giảm dần hoặc truy vấn giảm dần.
- **`sortDesc`**: Sắp xếp giảm dần.
- **`sum`**: Tính tổng trong mảng hoặc trên một cột trong DB.
- **`tap`**: Thực hiện hành động mà không thay đổi dữ liệu.
- **`unless`**: Thực hiện hành động nếu điều kiện không thỏa mãn.
- **`unlessEmpty`**: Thực hiện hành động nếu collection không rỗng.
- **`unlessNotEmpty`**: Thực hiện hành động nếu collection rỗng.
- **`value`**: Trả về giá trị của collection hoặc truy vấn sau khi đã xử lý.
- **`when`**: Thực hiện hành động dựa trên điều kiện.
- **`whenEmpty`**: Thực hiện hành động nếu collection rỗng.
- **`whenNotEmpty`**: Thực hiện hành động nếu collection không rỗng.
- **`where`**: Thêm điều kiện lọc vào truy vấn hoặc lọc mảng.
- **`whereStrict`**: Thêm điều kiện lọc nghiêm ngặt vào truy vấn hoặc lọc mảng.
- **`whereIn`**: Lọc bản ghi trong DB hoặc mảng dựa trên tập hợp.
- **`whereInStrict`**: Lọc bản ghi với so sánh nghiêm ngặt.
- **`whereInstanceOf`**: Lọc bản ghi thuộc một kiểu đối tượng cụ thể hoặc kiểm tra trong mảng.
- **`whereNotIn`**: Lọc bản ghi không thuộc tập hợp hoặc mảng.
- **`whereNotInStrict`**: Lọc bản ghi không thuộc tập hợp với so sánh nghiêm ngặt.
- **`whereNotNull`**: Lọc bản ghi có giá trị không NULL hoặc kiểm tra trong mảng.
- **`whereNull`**: Lọc bản ghi có giá trị NULL hoặc kiểm tra trong mảng.

---

**Ghi chú:**

- Một số hàm có thể xuất hiện trong nhiều danh mục khác nhau do tính linh hoạt trong việc sử dụng.
- Các hàm như `groupBy`, `sort`, và `map` thường xuyên được sử dụng cả trong xử lý mảng/collections và xây dựng truy vấn cơ sở dữ liệu.
- Hàm `last` có thể được sử dụng trong cả hai ngữ cảnh tùy thuộc vào cách triển khai cụ thể.
- Hàm `dd` và `dump` thường được sử dụng để debug dữ liệu, áp dụng cho cả collections và kết quả truy vấn.
- Các hàm như `macro`, `lazy` liên quan đến việc mở rộng hoặc tối ưu hóa collections, nhưng cũng có thể ảnh hưởng đến cách thức các truy vấn được xây dựng hoặc thực thi.
