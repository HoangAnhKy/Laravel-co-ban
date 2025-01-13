# upload image 

Sử dụng `WithFileUploads` để upload image 

```php
<?php

namespace App\Livewire;

use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegisterUser extends Component
{
    use WithFileUploads;

    #[Rule("required|min:3|max:50")]
    public $name;
    #[Rule("required|email|min:3|max:50")]
    public $email;
    #[Rule("required|min:3")]
    public $password;
    #[Rule("nullable")]
    public $image;

    public function createUser(){
        $validate = $this->validate();
        dd($validate);
    }



    public function render()
    {
        return view('livewire.register-user');
    }
}
```

# Lấy lại data cũ

Sử dụng `temporaryUrl()` để lấy lại image cũ 

```html
<div>
    <label for="profile_picture" class="block text-sm font-medium leading-6 text-gray-900">Profile Picture</label>
    <input type="file" id="profile_picture" name="profile_picture" wire:model="image"
            class="mt-1 block w-full rounded border-gray-300 text-gray-900 text-sm">
    @error('image')
        <span class="text-red-500 text-xs">{{ $message }} </span>
    @enderror

    @if(!empty($image))
        <img src="{{$image->temporaryUrl()}}">
    @endif
</div>
```

# Để upload nhiều file

```html
<div>
    <label for="profile_picture" class="block text-sm font-medium leading-6 text-gray-900">Profile Picture</label>
    <input multiple type="file" id="profile_picture" name="profile_picture" wire:model="image"
            class="mt-1 block w-full rounded border-gray-300 text-gray-900 text-sm">
    @error('image')
        <span class="text-red-500 text-xs">{{ $message }} </span>
    @enderror

    @if(!empty($image))
        <img src="{{$image->temporaryUrl()}}">
    @endif
</div>
```
