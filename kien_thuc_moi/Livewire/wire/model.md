`wire:modal="proprety"`: nó sẽ lưu giá trị ở form vào bên trong class. Nếu muốn `proprety` đó ở dưới view chỉ cần dùng {{ $name_proprety }} là được
    
```html
    <form action="">
        <input type="text" placeholder="name" wire:model="name_user"/>
        <input type="email" placeholder="email" wire:model="email"/>
        <input type="password" placeholder="password" wire:model="password"/>
        <button wire:click.prevent="createUser">Submit</button>
   </form>
```

![alt text](../image/example%20proprety%20in%20component.png)

- wire:modal có thể sử dụng Modifier như `.lazy` hoặc `.defer`.

- tham khảo thêm [Một số Modifier khác](./click.md#một-số-modifier-khác)