# lazy load

dùng để load các trang có khối lượng dữ liệu nặng

```html
    @livewire("view-user", ["lazy" => true])
```

# Skeleton 

Dùng để load các thẻ div đồ ra trước nhưng chưa có dữ liệu

[bootstrap 5.2 placeholders](https://getbootstrap.com/docs/5.2/components/placeholders/)

[Tailwind CSS Skeleton - Flowbite](https://flowbite.com/docs/components/skeleton/)


Khởi tạo view cho skeleton và dùng code sau để load

ví dụ 
```php
class ViewUser extends Component
{
    use WithPagination;

    public function placeholder(){ // load skeleton
        return view("livewire.Skeleton.plachoder-view-user");
    }

    #[On("load-user")]
    public function render()
    {
        sleep(5);
        return view('livewire.view-user', [
            "users" => User::query()->latest()->paginate(2)
        ]);
    }
}

```