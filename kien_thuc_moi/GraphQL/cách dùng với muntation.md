# Dùng để tạo (CREATE), cập nhật (UPDATE), hoặc xóa (DELETE) dữ liệu (giống POST, PUT, DELETE trong REST API)

```graphql
type Mutation {
  createUser(name: String!, email: String!, password: String!): User
  updateUser(id: ID, email: String, password: String): User
  deleteUser(id: ID): User
}
```

# Cú pháp

```graphQL
mutation {
  createUser(name: "kdev", email: "kdev@example.com", password: "123456") {
    id
    name
    email
  }
}

```

# Cách hoạt động

- Người dùng gửi `Request` từ `Client` (gửi request từ Postman, GraphQL Playground, Apollo Client hoặc Fetch API.)

- Laravel nhận `request` và chuyển đến `GraphQL` (`Request` đi qua `route` của `GraphQL` trong `config/graphQL.php` ). Laravel xác định `query` hoặc `mutation` nào cần xử lý.

- Laravel Gọi `Query/Mutation` Tương Ứng

- Laravel Trả Kết Quả Về `Client`

- Client hiển thị dữ liệu lên giao diện (React, Vue, Postman, Apollo, Fetch API, v.v.).

# Cách dùng

### Tạo file UserMuntation lưu user

```php
<?php

namespace App\GraphQL\Mutations;

use App\Models\Users;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Rebing\GraphQL\Support\Mutation;

class CreateUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createUser',
        'description' => 'Tạo một user mới'
    ];

    public function type(): Type
    {
        return \GraphQL::type('Users'); // Kiểu trả về (User Type)
    }

    public function args(): array
    {
        return [
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Tên của user'
            ],
            'email' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Email của user'
            ],
            'password' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Mật khẩu của user'
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return Users::query()->create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
        ]);
    }
}

```

### Khai báo với config/graphQL

```php
return [
    'schemas' => [
        'default' => [
            'mutation' => [
                'createUser' => \App\GraphQL\Mutations\CreateUserMutation::class,
            ],
            'query' => [
                'users' => \App\GraphQL\Queries\UsersQuery::class,
            ],
            'types' => [
                'Users' => \App\GraphQL\Types\UsersType::class,
            ],
        ],
    ],
];

```

### Tạo UsersType trả về dữ liệu

```php
<?php

namespace App\GraphQL\Types;

use App\Models\Users;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UsersType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Users',
        'description' => 'Thông tin User',
        'model' => Users::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID của user'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Tên user'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Email user'
            ]
        ];
    }
}
```

### Chạy demo trong postmant

```graphQL
mutation {
  createUser(name: "Hoàng Anh Kỳ", email: "hoangky@example.com", password: "123456") {
    id
    name
    email
  }
}
```
