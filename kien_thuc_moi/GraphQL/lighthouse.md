# Cài đặt

```sh
composer require nuwave/lighthouse
```

# publish cấu hình

```sh
php artisan vendor:publish --tag=lighthouse-config
```

# Cài đặt giao diện

```sh
composer require mll-lab/laravel-graphiql
```

truy cập thêm `/graphiql` để vào giao diện

# Cài đặt hỗ trợ IDE

giúp các IDE như PHPStorm, VSCode có thể tự động nhận diện các directive, query, mutation, và resolver được sử dụng trong schema GraphQL.

phải có `graphql/schema.graphql` trước mới dùng được, nó nằm cùng mục với `app`

```sh
php artisan lighthouse:ide-helper
```

# Khởi tạo `grapghql/schema.graphql`

Schema này xác định các kiểu dữ liệu (types), truy vấn (queries), biến đổi (mutations) và cách mà client có thể tương tác với API.

### Trường hợp muốn chia nhỏ schema với từng bảng

```pgsql
graphql/
├── schema.graphql
├── user.graphql
├── post.graphql
└── comment.graphql
```

- Trong `schema.graphql`, sử dụng `#import` để nhập các file schema khác
- Sử dụng `extend type` để mở rộng các loại `Query` và `Mutation` trong các file riêng lẻ.

  ```graphql
  #import user.graphql
  #import post.graphql
  #import comment.graphql

  extend type Query {
    _empty: String
  }
  ```

# Sử dụng ứng dụng

### Caching GraphQL

- bật caching bằng cách sửa `config/lighthouse.php`:

  ```php
  'cache' => [
      'enable' => env('LIGHTHOUSE_CACHE_ENABLE', true),
  ],
  ```

- chạy lệnh

  ```sh
  php artisan lighthouse:cache
  ```

### Bảo mật với Middleware

```graphql
// guard
type Query {
    me: User @auth
}
// policy
type Mutation {
    updateUser(id: ID!, name: String!): User @update @can("update", "App\\Models\\User")
}
```

### Tạo query logic khó

- lệnh

  ```sh
  `php artisan lighthouse:query UserQuery
  ```

- nó sẽ nằm ở `app/GraphQL/Queries/UserQuery.php`, mở lên và code

  ```php
  namespace App\GraphQL\Queries;

  use App\Models\User;

  class UserQuery
  {
      public function resolve($root, array $args)
      {
          return User::all();
      }
  }
  ```

- quay lại cập nhật schema

  ```graphql
  type Query {
    users: [User!]! @field(resolver: "App\\GraphQL\\Queries\\UserQuery@resolve")
  }
  ```

### validate

- `@rules`: dùng tương tự trong laravel

  ```graphql
  type Mutation {
    updateUser(
      id: ID!
      name: String @rules(apply: ["required"])
      email: String
        @rules(apply: ["required", "email", "unique:users,email,{{args.id}}"])
    ): User @update
  }
  ```

#### Trường hợp validate với logic khó

- **Đặt tên bên schema** `@validator`

  ```graphql
  input UpdateUserInput @validator {
    id: ID
    name: String
  }
  ```

- **Tạo file `validate` với cấu trúc `sử dụng tên của kiểu đầu vào và thêm Validator`**

  ```sh
  php artisan lighthouse:validator UpdateUserInputValidator
  ```

- mở file `validator` vừa tạo lên chỉnh, cũng tương tự bên laravel `attributes` sửa tên, `messages` sửa thông báo trả về

  ```php
  namespace App\GraphQL\Validators;

    use Illuminate\Validation\Rule;
    use Nuwave\Lighthouse\Validation\Validator;

    final class UpdateUserInputValidator extends Validator
    {
        public function rules(): array
        {
            return [
                'id' => [
                    'required'
                ],
                'name' => [
                    'sometimes',
                    Rule::unique('users', 'name')->ignore($this->arg('id'), 'id'),
                ],
            ];
        }
    }
  ```

### Tạo code trong `schema.graphql`

```graphql
type User {
  id: ID!
  name: String!
  email: String!
}

type Query {
  users: [User!]! @all
}

type Mutation {
  createUser(name: String!, email: String!): User @create
}
```
