
- dùng php artisan make:Request tên để cài đặt

- sau đó chuyển authorize = true để xác thực validate

    vd:
	
    ```php
    //...
    public function authorize()
    {
        return true;
    }
    // ...
    ```


- sử dụng `rules` để tạo validation

    vd: 
    ```php
	public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
               //'unique:App\Models\Course,name',
                Rule::unique(Course::class, 'name'),
            ],
        ];
    }
    ```
- dùng `messages` để trả về đề xuất mong muốn

    vd: 
    ```php
        public function messages()
        {
            return [
                'required' => ':attribute Không được để trống !',
                'string' => ':attribute phải là một chuỗi ký tự!',
                'unique' => ':attribute đã tồn tại',
            ];
        }
    ```

- dùng `attributes` để đặt tên đối tượng mong muốn

    vd: 
    ```php
    public function attributes(){
        return [
          'name' => 'Tên',
        ];
    }
    ```
    
----

# Xử lý nâng cao hơn

- `prepareForValidation()` xử lý trước khi xác thực.
- `passedValidation()` sẽ xử lý sau khi quá trình xác thực thành công.
- `validated()` hàm thực thi validate

- `$this->route(key)`: lấy giá trị từ route 
- `$this->merge([key => value])`: thêm giá trị vào $request để check

ví dụ:
```php

protected function prepareForValidation() 
{
    $this->merge(['course' => $this->route('course')]);
}

public function rules()
{
    return [
        'course' => [
            Rule::exists(Course::class, 'id'),
        ],
    ];
}

// kế thừa và ghi đè nó
public function validated($key = null, $default = null)
{
    $validated = parent::validated();
    unset($validated['course']);
    return $validated;
}
```

# cách dùng validate


- khai báo `fillable` danh sách cách đối tượng cần lấy bên trong model sau khi chạy hàm (CRUD)

    vd:

    ```php
    public $fillable = [
        'name',
    ];
    ```

- Bên `controller` gọi `validated()` khi sử dụng

    vd: 
    ```php
    public function store(StoreCourseRequest $request)
    {
        $this->model::create($request->validated());
        return redirect()->route('course.index');
    }
    ```

# custom validated
- Sử dụng câu lệnh sau để tạo ra Rule theo ý muốn của mình, có thể sử dụng lại nhiều lần

```sh
    php artisan make:rule ValueRequestUser
```

thư mục được tạo sẽ nằm trong thư mục `app/Rules/*`

- để sử dụng thì chỉ cần gọi nó ở trong filed cần check validated

```php 
    $validate = $request->validate([
        'old_password'=>['required', new ValueRequestUser($user)],
        'password'=>'required',
        'repassword'=>'required'
    ]);
```

Lưu ý: muốn nhận data truyền vào thì phải sử dụng thêm `__construct`
- ví dụ
```php
<?php

namespace App\Rules;

use App\Models\Users;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ValueRequestUser implements ValidationRule
{
    private $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail) : void
    {
        if( !Hash::check($value, $this->user->password)){
            $fail('Password not matching');
        }
    }
}

```