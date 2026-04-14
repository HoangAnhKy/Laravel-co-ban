# tránh tình trạng duplicate

```js
// button lưu
$("#addIngredient").on("click", function() {
    const productId = $('#product_id').val();

    // Kiểm tra nếu chưa có nguyên liệu nào
    if (ingredients.length === 0) {
        showAlert("Vui lòng thêm ít nhất một nguyên liệu!", "danger");
        return;
    }

    // Gửi AJAX
    $.ajax({
        url: '{{ route("admin.product.save_ingredients") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}', // Token bảo mật của Laravel
        },
        beforeSend: function() {
            // Disable nút để tránh bấm nhiều lần
            $("#addIngredient").prop('disabled', true).text('Đang lưu...');
        },
        success: function(response) {
            showAlert(response.message);
        },
        error: function(xhr) {
            showAlert(response.message);
        },
        complete: function() {
            // bỏ disabled
            $("#addIngredient").prop('disabled', false).text('Lưu');
        }
    });
});
```