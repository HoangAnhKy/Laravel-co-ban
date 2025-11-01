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