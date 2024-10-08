`Collections` trong Laravel là một lớp mạnh mẽ được xây dựng trên lớp `Illuminate\Support\Collection`. Collections giúp xử lý dữ liệu, đặc biệt là các mảng và kết quả của các câu truy vấn, một cách hiệu quả và tiện lợi hơn so với việc sử dụng mảng thông thường. Collections có rất nhiều phương thức hữu ích giúp thao tác dữ liệu dễ dàng, nhanh chóng và mạch lạc.

# Tạo Collection

```php
$collection = collect([1,2,3,4,5]); 
/* hoặc dùng biến tĩnh `make`
  
  use Illuminate\Support\Collection;
  $collection = Collection::make([1, 2, 3, 4, 5]);

*/
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

- **`diff`**: so sánh collection với collection khác hoặc array với array khác dựa trên các giá trị của nó

  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $diff = $collection->diff([2, 4, 6, 8]);
  $diff->all();// [1, 3, 5]
  ```
- **`diffAssoc`**: so sánh collection với collection khác hoặc array với array khác dựa trên các khóa và giá trị của nó

  ```php
  $collection = collect([
      'color' => 'orange',
      'type' => 'fruit',
      'remain' => 6,
  ]);
  
  $diff = $collection->diffAssoc([
      'color' => 'yellow',
      'type' => 'fruit',
      'remain' => 3,
      'used' => 6,
  ]);
  
  $diff->all();// ['color' => 'orange', 'remain' => 6]
  ```
- **`diffAssocUsing`**: Sẽ so sánh collection với collection khác hoặc array với array khác dựa trên **tên hàm callback** có sẵn hoặc tự code

  ```php
  $collection = collect([
      'color' => 'orange',
      'type' => 'fruit',
      'remain' => 6,
  ]);
  
  $diff = $collection->diffAssocUsing([
      'Color' => 'yellow',
      'Type' => 'fruit',
      'Remain' => 3,
  ], 'strnatcasecmp');
  
  $diff->all();// ['color' => 'orange', 'remain' => 6]
  ```

- **`diffKeys`**: so sánh dựa trên khóa của nó

  ```php
  $collection = collect([
      'one' => 10,
      'two' => 20,
      'three' => 30,
      'four' => 40,
      'five' => 50,
  ]);
  
  $diff = $collection->diffKeys([
      'two' => 2,
      'four' => 4,
      'six' => 6,
      'eight' => 8,
  ]);
  
  $diff->all();// ['one' => 10, 'three' => 30, 'five' => 50]
  ```

- **`duplicates`** và **`duplicatesStrict`**: truy xuất và trả về các giá trị trùng lặp từ bộ sưu tập

  ```php
  // Có thể sử dụng key để check với object
  $collection = collect(['a', 'b', 'a', 'c', 'b']);
  
  $collection->duplicates();// [2 => 'a', 4 => 'b']
  ```

- **`intersect`**: xóa bất kỳ giá trị nào khỏi collection gốc không có trong collection đã cho

  ```php
    $collection = collect(['Desk', 'Sofa', 'Chair']);
  
    $intersect = $collection->intersect(['Desk', 'Chair', 'Bookcase']);
    
    $intersect->all();// [0 => 'Desk', 2 => 'Chair']
  ```
- **`intersectAssoc`**: xóa bất kỳ giá trị nào khỏi collection gốc không có trong collection đã cho. Dựa trên key/value.

  ```php
  $collection = collect([
      'color' => 'red',
      'size' => 'M',
      'material' => 'cotton'
  ]);
  
  $intersect = $collection->intersectAssoc([
      'color' => 'blue',
      'size' => 'M',
      'material' => 'polyester'
  ]);
  
  $intersect->all();// ['size' => 'M']
  ```
- **`intersectByKeys`**: xóa bất kỳ giá trị nào khỏi collection gốc không có trong collection đã cho. Dựa trên key.

  ```php
  $collection = collect([
      'serial' => 'UX301', 'type' => 'screen', 'year' => 2009,
  ]);
  
  $intersect = $collection->intersectByKeys([
      'reference' => 'UX404', 'type' => 'tab', 'year' => 2011,
  ]);
  
  $intersect->all();// ['type' => 'screen', 'year' => 2009]
  ```
- **`random`**: trả về một mục ngẫu nhiên từ collection

  ```php
    $collection = collect([1, 2, 3, 4, 5]);
 
    $collection->random();// 4 - (retrieved randomly)
  ```
