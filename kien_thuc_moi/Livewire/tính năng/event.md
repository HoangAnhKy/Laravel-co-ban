Dùng để thực thi một logic hay một tính năng nào đó:

```php
// sử dụng `dispatch(name event, props);`
class RegisterUser extends Component{
    // ...
    public function createUser(){
        $validate = $this->validate();

        if (!empty($this->image)){
            $validate["image"] = $this->image->store("upload", "public");
        }
        $user = User::create($validate);
        $this->reset();
        $this->dispatch("load-user", $user);
    }
    // ...
}

// -----------------
use Livewire\Attributes\On;

class ViewUser extends Component
{
    use WithPagination;

    #[On("load-user")] // bên nhận dùng On tên event và nhận dữ liệu qua props
    public function loadUsers($user = null)
    {
        dd($user);
    }
    public function render()
    {
        return view('livewire.view-user', [
            "users" => User::query()->latest()->paginate(2)
        ]);
    }
}
```