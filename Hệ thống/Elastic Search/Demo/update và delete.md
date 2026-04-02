# update 
`id` là dữ liệu trong elastic search
```php
 public function update(Request $request, $id)
{
    $params = [
        'index'   => 'my_products',
        'id'      => $id,
        'routing' => 'chuột', // BẮT BUỘC nếu lúc tạo có dùng routing
        'body'    => [
            'doc' => [ // Chỉ cập nhật những trường này
                'price' => 600000,
                'status' => 'active'
            ]
        ]
    ];

    $response = $this->client->update($params);
    return response()->json($response->asArray());
}
```

# delete

```php
public function destroy(Request $request, $id)
{
    $params = [
        'index'   => 'my_products',
        'id'      => $id,
        'routing' => $request->input('routing', 'Laptop'), // <--- Phải truyền "Laptop" vào đây
    ];

    try {
        $response = $this->client->delete($params);
        return response()->json($response->asArray());
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Không tìm thấy để xóa! Kiểm tra lại ID hoặc Routing xem đúng "Laptop" chưa!'
        ], 404);
    }
}
```

# get all

```php
public function getAll(Request $request)
{
    $params = [
        'index' => 'my_products',
        'body'  => [
            'query' => [
                'match_all' => (object)[] // Phải để (object)[] để ra dấu {} trong JSON
            ]
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```