- **`take`**: lấy collection mới với số lượng mục được chỉ định

  ```php
  $collection = collect([0, 1, 2, 3, 4, 5]);
  
  $chunk = $collection->take(3);
  
  $chunk->all();// [0, 1, 2]
  ```
- **`takeUntil`** giống như `take` nhưng lọc qua callback trả về true mới dừng

  ```php
  $collection = collect([1, 2, 3, 4]);
  
  $subset = $collection->takeUntil(function (int $item) {
      return $item >= 3;
  });
  
  $subset->all();// [1, 2]
  ```
- **`takeWhile`**: ngược lại của `takeUntil` khi callback trả false nó mới dừng

  ```php
  $collection = collect([1, 2, 3, 4]);
  
  $subset = $collection->takeWhile(function (int $item) {
      return $item < 3;
  });
  
  $subset->all();// [1, 2]
  ```

- **`unique`** và **`uniqueStrict`**: trả về collection không bị trùng phần tử

  ```php
  // có thể sử dụng thêm key hoặc callback, mặc định là với giá trị của mảng một chiều

  $collection = collect([1, 1, 2, 2, 3, 4, 2]);
  
  $unique = $collection->unique();
  
  $unique->values()->all();// [1, 2, 3, 4]
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
- **`mapSpread`**: Giải nén các mảng con và áp dụng hàm cho từng phần tử. Ứng với mỗi biến bên trong hàm `callback` sẽ là phần tử trong mảng đa chiều

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
- **`reduce`**: biến collection thành một giá trị duy nhất, truyền kết quả của mỗi lần lặp vào lần lặp tiếp theo

  ```php
    $collection = collect([1, 2, 3]);
    
    $total = $collection->reduce(function (?int $carry, int $item) {
        return $carry + $item;
    }); // 6
    
    // đặt giá trị mặc định
    $total = $collection->reduce(function (?int $carry, int $item) {
        return $carry + $item;
    },4); // 10
  ```
- **`reduceSpread`**:  tương tự như `reduce`, nhưng với khả năng giải nén các giá trị từ collection thành nhiều tham số để truyền vào hàm callback

  ```php
  $collection = collect([
      [1, 2],
      [3, 4],
      [5, 6]
  ]);

  $result = $collection->reduceSpread(function ($carry, $a, $b) {
      return [$carry[0] + $a, $carry[1] + $b];
  }, [0, 0]);

  print_r($result); // [9, 12]

  ```
- **`skip`**: trả về một collection mới, trong đó số lượng phần tử nhất định được xóa khỏi phần đầu của collection.

  ```php
  $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
  $collection = $collection->skip(4);
  $collection->all();// [5, 6, 7, 8, 9, 10]
  ```

- **`skipUntil`** và **`skipWhile`**: Giống như `skip` nhưng sử dụng callback, bắt đầu bỏ qua khi điều kiện trong callback là true.

  ```php
    $collection = collect([1, 2, 3, 4]);
  

    // sẽ trả về 3,4 do là 3 thỏa mãn nó 
    $subset = $collection->skipUntil(function (int $item) {
        return $item >= 3;
    });
    
    $subset->all();// [3, 4]

    // sẽ trả về 4 do là 4 không thỏa mãn nó nữa
    $subset = $collection->skipWhile(function (int $item) {
        return $item <= 3;
    });
    
    $subset->all();// [4]
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
- **`multiply`**: lặp các giá trị tong collection tới n lần.

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

- **`split`** : Chia một tập hợp thành số nhóm nhất định. Nó sẽ chia đều các phần tử rồi nếu dư nó sẽ thêm trừ trên xuống

  ```php
  $collection = collect([1, 2, 3, 4, 5, 6, 7]);

  $split = $collection->split(3);
  $split->all(); // [[1,2,3],[4,5],[6,7]];

  ```
- **`splitIn`**: Giống như `split`nhưng nó chia đều hơn.

  ```php
  $collection = collect([1, 2, 3, 4, 5, 6, 7]);

  $split = $collection->splitIn(3);
  $split->all(); // [[1,2,3],[4,5,6],[7]];

  ```

### Kết Nối và Hợp Nhất:

- **`concat`**: Kết hợp hai hoặc nhiều mảng lại với nhau.

  ```php
  $collection = collect(['John Doe']);
 
  $concatenated = $collection->concat(['Jane Doe'])->concat(['name' => 'Johnny Doe']);
  
  $concatenated->all();// ['John Doe', 'Jane Doe', 'Johnny Doe']
  ```

