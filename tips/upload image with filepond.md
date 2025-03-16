# [docs](https://github.com/pqina/filepond)

# plugin

### [filepond-plugin-image-preview](https://github.com/pqina/filepond-plugin-image-preview)

Xem trước hình ảnh

```js
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
<link
    href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
    rel="stylesheet"
/>

<!-- add before </body> -->
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>

<script>
    // Register the plugin
    FilePond.registerPlugin(FilePondPluginImagePreview);

    // ... FilePond initialisation code here
</script>
```

# code demo

- code back-end

  ```php
  <?php

  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Facades\Storage;

  // giao diện
  Route::get('/', function () {
      return view('image');
  });

  // lấy file tạm
  Route::get('/restore/{path?}', function (Request $request) {
      $path = $request->input('path');
      $file = Storage::disk('public')->get($path);
      $fileName = basename($path);
      return response($file)->withHeaders([
          'Content-Disposition' => 'inline; filename=' . $fileName
      ]);

  })->name("restore");

  // thêm file tạm
  Route::post('/tmpupload', function (Request $request) {
      $file = request()->file('file')[0];
      $path = $file->storeAs(('tmp'), $file->getClientOriginalName(), 'public');
      return response()->json(['path' => $path ?? null]);
  })->name('tmpupload');

  // xóa file tạm
  Route::delete('/tmprevert', function (Request $request) {
      $path = $request->getContent();
      if ($path) {

          Storage::disk('public')->delete($path);
          return response()->json(['path' => $path]);
      }
  })->name('tmprevert');

  // lưu file
  Route::post("/save", function(Request $request){
      $paths = $request->input('file', []);
      foreach ($paths as $path) {
          $tmpPath = $path;
          $finalPath = 'uploads/' . basename($path);

          if (Storage::disk('public')->exists($tmpPath)) {
              Storage::disk('public')->move($tmpPath, $finalPath);
          }
      }

      return redirect()->back()->with("clearCache", "ok");
  });
  ```

- code giao diện

  - khởi tạo form
    ```php
    <form method="POST" action="/save" enctype="multipart/form-data">
        @csrf
        <h2>upload file</h2>
        <input type="file" name="file[]" multiple></input>
        <button type="submit">upload</button>
    </form>
    ```
  - script xử lý

    ```js
    const inputElement = document.querySelector('input[type="file"]');
    const csrf = "{{ csrf_token() }}";
    // xóa cache khi lưu thành công
    const clearCache = "{{ session()->pull('clearCache') }}";

    if (clearCache) {
      localStorage.clear();
    }

    // lấy file tạm lưu trong cache
    let savedFiles = JSON.parse(localStorage.getItem("uploadedFiles") || "[]");

    // khởi tạo pond
    const pond = FilePond.create(inputElement, {
      allowMultiple: true, // chấp nhận mọi event

      //acceptedFileTypes: ["image/*", "application/pdf"], // accept file
      //maxFileSize: "5MB", // giới hạn size
      //maxFiles: 3, // giới hạn số lượng

      // tùy chỉnh thông báo
      //labelFileTypeNotAllowed: "Chỉ cho phép file ảnh và PDF",
      //labelMaxFileSizeExceeded: "Kích thước file vượt quá giới hạn 5MB",

      // sửa tiêu đề
      labelIdle:
        'Kéo thả file vào đây hoặc <span class="filepond--label-action">Chọn</span>',

      // lấy file tạm với route restore, phải để type là limbo(đã up)
      files: savedFiles.map((file) => {
        const filePath = file;
        return {
          source: filePath,
          options: { type: "limbo" },
        };
      }),

      server: {
        // lưu file tạm
        process: {
          url: "/tmpupload",
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": csrf,
          },
          onload: (response) => {
            // Server trả về: { "id": "tmp/abc.jpg" }
            let data = JSON.parse(response);
            let fileId = data.path; // "tmp/abc.jpg"

            // Lưu vào savedFiles
            if (fileId !== null) {
              savedFiles.push(fileId);
              localStorage.setItem("uploadedFiles", JSON.stringify(savedFiles));
            }

            // Phải return ID cho FilePond
            return fileId;
          },
        },
        // xóa file tạm
        revert: {
          url: "/tmprevert",
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": csrf,
          },
          onload: (response) => {
            // Server trả về: { "id": "tmp/abc.jpg" }
            let data = JSON.parse(response);
            let fileId = data.path; // "tmp/abc.jpg"
            console.log(fileId);

            // Lưu vào savedFiles
            savedFiles = savedFiles.filter((id) => id !== fileId);
            localStorage.setItem("uploadedFiles", JSON.stringify(savedFiles));

            // Phải return ID cho FilePond
            return fileId;
          },
        },
        restore: {
          url: "/restore?path=",
        },
      },
    });
    ```

