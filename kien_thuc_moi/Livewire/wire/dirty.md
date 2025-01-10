# mô tả

sau khi người dùng nhập dữ liệu nhưng chưa nhấn nút gửi form thì nó sẽ hiện thông báo 

# Ví dụ cú pháp

- Với `wire:dirty` thì nó sẽ kiểm tra form nếu thay đổi mà chưa submid nó sẽ hiện.
- Với `wire:target` thì nó chỉ bắt với target đó thay đổi hay không thôi

```html
<form wire:submit="update">
    <input wire:model.blur="title">
 
    <div wire:dirty wire:target="title">Unsaved title...</div> 
 
    <button type="submit">Update</button>
</form>
```

## `.remove` 

nếu form chưa thay đổi chưa click gì vào nó sẽ hiện lên. nó sẽ mất khi thay đổi

```html
<div wire:dirty.remove>The data is in-sync...</div>
```

## `.class`

nếu input thay đổi mà chưa lưu nó sẽ thay đổi 

```html
<input wire:model.blur="title" wire:dirty.class="border-yellow-500">
```