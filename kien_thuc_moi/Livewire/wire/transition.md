Dùng để mở hoặc ẩn một nội dung nào đó. Nó sẽ phải kết hợp với @if của blade

```html
<div>
    <!-- ... -->
 
    <button wire:click="$set('showComments', true)">Show comments</button>
 
    @if ($showComments)
        <div wire:transition> 
            @foreach ($post->comments as $comment)
                <!-- ... -->
            @endforeach
        </div>
    @endif
</div>
```

hiệu ứng đầu vô, đầu ra

![alt text](../image/hieu%20ung%20in%20out%20cua%20transition.png)

# một số dùng thêm

![alt text](../image/action%20transition.png)