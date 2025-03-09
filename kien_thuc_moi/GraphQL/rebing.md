# Cài đặt gói

```sh
composer require rebing/graphql-laravel
```

# public gói cấu hình

```sh
php artisan vendor:publish --provider="Rebing\GraphQL\GraphQLServiceProvider"
```

# Tạo Type cho GraphQL

```php
// tạo ở App\GraphQL\Type
<?php

namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Users',
        'description' => 'A user',
        'model' => \App\Models\Users::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the user',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the user',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The email of the user',
            ]
        ];
    }
}
```

# Tạo Query cho GraphQL

```php
<?php
// tạo ở App\GraphQL\Query
namespace App\GraphQL\Query;

use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Models\Users;

class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'users',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Users'));
    }

    public function resolve($root, $args)
    {
        return Users::all();
    }
}
```

# Cấu hình Schema

Trong file `config/graphql.php`, đăng ký `UserType` và `UsersQuery`

```php
<?php

return [
    'schemas' => [
        'default' => [
            'query' => [
                'users' => \App\GraphQL\Query\UsersQuery::class,
            ],
            'types' => [
                'Users' => \App\GraphQL\Type\UserType::class,
            ],
        ],
    ],
];

```

# kiểm thử tại

Đường dẫn nằm ở trong `config/graphql.php`

```php
return [
    'routes' => [
        'graphql' => 'graphql'
    ],
];
```

```uri
http://127.0.0.1:8000/graphql?query={users{id,name,email}}
```
