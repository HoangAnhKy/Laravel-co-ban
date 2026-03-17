# upload file 
mục đich đoạn code là xử lý nguời dùng có thể chọn lại file cũ vẫn bắt được sự kiện change
```js
<body>
    <div>
        <label for="fileImport">import file</label>
        <input type="file" id="fileImport"/>
    </div>
</body>
<script>
    document.getElementById("fileImport").addEventListener("change", function (e) {
        const target = e.target
        if (target.files !== null && target.files.length !== 0) {
            console.log(target.files)
            target.value = '';
        }
    })
</script>
```

# Download file mà dùng ajax

mục đích muốn dùng để  check lỗi các kiểu 

```php
public function exportFile(Request $request){
    if ($request->file_path) {
        $path = Storage::disk('storage')->path($request->file_path);
        $mimeType   = File::mimeType($path);
        return response()->download($path,$request->file_path, [
            'Content-Type' => $mimeType,
            'Content-Encoding' => 'Shift-JIS'
        ])->deleteFileAfterSend(false); // download file mà không xóa file
    }

    $result = self::handleExport();

    if (is_array($result) && $result['status'] === false) {
        return self::responseResult(false, [], $result['message']);
    }

    if (empty($result['file_path'])) {
        return self::responseResult(false, [], __('messages.failed_action'));
    }
    return self::responseResult(true, ['file_path'=> $result['file_path'] ], __('messages.success'));
}
```

```js
handleExport() {
    this.isLoading = true;
    axios.get("{{ route('exportFile') }}")
    .then(response => {
        if(!response.data.status){
            this.showAlert('error', response.data.message)
            return;
        }
        window.location.href="{{ route('exportFile') }}" + "?file_path=" + response.data.data.file_path;
    })
    .finally(() => {
        this.isLoading = false;
    });
}
```