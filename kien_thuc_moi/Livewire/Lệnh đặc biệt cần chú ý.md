# Dùng flash session

- ở component

    ```php
    // ...
    public function createUser(){
            $validate = $this->validate();

            $validate['birthdate'] = '2003-02-'.str_pad(rand(1, 31), 2, '0', STR_PAD_LEFT);
            $validate['password'] = bcrypt($validate['password']);
            $validate['position'] = 1;
            User::create($validate);

            $this->reset(["email"]);
            request()->session()->flash('message', 'save success');
        }
    // ...
    ```

- ở view

    ```html
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @endif
    ```

# Lệnh nhúng Livewire

- Sử dụng để nhúng một Livewire component vào view Blade trong Laravel

- Ví dụ:

    ```php
    <!-- resources/views/home.blade.php -->
    @extends('layouts.app')

    @section('content')
        <h2>My Clicker Component</h2>
        @livewire('clicker') 
        /*
         Hoặc có thể dùng <livewire:clicker />
         Khác chỗ truyền tham số 
            <livewire:clicker title="Custom Title" />
            @livewire('clicker', ['title' => 'Custom Title'])
        */
    @endsection

    ```

- Nguyên lý khi sử dụng `@Liverire` Laravel sẽ: 
    
    - Tìm một Livewire component có tên Clicker.

    - Render nội dung của Livewire component đó.

    - Bao gồm các script và event cần thiết để Livewire hoạt động trên trình duyệt.

# Truyền biến qua class

- Trong ví dụ bên dưới, `$title proprety` sẽ trả về view chứ không phải là `$title ở render`
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Clicker extends Component
{
    public $title = "Livewire"; // cho phép view truy cập vào proprety này

    public function render()
    {
        $title = "Test";
        $user = User::all()->count();
        return view('livewire.clicker', compact('title', 'user'));
    }
}
```

# bindding data với form

- ví dụ với form tạo user

    ```html
    <form action="">
        <input type="text" placeholder="name" wire:model="name_user"/>
        <input type="email" placeholder="email" wire:model="email"/>
        <input type="password" placeholder="password" wire:model="password"/>
        <button wire:click.prevent="createUser">Submit</button>
   </form>
    ```
- Lưu ý trong form
    
    - [wire:model](wire/model.md) tham khảo thêm ở đây
    - [wire:click](wire/click.md) tham khảo thêm ở đây