- upload code theo dạng trunk file

  ```js
  process: (fieldName, file, metadata, load, error, progress, abort) => {
              const chunkSize = 1024 * 1024; // 1MB mỗi chunk
              const totalChunks = Math.ceil(file.size / chunkSize);
              let currentChunk = 0;

              function uploadChunk() {
                  const start = currentChunk * chunkSize;
                  const end = Math.min(file.size, start + chunkSize);
                  const blob = file.slice(start, end);

                  // Tạo FormData chứa chunk và các thông tin cần thiết
                  const formData = new FormData();
                  formData.append('file', blob);
                  formData.append('chunkNumber', currentChunk + 1);
                  formData.append('totalChunks', totalChunks);
                  formData.append('fileName', file.name);

                  fetch('{{ route("tmpuploadChunk") }}', {
                      method: 'POST',
                      headers: {
                          'X-CSRF-TOKEN': csrf
                      },
                      body: formData
                      })
                  .then(response => response.json())
                  .then(data => {
                  currentChunk++;
                    progress(true, currentChunk, totalChunks);
                    if (currentChunk < totalChunks) {
                        uploadChunk();
                    } else {
                        // Sau khi upload xong tất cả các chunk, file đã được ghép.
                            // data.fileId chứa đường dẫn file hoàn chỉnh (ví dụ: "tmp/filename.png")
                        if (data.fileId) {
                            if (!savedFiles.includes(data.fileId)) {
                                savedFiles.push(data.fileId);
                                localStorage.setItem('uploadedFiles', JSON.stringify(savedFiles));
                            }
                        }
                        load(data.fileId);
                    }
                  })
                  .catch(err => {
                       error('Upload failed: ' + err);
                  });
              }

              uploadChunk();

              return {
                  abort: () => {
                        abort();
                  }
              };
          },
  ```

  ```PHP

    // Endpoint upload theo dạng chunk
    Route::post('/tmpuploadChunk', function (Request $request) {
        $chunkNumber = $request->input('chunkNumber');    // Số thứ tự chunk hiện tại
        $totalChunks = $request->input('totalChunks');      // Tổng số chunk của file
        $fileName = $request->input('fileName');            // Tên file gốc

        // Lưu chunk vào thư mục 'tmp/chunks'
        $chunkFile = $request->file('file');
        $chunkName = $fileName . '.part' . $chunkNumber;
        Storage::disk('public')->putFileAs('tmp/chunks', $chunkFile, $chunkName);

        // Kiểm tra xem tất cả các chunk đã được upload hay chưa
        $allChunksUploaded = true;
        for ($i = 1; $i <= $totalChunks; $i++) {
            if (!Storage::disk('public')->exists('tmp/chunks/' . $fileName . '.part' . $i)) {
                $allChunksUploaded = false;
                break;
            }
        }

        if ($allChunksUploaded) {
            // Ghép các chunk lại thành file hoàn chỉnh
            $finalPath = 'tmp/' . $fileName;
            $finalContent = '';
            for ($i = 1; $i <= $totalChunks; $i++) {
                $chunkPath = storage_path('app/public/tmp/chunks/' . $fileName . '.part' . $i);
                $finalContent .= file_get_contents($chunkPath);
                // Xóa file chunk sau khi ghép
                Storage::disk('public')->delete('tmp/chunks/' . $fileName . '.part' . $i);
            }
            // Lưu file hoàn chỉnh vào thư mục tmp
            Storage::disk('public')->put($finalPath, $finalContent);
            return response()->json(['fileId' => $finalPath]);
        } else {
            // Chưa đủ chunk, trả về fileId null
            return response()->json(['fileId' => null]);
        }
    })->name('tmpuploadChunk');
  ```
