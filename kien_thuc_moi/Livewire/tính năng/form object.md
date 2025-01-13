Giống việc tạo `modal` rồi dùng

# Lệnh tạo

```sh
php artisan livewire:form nameForm
```

# Sử dụng

- B1: Tạo form

    ```sh
    php artisan livewire:form PostForm
    ```

- B2: Thêm dữ liệu

    ```php
    <?php
 
    namespace App\Livewire\Forms;
    
    use Livewire\Attributes\Validate;
    use Livewire\Form;
    
    class PostForm extends Form
    {
        #[Validate('required|min:5')]
        public $title = '';
    
        #[Validate('required|min:5')]
        public $content = '';
    }
    ```
- B3: khai báo và dùng như proprety cũ nhưng chỉ khác là nó thêm form thôi

    ```php
    <?php
 
    namespace App\Livewire;
    
    use App\Livewire\Forms\PostForm;
    use Livewire\Component;
    use App\Models\Post;
    
    class CreatePost extends Component
    {
        public PostForm $form; 
    
        public function save()
        {
            $this->validate();
    
            Post::create(
                $this->form->all() 
            );
    
            return $this->redirect('/posts');
        }
    
        public function render()
        {
            return view('livewire.create-post');
        }
    }
    ```