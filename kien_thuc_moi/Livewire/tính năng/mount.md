# gắn thêm giữ liệu


Khi dùng 

```html
@livewire("todo-list", ["q" => "laravel"])
```

Đồng nghĩa với việc gắn dữ liệu `props q` vào function `mount`


```php
public function mount($q){
    $this->search = $q;
}


public function render()
{
    $todos = Todo::query();

    $todos->where("name", "like", "%$this->search%");

    $todos = $todos->latest()->paginate(5);
    return view('livewire.ToDo.todo-list', compact("todos"));
}
```