- **`replace`**: hoạt động tương tự như `merge`, nhưng nó có thể ghi đè vào index đó, nếu không có sẽ thêm vào

  ```php
  $collection = collect(['Taylor', 'Abigail', 'James']);
  
  $replaced = $collection->replace([1 => 'Victoria', 3 => 'Finn']);
  
  $replaced->all();// ['Taylor', 'Victoria', 'James', 'Finn']
  ```
- **`replaceRecursive`**: hoạt động giống như `replace`, nhưng nó sẽ đệ quy vào mảng và áp dụng cùng một quy trình thay thế cho các giá trị bên trong phần tử (mảng đa chiều).

  ```php
  $collection = collect([
      'Taylor',
      'Abigail',
      [
          'James',
          'Victoria',
          'Finn'
      ]
  ]);
  
  $replaced = $collection->replaceRecursive([
      'Charlie',
      2 => [1 => 'King'] // Số 2 ở đây đại diện cho index 2
  ]);
  
  $replaced->all();
  // ['Charlie', 'Abigail', ['James', 'King', 'Finn']]
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

- **`combine`**: kết hợp các giá trị của collection, dưới dạng khóa, với các giá trị của một mảng hoặc collection:

  ```php
  $collection = collect(['name', 'age']);
 
  $combined = $collection->combine(['George', 29]);
  
  $combined->all();// ['name' => 'George', 'age' => 29]
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

- **`each`**: Thực hiện một hành động cho mỗi phần tử (loop) trong mảng một chiều.
 
  ```php
  $collection = collect([1, 2, 3, 4]);
 
  $collection->each(function (int $item, int $key) {
      // ...
  });
  ```
- **`eachSpread`**: Giải nén các mảng con và thực hiện hành động cho từng phần tử. Ứng với mỗi biến bên trong hàm `callback` sẽ là phần tử trong mảng đa chiều
  
  ```php
  $collection = collect([[1,2], [3,4], [5]]);
  $collection->eachSpread(function ($value_f, $value_l = null) {
      echo $value_f; // 135
      echo $value_l; // 123452 vì ở mục 3 sẽ ko có $value_l nên nó lấy về thằng đầu tiên.
  });

  ```
- **`tap`**: Thực hiện một hành động trên mảng mà không làm thay đổi nó.
  
  ```php
  $first = collect([2, 4, 3, 1, 5])
            ->sort()
            ->tap(function (Collection $collection) {
                print_r( $collection->values()->all()); // Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 ) 
            })
            ->shift();

  echo $first;// 1
  ```
- **`push`**: Thêm một phần tử vào cuối mảng.

  ```php
  $collection = collect([1, 2, 3, 4]);
 
  $collection->push(5);
  
  $collection->all();// [1, 2, 3, 4, 5]
  ```
- **`pull`**: Lấy và loại bỏ một phần tử khỏi mảng dựa trên khóa.

  ```php
  $collection = collect(['product_id' => 'prod-100', 'name' => 'Desk']);
  $collection->pull('name');// 'Desk'
  $collection->all();// ['product_id' => 'prod-100']
  ```
- **`prepend`**: Thêm một phần tử vào đầu mảng.

  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $collection->prepend(0);
  $collection->all();// [0, 1, 2, 3, 4, 5]

  // truyền biến thứ 2 thành key
  $collection = collect(['one' => 1, 'two' => 2]);
  $collection->prepend(0, 'zero');
  $collection->all();// ['zero' => 0, 'one' => 1, 'two' => 2]
  ```
- **`pop`**: Lấy và loại bỏ phần tử cuối cùng của mảng.
  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $collection->pop();// 5
  $collection->all();// [1, 2, 3, 4]

  // truyền index
  $collection->pop(3);// collect([5, 4, 3])
  $collection->all();// [1, 2]
  ```
- **`shift`**: Lấy và loại bỏ phần tử đầu tiên của mảng.
  ```php
    $collection = collect([1, 2, 3, 4, 5]);
 
  $collection->shift();// 1
  $collection->all();// [2, 3, 4, 5]
  
  // truyền index
  $collection->shift(3);// [1,2,3]
  $collection->all();// [4, 5]
  ```
- **`put`**: thêm key/value vào collection

  ```php
  $collection = collect(['product_id' => 1, 'name' => 'Desk']);
  
  $collection->put('price', 100);
  
  $collection->all();// ['product_id' => 1, 'name' => 'Desk', 'price' => 100]
  ```
