# Mối quan hệ của Type và Query

- Type là cách định nghĩa cấu trúc dữ liệu trong GraphQL.

  - Tương tự như một Model trong Laravel.
  - Định nghĩa các trường (fields) và kiểu dữ liệu của chúng. (Xác định các trường trả về)
  - Giúp GraphQL biết dữ liệu có những trường nào khi trả về.

- Query là cách lấy dữ liệu từ database thông qua GraphQL.

  - Query luôn gọi một Type để xác định cấu trúc dữ liệu trả về.
  - Tương tự như một Controller trong Laravel.

# Cấu trúc của một Query trong GraphQL

Yêu cầu lấy danh sách users. với các trường là id, name, email

```graphql
{
  users {
    id
    name
    email
  }
}
```

# Truyền tham số

- graphQL

  ```graphQL
  {
  user(id: 1) {
      name
      email
  }
  }

  ```

- Sẽ trương ứng trong fileQuery

  ```php
  public function resolve($root, $args)
  {
      return User::find($args['id']);
  }
  ```

# paginate

- graphQL

  ```graphQL
  {
    users(page: 2) {
      data {
        id
        name
        email
      }
      total
      per_page
      current_page
      last_page
    }
  }
  ```

- Khai báo tại `config/graphQL`
  ```php
  [
    'query' => [
        "users" => \App\GraphQL\Query\UsersQuery::class,
    ],
    'types' => [
      "Users" => \App\GraphQL\Type\UsersType::class,
      "UsersPaginate" => \App\GraphQL\Type\UsersPaginateType::class,
    ],
  ]
  ```
- `UsersType`

  ```php
  <?php

  namespace App\GraphQL\Type;

  use Rebing\GraphQL\Support\Type as GraphQLType;
  use GraphQL\Type\Definition\Type;

  class UsersType extends GraphQLType
  {
      protected $attributes = [
          'name' => 'Users',
          'description' => 'A user',
      ];

      public function fields(): array
      {
          return [
              'id' => [
                  'type' => Type::nonNull(Type::int()),
                  'description' => 'The ID of the user',
              ],
              'name' => [
                  'type' => Type::string(),
                  'description' => 'The name of the user',
              ],
              'email' => [
                  'type' => Type::string(),
                  'description' => 'The email of the user',
              ],
          ];
      }
  }
  ```

- `UsersPaginateType`

  ```php
  <?php

  namespace App\GraphQL\Type;

  use Rebing\GraphQL\Support\Type as GraphQLType;
  use GraphQL\Type\Definition\Type;
  use Rebing\GraphQL\Support\Facades\GraphQL;

  class UsersPaginateType extends GraphQLType
  {
      protected $attributes = [
          'name' => 'UsersPaginate',
          'description' => 'A paginated list of users',
      ];

      public function fields(): array
      {
          return [
              'data' => [
                  'type' => Type::listOf(GraphQL::type('Users')),
                  'description' => 'Danh sách người dùng',
                  'resolve' => function ($root) {
                      return $root->items(); // Sửa lỗi không lấy đúng dữ liệu
                  },
              ],
              'total' => [
                  'type' => Type::int(),
                  'description' => 'Tổng số người dùng',
                  'resolve' => function ($root) {
                      return $root->total();
                  },
              ],
              'per_page' => [
                  'type' => Type::int(),
                  'description' => 'Số user mỗi trang',
                  'resolve' => function ($root) {
                      return $root->perPage();
                  },
              ],
              'current_page' => [
                  'type' => Type::int(),
                  'description' => 'Trang hiện tại',
                  'resolve' => function ($root) {
                      return $root->currentPage();
                  },
              ],
              'last_page' => [
                  'type' => Type::int(),
                  'description' => 'Tổng số trang',
                  'resolve' => function ($root) {
                      return $root->lastPage();
                  },
              ],
          ];
      }

  }

  ```

- `UserQuery`

  ```php
  <?php

  namespace App\GraphQL\Query;

  use Rebing\GraphQL\Support\Query;
  use GraphQL\Type\Definition\Type;
  use Rebing\GraphQL\Support\Facades\GraphQL;
  use App\Models\Users;
  use Illuminate\Support\Facades\Log;

  class UsersQuery extends Query
  {
      protected $attributes = [
          'name' => 'users',
      ];

      public function type(): Type
      {
          return GraphQL::type('UsersPaginate');
      }

      public function args(): array
      {
          return [
              'page' => [
                  'name' => 'page',
                  'type' => Type::int(),
                  'rules' => ['required'],
              ],
          ];
      }

      public function resolve($root, $args)
      {
          $users = Users::paginate(10, ['*'], 'page', $args['page']);

          return $users;
      }
  }
  ```

# Relationship

- graphQL

  ```graphQL
  {
    users {
        id
        name
        posts {
            title
            content
        }
    }
  }
  ```

- Sẽ trương ứng trong fileQuery

  ```php
  public function resolve($root, $args)
  {
    return User::with('posts')->get();
  }
  ```
