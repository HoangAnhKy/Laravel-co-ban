# cách dùng validate với class component

## giống laravel
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Clicker extends Component
{
    public $name_user;
    public $email;
    public $password;

    public function createUser(){
        $validate = $this->validate([
            'name_user' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $validate['birthdate'] = '2003-02-'.str_pad(rand(1, 31), 2, '0', STR_PAD_LEFT);
        $validate['password'] = bcrypt($validate['password']);
        $validate['position'] = 1;
        
        User::create($validate);
        $this->reset(); // reset lại toàn bộ proprety hoặc (["password"]) resert password
    }


    public function render()
    {
        $title = "Test";
        $users = User::all();
        return view('livewire.clicker', compact('title', 'users'));
    }
}

```

## validate riêng cho proprety

```php
<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Clicker extends Component
{
    #[Rule("required")]
    public $name_user;
    #[Rule("required|email")]
    public $email;
    #[Rule("required|min:6")]
    public $password;

    #[Rule("image.*" => image)] // validate cho tất cả image
    public $image

    public function createUser(){
        $validate = $this->validate();

        $validate['birthdate'] = '2003-02-'.str_pad(rand(1, 31), 2, '0', STR_PAD_LEFT);
        $validate['password'] = bcrypt($validate['password']);
        $validate['position'] = 1;
        dd($validate);
        User::create($validate);
        $this->reset(); // reset lại toàn bộ proprety hoặc (["password"]) resert password
    }


    public function render()
    {
        $title = "Test";
        $users = User::all();
        return view('livewire.clicker', compact('title', 'users'));
    }
}
```

# Validate Riêng cho một  cột 

```php

    public function update(){

        $validate = $this->validateOnly("todo_edit_name");

        $data = Todo::find($this->TODO_ID_EDIT);
        $data->name = $this->todo_edit_name;
        $data->save();
        $this->edit();
    }

```

# Lấy vaildate ở View 

- Giống laravel 

```html
<div>
    <h1 class="text-lg font-semibold">{{ $name_user }}</h1>
    <form action="" class="space-y-4">
        <input 
            class="block w-full rounded border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" 
            type="text" 
            placeholder="Name" 
            wire:model.lazy="name_user"
        />
        @error('name_user')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror

        <input 
            class="block w-full rounded border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" 
            type="email" 
            placeholder="Email" 
            wire:model="email"
        />
        @error('email')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror

        <input 
            class="block w-full rounded border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" 
            type="password" 
            placeholder="Password" 
            wire:model="password"
        />
        @error('password')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror

        <button 
            class="block w-full rounded bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-200" 
            wire:click.prevent="createUser">
            Submit
        </button>
    </form>
</div>
 
```