`wire:loading` cung cấp cho người dùng phản hồi trực quan khi có yêu cầu được gửi đến máy chủ, để họ biết rằng họ đang chờ một quy trình hoàn tất.

```html
<form wire:submit="save">
    <button type="submit">Save</button>
 
    <div wire:loading> 
        Saving post...
    </div>
</form>
```

# Các tác phụ trợ

## `.remove`

- Mô tả: hiển thị một phần tử theo mặc định và ẩn nó trong khi yêu cầu tới máy chủ. Ngược lại với mặc định.

- cú pháp

    ```html
    <div wire:loading.remove>...</div>
    ```

## `.class`

- Mô tả: dùng để thay đổi `class` khi đang thực thi gì đó thì class này sẽ được áp dụng vô

- cú pháp: 

    ```html
    <button wire:loading.class="opacity-50">Save</button>
    <!-- thực thi khi form save -->
    ```
- Để sử dụng ngược lại ta dùng `wire:loading.class.remove` sẽ cho tác dụng ngược lại

## `.attr`

- Mô tả: được sử dụng để thay đổi các thuộc tính (attributes) của một phần tử HTML khi một yêu cầu (request) Livewire đang được xử lý (loading).

- Cú pháp
    
    ```html
    wire:loading.attr="attributeName=value"
    ```

## Nhắm vào một mục tiêu cụ thể `wire:targe`

- Ví Dụ: Dùng để load khi nhấn vào button remove

    ```html
    <form wire:submit="save">
        <!-- ... -->
    
        <button type="submit">Save</button>
    
        <button type="button" wire:click="remove">Remove</button>
    
        <div wire:loading wire:target="remove">  
            Removing post...
        </div>
    </form>
    ```

- hoặc có thể thêm `remove(id)` để khỏi bị nhầm lẫn.
- hoặc `wire:target.except="download"` trừ một số hành động nào đó
- nếu muốn nhiều hành động chỉ cần `", "` là được `wire:target="save, remove"`

## `.delay`

- Mô tả: Dùng để trì hoãn bao lâu

- Cú pháp:

    ```html
    <div wire:loading.delay.shortest>...</div> <!-- 50ms -->
    <div wire:loading.delay.shorter>...</div>  <!-- 100ms -->
    <div wire:loading.delay.short>...</div>    <!-- 150ms -->
    <div wire:loading.delay>...</div>          <!-- 200ms -->
    <div wire:loading.delay.long>...</div>     <!-- 300ms -->
    <div wire:loading.delay.longer>...</div>   <!-- 500ms -->
    <div wire:loading.delay.longest>...</div>  <!-- 1000ms -->
    ```