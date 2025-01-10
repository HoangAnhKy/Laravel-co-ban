Dùng để người dùng xác nhận lại thông tin

```html
<button
    type="button"
    wire:click="delete"
    wire:confirm="Are you sure you want to delete this post?"
>
    Delete post 
</button>

// lấy thêm dữ liệu dùng prompt

<button
    type="button"
    wire:click="delete"
    wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE"
>
    Delete account 
</button>
```