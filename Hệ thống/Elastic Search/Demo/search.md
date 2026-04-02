- [Lý thuyết](#lý-thuyết)
- [lưu ý](#lưu-ý)
- [search match theo 1 field cụ thể](#search-match-theo-1-field-cụ-thể)
- [search match theo nhiều fields](#search-match-theo-nhiều-fields)
- [search theo routing](#search-theo-routing)
- [search tự động sửa lỗi từ gần đúng `fuzziness`](#search-tự-động-sửa-lỗi-từ-gần-đúng-fuzziness)
- [phân trang với from và size](#phân-trang-với-from-và-size)
- [sort dữ liệu tìm kiếm](#sort-dữ-liệu-tìm-kiếm)
- [highlight keysearch](#highlight-keysearch)
- [Lọc chính xác](#lọc-chính-xác)
- [chỉ  lấy cột `_source`](#chỉ--lấy-cột-_source)
- [Đếm số lượng](#đếm-số-lượng)
- [Boosting ^ | Đánh giá quan trọng](#boosting---đánh-giá-quan-trọng)
- [lenient | fix lỗi tìm khác kiểu dữ liệu](#lenient--fix-lỗi-tìm-khác-kiểu-dữ-liệu)
- [track\_total\_hits | "Đếm không sót một ai"](#track_total_hits--đếm-không-sót-một-ai)

***
# Lý thuyết 

khi search nó sẽ dựa vào `match` để  tìm kiếm dữ liệu phù hợp

# lưu ý

```php
$responseData = $results->asArray();

// 1. Lấy danh sách sản phẩm
$products = $responseData['hits']['hits'];

// 2. Lấy bộ đếm khoảng giá
$priceStats = $responseData['aggregations'];
```


# search match theo 1 field cụ thể

search theo title

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'body' => [
            'query' => [
                'match' => [
                    'title' => $request->q // Tìm kiếm theo tiêu đề
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);

    // Xử lý kết quả trả về
    return response()->json($results->asArray()['hits']['hits']);
}
```

# search match theo nhiều fields 

search theo title và price

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $request->q,       // Từ khóa cần tìm
                    'fields' => ['title', 'price'] // Danh sách các trường muốn tìm
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);

    // Xử lý kết quả trả về
    return response()->json($results->asArray()['hits']['hits']);
}
```

# search theo routing

search sai do nó tìm ở routing laptop, kết quả ra []
```url
http://127.0.0.1:8000/elasticsearch-search?routing=Laptop&q=chuột
```
search đúng do nó tìm ở routing chuột, kết quả ra đúng nếu có title đó

```url
http://127.0.0.1:8000/elasticsearch-search?routing=chuột&q=chuột
```

code demo

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'routing' => $request->input('routing'), // search theo ngăn nào
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $request->q ?? '',       // Từ khóa cần tìm
                    'fields' => ['title'] // Danh sách các trường muốn tìm
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);

    // Xử lý kết quả trả về
    return response()->json($results->asArray()['hits']['hits']);
}
```

# search tự động sửa lỗi từ gần đúng `fuzziness`

- `fuzziness => AUTO`: Elastic sẽ tính toán khoảng cách chỉnh sửa (Levenshtein distance).
  - Nếu từ có 3-5 ký tự: Cho phép sai 1 lỗi.
  - Nếu từ > 5 ký tự: Cho phép sai 2 lỗi.
- `prefix_length`: kí tự đầu phải đúng
- `fuzzy_transpositions`: chấp nhận chữ đảo ngược



```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'routing' => $request->input('routing'),
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $request->q ?? '',       // Từ khóa cần tìm
                    'fields' => ['title'], // Danh sách các trường muốn tìm
                    'fuzziness' => 'AUTO', // tự động sửa lỗi theo từ gần đúng nếu search sát nó
                    'prefix_length'        => 1,             // Ký tự đầu phải đúng
                    'fuzzy_transpositions' => true,          // Chấp nhận gõ đảo chữ
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);

    // Xử lý kết quả trả về
    return response()->json($results->asArray()['hits']['hits']);
}
```

# phân trang với from và size

- `from` số trang, nó đếm từ 0 => trang 1
- `size` là bao nhiêu dòng trên 1 request

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'routing' => $request->input('routing'),
        'from' => $request->input('from',0),  // Trang 1
        'size' => $request->input('size', 1), // Lấy 1 kết quả mỗi trang
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $request->q ?? '',       // Từ khóa cần tìm
                    'fields' => ['title'], // Danh sách các trường muốn tìm

                ]
            ]
        ]
    ];

    $results = $this->client->search($params);

    // Xử lý kết quả trả về
    return response()->json($results->asArray()['hits']['hits']);
}
```

# sort dữ liệu tìm kiếm

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'routing' => $request->input('routing'),
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $request->q ?? '',       // Từ khóa cần tìm
                    'fields' => ['title'], // Danh sách các trường muốn tìm
                    'fuzziness' => 'AUTO',
                ]
            ],
            'sort' => [
                ['price' => 
                    [
                        'order' => 'asc',
                        'missing' => '_last' // Không có giá thì cho xuống cuối danh sách
                    ]
                ], // Giá tăng dần
                ['_score' => ['order' => 'desc']] // Sau đó mới đến độ liên quan
            ]
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```

# highlight keysearch
```php
// url: http://127.0.0.1:8000/search?q=Logitech
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'routing' => $request->input('routing'),
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $request->q ?? '',       // Từ khóa cần tìm
                    'fields' => ['title'], // Danh sách các trường muốn tìm
                    'fuzziness' => 'AUTO',
                ]
            ],
            'highlight' => [ // hight light "Chuột không dây <strong>Logitech</strong>"
                'fields' => [
                    'title' => [
                        'pre_tags' => ['<strong>'],
                        'post_tags' => ['</strong>']
                    ]
                ]
            ],
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```

# Lọc chính xác 

Đây là phần "xương sống" của các bộ lọc tìm kiếm.

- `must`: Tìm kiếm từ khóa (có tính điểm relevance). bắt buộc có
- `filter`: Lọc theo điều kiện cứng như "Cửa hàng còn hàng", "Giá trong khoảng X-Y" (không tính điểm, cực nhanh vì có Cache).
- `should`: Không bắt buộc, nhưng nếu có thì bản ghi đó sẽ được cộng thêm điểm và đẩy lên đầu. (giống lệnh OR trong sql)
- `must_not` ngược lại với `must` nó bắt buộc không có

| Từ khóa  | Ý nghĩa Logic | Bắt buộc?        | Có tính điểm (_score)?        |
| -------- | ------------- | ---------------- | ----------------------------- |
| must     | AND           | CÓ               | CÓ (Càng giống càng tốt)      |
| filter   | AND           | CÓ               | KHÔNG (Chỉ quan tâm Đúng/Sai) |
| should   | OR            | KHÔNG            | CÓ (Có thì cộng thêm điểm)    |
| must_not | NOT           | CÓ (Để loại trừ) | KHÔNG                         |

- sử dụng code html

    | Ký hiệu toán học | Viết tắt Elastic | Ý nghĩa đầy đủ        |
    | ---------------- | ---------------- | --------------------- |
    | ≥                | gte              | Greater Than or Equal |
    | ≤                | lte              | Less Than or Equal    |
    | >                | gt               | Greater Than          |
    | <                | lt               | Less Than             |


- phân biệt trong tìm kiếm
  - `term`: là đi tìm sự "Chính xác tuyệt đối"
  - `match`: là đi tìm sự "Phù hợp" (kiểu áng chừng, đại khái)

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'body' => [
            'query' => [
                'bool' => [
                    'must' => [
                        ['match' => ['title' => 'GAMING']] // Tìm theo từ khóa phải có gaming trong tile
                    ],
                    'filter' => [
                        // ...nhưng chỉ những từ CÓ CHỨA chữ "Chuột" trong title
                        ['match' => ['title' => 'Chuột']],
                        // Kết hợp lọc giá > 2000000
                        ['range' => ['price' => ['lte' => 2000000]]]
                    ],
                    'should' => [
                        ['match' => ['title' => 'gaming']] // Ưu tiên hàng gaming lên trước
                    ],
                    'must_not' => [
                        ['match' => ["title" => "Không dây"]] // bắt buộc là hàng có dây
                    ]
                ]
            ],
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```

# chỉ  lấy cột `_source`

giống như select trong sql

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        '_source' => ['title', 'price'],
        'body' => [
            'query' => [
                'bool' => [
                    'must' => [
                        ['match' => ['title' => 'Logitech']] // Tìm theo từ khóa phải có gaming trong tile
                    ],
                    'filter' => [
                        // ...nhưng chỉ những từ CÓ CHỨA chữ "Chuột" trong title
                        ['match' => ['title' => 'Chuột']],
                        // Kết hợp lọc giá > 2000000
                        ['range' => ['price' => ['gte' => 500000]]]
                    ],
                    'should' => [
                        ['match' => ['title' => 'gaming']] // Ưu tiên hàng Pro
                    ],
                    'must_not' => [
                        ['match' => ["title" => "Không dây"]] // bắt buộc là hàng không dây
                    ]
                ]
            ],
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```

# Đếm số lượng 

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'body' => [
            'query' => [
                'bool' => [
                    'must' => [
                        ['match' => ['title' => 'Logitech']] // Tìm theo từ khóa phải có gaming trong tile
                    ],
                    'filter' => [
                        // ...nhưng chỉ những từ CÓ CHỨA chữ "Chuột" trong title
                        ['match' => ['title' => 'Chuột']],
                        // Kết hợp lọc giá > 2000000
                        ['range' => ['price' => ['gte' => 500000]]]
                    ],
                    'should' => [
                        ['match' => ['title' => 'gaming']] // Ưu tiên hàng Pro
                    ],
                    'must_not' => [
//                            ['match' => ["title" => "Không dây"]] // bắt buộc là hàng không dây
                    ]
                ]
            ],
            'aggs' => [ // đếm số lượng
                'price_ranges' => [
                    'range' => [
                        'field' => 'price',
                        'ranges' => [
                            ['key' => '< 500k','to' => 500000], // set lại key và tìm mấy giá trị < 500k
                            ['from' => 500000, 'to' => 1000000], // 500k - 1tr
                            ['from' => 1000000]               // > 1tr
                        ]
                    ]
                ],
                'title_counts' => [ // tên
                    'filters' => [ // loại filter
                        'filters' => [
                            'so_luong_gaming'    => ['match' => ['title' => 'gaming']],
                            'so_luong_khong_day' => ['match' => ['title' => 'không dây']]
                        ]
                    ]
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray());
}
```

# Boosting ^ | Đánh giá quan trọng

dùng để  tăng điểm chính xác khi tìm kiếm `multi_match`. ở ví dụ dưới khi title phù hợp điểm sẽ cao hơn 5 lần 

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'body' => [
            'query' => [
                'multi_match' => [
                    'query'  => $request->q ?? '',
                    'fields' => [
                        'title^5',   // <--- TIÊU ĐỀ QUAN TRỌNG GẤP 5 LẦN
                        'content^1'  // Nội dung giữ nguyên trọng số cơ bản là 1
                    ]
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```

# lenient | fix lỗi tìm khác kiểu dữ liệu
dùng khi tìm kiếm các trường khác nhau về kiểu dữ liệu int, string, .... Nó sẽ tránh được lỗi 400 

```php
public function search(Request $request)
{
    $params = [
        'index' => 'my_products',
        'body' => [
            'query' => [
                'multi_match' => [
                    'query'  => $request->q ?? '',
                    'fields' => [
                        'title^5',
                        'content^1',
                        'price'
                    ],
                    'lenient' => true // tránh lỗi 400 khi search string trong trường int
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```

# track_total_hits | "Đếm không sót một ai"

```php
'body' => [
    'track_total_hits' => true, // <--- Đếm chính xác tổng số dù có bao nhiêu triệu bản ghi
    'query' => [...]
]
```