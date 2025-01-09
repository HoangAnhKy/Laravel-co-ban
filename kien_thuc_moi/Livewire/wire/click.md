`wire:click`: nó sẽ gửi yêu cầu AJAX đến server và thực thi phương thức được chỉ định trong `class component`. Ví dụ `wire:click = "createUser"` nó sẽ gọi tới `method createUser` trong class component.
    
# Một số Modifier khác

## `.prevent`
- Mô tả: Ngăn hành vi mặc định của sự kiện (như submit form hoặc điều hướng link).

- cách dùng:
    ```html
    <a href="#" wire:click.prevent="openLink">Click me</a>
    ```

- Ví dụ: xử lý bên trong mà không reload trang

    ```php
        namespace App\Livewire;

        use Livewire\Component;

        class ExampleComponent extends Component
        {
            public function openLink()
            {
                // Xử lý khi link được click
                session()->flash('message', 'Đã nhấp vào liên kết mà không cần điều hướng!');
            }

            public function render()
            {
                return view('livewire.example-component');
            }
        }

        // View 

        <a href="#" wire:click.prevent="openLink">Click me</a>
        @if (session()->has('message'))
            <p>{{ session('message') }}</p>
        @endif
    ```

## `.stop`

- Mô tả: Ngăn sự kiện click lan truyền lên các phần tử cha. 

- Cách dùng:

    ```html
        <button wire:click.stop="method">Click</button>
    ```

- Ví dụ: nếu mà bỏ `.stop` thì nó sẽ xử lý `parentClick`, ngược lại

    ```php
    namespace App\Livewire;

    use Livewire\Component;

    class ExampleComponent extends Component
    {
        public function parentClick()
        {
            session()->flash('message', 'Parent clicked!');
        }

        public function childClick()
        {
            session()->flash('message', 'Child clicked!');
        }

        public function render()
        {
            return view('livewire.example-component');
        }
    }


    // view

    <div wire:click="parentClick" style="padding: 20px; background:f0f0f0;">
        Parent
        <button wire:click.stop="childClick">Child</button>
    </div>
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @endif
    ```

## `.self`

- Mô tả: Chỉ kích hoạt sự kiện nếu chính phần tử hiện tại được nhấn, không phải phần tử con.

- Cách dùng:

    ```html
    <div wire:click.self="method"><button>Click</button></div>
    ```

- Ví dụ: chỉ chạy khi click vào thẻ div

    ```php
    namespace App\Livewire;

    use Livewire\Component;

    class ExampleComponent extends Component
    {
        public function divClick()
        {
            session()->flash('message', 'Div clicked!');
        }

        public function render()
        {
            return view('livewire.example-component');
        }
    }
    
    // view

     <div>
        div parent
        <div wire:click.self="divClick" style="padding: 20px; background:f0f0f0;">
            Div Content
                <button>Button Inside</button>
            </div>
   </div>
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @endif
    ```

## `debounce`

- Mô tả: Trì hoãn việc thực thi phương thức sau khi nhấn.

- Cách dùng:

    ```html
    <button wire:click.debounce.500ms="method">Click</button>
    ```

- Ví dụ: 

    ```php
    namespace App\Livewire;

    use Livewire\Component;

    class ExampleComponent extends Component
    {
        public function search()
        {
            session()->flash('message', 'Search executed after delay!');
        }

        public function render()
        {
            return view('livewire.example-component');
        }
    }

    // view 

    <button wire:click.debounce.500ms="search">Search</button>
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @endif

    ```


## `.once`

- Mô tả: Chỉ thực thi phương thức một lần duy nhất.

- Cách dùng:

    ```html
    <button wire:click.once="method">Click Once</button>
    ```

- Ví dụ: 

    ```php
    namespace App\Livewire;

    use Livewire\Component;

    class ExampleComponent extends Component
    {
        public function executeOnce()
        {
            session()->flash('message', 'Executed once!');
        }

        public function render()
        {
            return view('livewire.example-component');
        }
    }

    // view 

    <button wire:click.once="executeOnce">Execute Once</button>
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @endif


    ```

## `.lazy`

- Mô tả: Chỉ thực thi phương thức khi người dùng hoàn tất hành động thay đổi trạng thái.

- Cách dùng:

    ```html
    <input wire:model.lazy="name" wire:click="saveName">Save</input>
    ```

- Ví dụ: Chỉ khi người dùng rời khỏi (blur) ô input, giá trị $name mới được cập nhật. Sau đó nhấn "Save" để gọi saveName()

    ```php
    namespace App\Livewire;

    use Livewire\Component;

    class ExampleComponent extends Component
    {
        public $name = '';

        public function saveName()
        {
            session()->flash('message', "Saved name: $this->name");
        }

        public function render()
        {
            return view('livewire.example-component');
        }
    }

    // view

    <input type="text" wire:model.lazy="name">
    <button wire:click="saveName">Save</button>
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @endif


    ```

## `.defer`

- Mô tả: Trì hoãn việc cập nhật property cho đến khi xảy ra sự kiện khác (như submit form).

- Cách dùng:

    ```html
    <button wire:model.defer="method">Click</button>
    ```

- Ví dụ: Dữ liệu chỉ được đồng bộ từ input đến $name khi nhấn nút "Save".

    ```php
    namespace App\Livewire;

    use Livewire\Component;

    class ExampleComponent extends Component
    {
        public $name = '';

        public function saveName()
        {
            session()->flash('message', "Deferred name: $this->name");
        }

        public function render()
        {
            return view('livewire.example-component');
        }
    }

    // view

    <input type="text" wire:model.defer="name">
    <button wire:click="saveName">Save</button>
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @endif


    ```

## `.default`

- Mô tả: Giữ lại hành vi mặc định của sự kiện (cho phép hành vi mặc định xảy ra cùng với sự kiện Livewire).

- Cách dùng:

    ```html
    <a href="/download" wire:click.default="downloadFile">Download</a>
    ```

- Ví dụ: Khi nhấn vào liên kết "Download", hành vi mặc định (tải file) vẫn xảy ra và phương thức downloadFile() cũng được gọi.

    ```php
    namespace App\Livewire;

    use Livewire\Component;

    class ExampleComponent extends Component
    {
        public function downloadFile()
        {
            session()->flash('message', 'File download started!');
        }

        public function render()
        {
            return view('livewire.example-component');
        }
    }

    // view

    <a href="/path-to-file" wire:click.default="downloadFile">Download</a>
    @if (session()->has('message'))
    <p>{{ session('message') }}</p>
    @endif

    ```