- **`forget`**: xóa một mục khỏi collection theo khóa của nó

  ```php
  $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);
 
  // Forget a single key...
  $collection->forget('name');// ['framework' => 'laravel']
  
  // Forget multiple keys...
  $collection->forget(['name', 'framework']);  // []
  ```

- **`pad`**:  điền giá trị đã cho vào mảng cho đến khi mảng đạt đến kích thước đã chỉ định

  ```php
  $collection = collect(['A', 'B', 'C']);
  
  $filtered = $collection->pad(5, 0);
  
  $filtered->all();// ['A', 'B', 'C', 0, 0]
  
  $filtered = $collection->pad(-5, 0);
  
  $filtered->all();// [0, 0, 'A', 'B', 'C']
  ```

### Các Hàm Hỗ Trợ Khác:
- **`make`**: Phương thức tĩnh tạo ra một `collection` mới.
  ```php
  use Illuminate\Support\Collection;
  $collection = Collection::make([1, 2, 3, 4, 5]); // giống collect()
  ```
- **`macro`**: Định nghĩa một macro mới cho collection.

  ```php
  Collection::macro('toUpper', function () {
      return $this->map(function (string $value) {
          return Str::upper($value);
      });
  });
  
  $collection = collect(['first', 'second']);
  
  $upper = $collection->toUpper();
  ```
- **`lazy`**: Tạo một collection theo cách lười (lazy).
  
  ```php
  $lazyCollection = collect([1, 2, 3, 4])->lazy();

  $lazyCollection::class;  
  // Illuminate\Support\LazyCollection
  $lazyCollection->all();// [1, 2, 3, 4]
  ```
- **`times`**: Tạo một collection chứa một số phần tử dựa trên số lần lặp.
  ```php
  $collection = Collection::times(10, function (int $number) {
      return $number * 9;
  });
  $collection->all();// [9, 18, 27, 36, 45, 54, 63, 72, 81, 90]
  ```
- **`range`**: Tạo một collection chứa các số trong khoảng xác định.
  ```php
  $collection = collect()->range(3, 6);
  $collection->all();// [3, 4, 5, 6]
  ```
- **`forPage`**: Lấy một trang cụ thể của mảng dựa trên số phần tử mỗi trang.
  ```php
  $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
  $chunk = $collection->forPage(2, 3); // (page, perpage(limit on page))
  $chunk->all();// [4, 5, 6]
  ```
- **`dump`**: Hiển thị nội dung của collection để debug.

  ```php
  $collection = collect(['John Doe', 'Jane Doe']);

  $collection->dump();
  ```
- **`dd`**: Dump và kết thúc script (Dump and Die).

  ```php
  $collection = collect(['John Doe', 'Jane Doe']);

  $collection->dd();
  ```
- **`toArray`**: Chuyển đổi collection thành mảng.
  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $chunks = $collection->sliding(2);
  $chunks->toArray();
  ```
- **`toJson`**: Chuyển đổi collection thành JSON.

  ```php
  $collection = collect([1, 2, 3, 4, 5]);
  $chunks = $collection->sliding(2);
  $chunks->toArray();
  ```

## 2. Hàm Thường Dùng Với Cơ Sở Dữ Liệu (Databases)

Các hàm này thường được sử dụng trong việc xây dựng các truy vấn cơ sở dữ liệu, thường thông qua các ORM như Eloquent trong Laravel. Chúng giúp tương tác với dữ liệu được lưu trữ trong hệ quản trị cơ sở dữ liệu.

### Lọc và Điều Kiện:

- **`search`**: Tìm kiếm giá trị đã cho trong collection  trả về khóa của nó nếu tìm thấy, không sẽ trả về flase.

  ```php
    $collection = collect([2, 4, 6, 8]);
  
    $collection->search(4);// 1
  ```
- **`select`**: chọn các khóa đã cho từ collection, tương tự như SELECT câu lệnh SQL:

  ```php
  $users = collect([
      ['name' => 'Taylor Otwell', 'role' => 'Developer', 'status' => 'active'],
      ['name' => 'Victoria Faith', 'role' => 'Researcher', 'status' => 'active'],
  ]);
  
  $users->select(['name', 'role']);
  
  /*
      [
          ['name' => 'Taylor Otwell', 'role' => 'Developer'],
          ['name' => 'Victoria Faith', 'role' => 'Researcher'],
      ],
  */
  ```

- **`where`** và **`whereStrict`**: Thêm điều kiện lọc vào truy vấn. 
  ```php
  // hỗ trợ là: '===', '!==', '!=', '==', '=', '<>', '>', '<', '>=' và '<=':
  $collection = collect([
      ['name' => 'Jim', 'deleted_at' => '2019-01-01 00:00:00'],
      ['name' => 'Sally', 'deleted_at' => '2019-01-02 00:00:00'],
      ['name' => 'Sue', 'deleted_at' => null],
  ]);
  
  $filtered = $collection->where('deleted_at', '!=', null);
  
  $filtered->all();
  ```
- **`whereBetween`**: Lọc các bản ghi trong khoảng giá trị xác định.
  ```php
  $collection = collect([
      ['product' => 'Desk', 'price' => 200],
      ['product' => 'Chair', 'price' => 80],
      ['product' => 'Bookcase', 'price' => 150],
      ['product' => 'Pencil', 'price' => 30],
      ['product' => 'Door', 'price' => 100],
  ]);
  
  $filtered = $collection->whereBetween('price', [100, 200]);
  
  $filtered->all();
  
  /*
      [
          ['product' => 'Desk', 'price' => 200],
          ['product' => 'Bookcase', 'price' => 150],
          ['product' => 'Door', 'price' => 100],
      ]
  */
  ```
- **`whereIn`** và  **`whereInStrict`**: Lọc các bản ghi có giá trị thuộc một tập hợp xác định.

  ```php
  $collection = collect([
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Chair', 'price' => 100],
    ['product' => 'Bookcase', 'price' => 150],
    ['product' => 'Door', 'price' => 100],
  ]);

  $filtered = $collection->whereIn('price', [150, 200]);

  $filtered->all();

  /*
    [
        ['product' => 'Desk', 'price' => 200],
        ['product' => 'Bookcase', 'price' => 150],
    ]
  */
  ```
- **`whereNotBetween`**: Lọc các bản ghi không nằm trong khoảng giá trị xác định.
  
  ```php
  $collection = collect([
      ['product' => 'Desk', 'price' => 200],
      ['product' => 'Chair', 'price' => 80],
      ['product' => 'Bookcase', 'price' => 150],
      ['product' => 'Pencil', 'price' => 30],
      ['product' => 'Door', 'price' => 100],
  ]);
  
  $filtered = $collection->whereNotBetween('price', [100, 200]);
  
  $filtered->all();
  
  /*
      [
          ['product' => 'Chair', 'price' => 80],
          ['product' => 'Pencil', 'price' => 30],
      ]
  */
  ```

- **`whereNotIn`** và **`whereNotInStrict`**: Lọc các bản ghi có giá trị không thuộc một tập hợp xác định.
  ```php
    $collection = collect([
      ['product' => 'Desk', 'price' => 200],
      ['product' => 'Chair', 'price' => 100],
      ['product' => 'Bookcase', 'price' => 150],
      ['product' => 'Door', 'price' => 100],
    ]);

    $filtered = $collection->whereNotIn('price', [150, 200]);

    $filtered->all();

    /*
      [
          ['product' => 'Chair', 'price' => 100],
          ['product' => 'Door', 'price' => 100],
      ]
    */
  ```
- **`whereNull`**: Lọc các bản ghi có giá trị NULL ở một cột cụ thể.
  ```php
  $collection = collect([
      ['name' => 'Desk'],
      ['name' => null],
      ['name' => 'Bookcase'],
  ]);
  
  $filtered = $collection->whereNull('name');
  
  $filtered->all();
  
  /*
      [
          ['name' => null],
      ]
  */
  ```
- **`whereNotNull`**: Lọc các bản ghi có giá trị không NULL ở một cột cụ thể.

  
  ```php
    $collection = collect([
      ['name' => 'Desk'],
      ['name' => null],
      ['name' => 'Bookcase'],
    ]);

    $filtered = $collection->whereNotNull('name');

    $filtered->all();

    /*
      [
          ['name' => 'Desk'],
          ['name' => 'Bookcase'],
      ]
    */
  ```

- **`whereInstanceOf`**: Lọc các bản ghi thuộc một kiểu đối tượng cụ thể.
  ```php
  use App\Models\User;
  use App\Models\Post;

  $collection = collect([
    new User,
    new User,
    new Post,
  ]);

  $filtered = $collection->whereInstanceOf(User::class);

  $filtered->all();// [App\Models\User, App\Models\User]
  ```
### Kết Nối và Hợp Nhất:

- **`join`**: Thực hiện phép kết nối (JOIN) giữa các bảng trong truy vấn.

  ```php
  collect(['a', 'b', 'c'])->join(', '); // 'a, b, c'

  collect(['a', 'b', 'c'])->join(', ', ', and '); // 'a, b, and c'

  collect(['a', 'b'])->join(', ', ' and '); // 'a and b'

  collect(['a'])->join(', ', ' and '); // 'a'

  collect([])->join(', ', ' and '); // ''
  ```
- **`crossJoin`**: Thực hiện phép kết nối chéo giữa các bảng.

  ```php
  $collection = collect([1, 2]);
  $matrix = $collection->crossJoin(['a', 'b'], ['I', 'II']);

  $matrix->all();

  /*
    [
        [1, 'a', 'I'],
        [1, 'a', 'II'],
        [1, 'b', 'I'],
        [1, 'b', 'II'],
        [2, 'a', 'I'],
        [2, 'a', 'II'],
        [2, 'b', 'I'],
        [2, 'b', 'II'],
    ]
  */
  ```
- **`zip`**: Kết hợp các kết quả truy vấn lại với nhau.

  ```php
  $collection = collect(['Chair', 'Desk']);
  
  $zipped = $collection->zip([100, 200]);
  
  $zipped->all();// [['Chair', 100], ['Desk', 200]]
  ```

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

- **`value`**: Trả về giá trị của collection hoặc truy vấn sau khi đã xử lý.
- **`unless`**: Thực hiện hành động nếu điều kiện không thỏa mãn.

  ```php
    $collection = collect([1, 2, 3]);
 
    $collection->unless(true, function (Collection $collection) {
        return $collection->push(4);
    });
    
    $collection->unless(false, function (Collection $collection) {
        return $collection->push(5);
    });
    
    $collection->all();// [1, 2, 3, 5]
  ```
- **`when`**: Thực hiện hành động dựa trên điều kiện. Ngược lại của `unless`
  ```php
  $collection = collect([1, 2, 3]);
 
  $collection->when(true, function (Collection $collection, int $value) {
      return $collection->push(4);
  });
  
  $collection->when(false, function (Collection $collection, int $value) {
      return $collection->push(5);
  });
  
  $collection->all();// [1, 2, 3, 4]
  ```
- **`whenEmpty`**: Thực hiện hành động nếu collection rỗng.
  
  ```php
  $collection = collect(['Michael', 'Tom']);
 
  $collection->whenEmpty(function (Collection $collection) {
      return $collection->push('Adam');
  });
  
  $collection->all();// ['Michael', 'Tom']
  
  
  $collection = collect();
  
  $collection->whenEmpty(function (Collection $collection) {
      return $collection->push('Adam');
  });
  
  $collection->all();// ['Adam']
  ```
- **`whenNotEmpty`**: Thực hiện hành động nếu collection không rỗng. Ngược lại của `whenEmpty`

  ```php
  $collection = collect(['michael', 'tom']);
 
  $collection->whenNotEmpty(function (Collection $collection) {
      return $collection->push('adam');
  });
  
  $collection->all();// ['michael', 'tom', 'adam']
  
  
  $collection = collect();
  
  $collection->whenNotEmpty(function (Collection $collection) {
      return $collection->push('adam');
  });
  
  $collection->all();// []
  ```
- **`where`**: Thêm điều kiện lọc vào truy vấn hoặc lọc mảng.
- **`whereStrict`**: Thêm điều kiện lọc nghiêm ngặt vào truy vấn hoặc lọc mảng.
- **`whereIn`** và **`whereInStrict`**: Lọc bản ghi trong DB hoặc mảng dựa trên tập hợp.
- **`whereInstanceOf`**: Lọc bản ghi thuộc một kiểu đối tượng cụ thể hoặc kiểm tra trong mảng.
- **`whereNotIn`** và **`whereNotInStrict`**: Lọc bản ghi không thuộc tập hợp với so sánh , `whereNotInStrict` là chế độ nghiêm ngặt.
- **`whereNotNull`**: Lọc bản ghi có giá trị không NULL hoặc kiểm tra trong mảng.
- **`whereNull`**: Lọc bản ghi có giá trị NULL hoặc kiểm tra trong mảng.

---

**Ghi chú:**

- Các hàm như `macro`, `lazy` liên quan đến việc mở rộng hoặc tối ưu hóa collections, nhưng cũng có thể ảnh hưởng đến cách thức các truy vấn được xây dựng hoặc thực thi.
