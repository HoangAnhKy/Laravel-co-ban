# Khái niệm
- là class khá mạnh để chạy command

# Các hàm cơ bản

## Tạo Process bằng `__construct`

    ```php
    $process = new Process(['python3', base_path('scripts/demo.py'), 'param1']);
    // phụ thuộc vào file python
    /*
    import sys
    print("Hello from Python!")
    print("Args:", sys.argv[1:]) # lấy từ đâuf tới cuối
    */
    ```

## Chạy
- `run()` → chạy, chờ xong mới trả kết quả.

    ```php
    use Symfony\Component\Process\Process;

    Route::get('/demo-run', function () {
        $process = new Process(['python3', base_path('scripts/demo.py'), 'hello']);

        $process->run(); // chạy, chờ Python kết thúc

        if (!$process->isSuccessful()) {
            // phải tự kiểm tra
            return response()->json([
                'success' => false,
                'error'   => $process->getErrorOutput(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'output'  => $process->getOutput(),
        ]);
    });
    ```
- `mustRun()` → chạy, nếu lỗi thì ném exception (gọn hơn so với tự check).

    ```php
    use Symfony\Component\Process\Process;
    use Symfony\Component\Process\Exception\ProcessFailedException;

    Route::get('/demo-mustrun', function () {
        $process = new Process(['python3', base_path('scripts/demo.py'), 'hello']);

        try {
            $process->mustRun(); // nếu lỗi sẽ throw ProcessFailedException
            return response()->json([
                'success' => true,
                'output'  => $process->getOutput(),
            ]);
        } catch (ProcessFailedException $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'stderr'  => $process->getErrorOutput(),
            ], 500);
        }
    });
    ```
## kiểm tra trạng thái và lấy kết quả

- `isSuccessful()` → true nếu exit code = 0.
  
- `isRunning()` → true nếu process đang chạy.
  
- `getOutput()` → stdout (thứ Python in ra bằng print).
  
- `getErrorOutput()` → stderr (log lỗi, exception Python).

## cấu hình

- `setTimeout($seconds)` → giới hạn thời gian chạy (tránh treo).
    ```php
    $process->setTimeout(30); // tối đa 30s
    $process->setIdleTimeout(10); // dừng nếu 10s không có output
    ```
- `setInput($data)` → gửi data vào stdin cho Python (khi không muốn truyền argv).
  ```php
  $process = new Process(['python3', base_path('scripts/demo.py')]);
  $process->setInput(json_encode(['a' => 1, 'b' => 2]));
  ```
- `setEnv([$key=>$value])` → thiết lập biến môi trường.
  ```php
  $process->setEnv(['PYTHONPATH' => '/home/ubuntu/.local/lib/python3.10/site-packages']);
  ```

# Demo

```php
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

Route::get('/demo-process', function () {
    // Ví dụ 1: Truyền tham số qua argv
    $process = new Process([
        'python3',
        base_path('scripts/demo.py'),
        'param1',
        'param2'
    ]);
    $process->setTimeout(10);

    try {
        $process->mustRun(); // throw exception nếu lỗi
        $output1 = $process->getOutput();
    } catch (ProcessFailedException $e) {
        $output1 = "Error: " . $process->getErrorOutput();
    }

    // Ví dụ 2: Gửi JSON qua stdin
    $process2 = new Process(['python3', base_path('scripts/demo.py')]);
    $process2->setInput(json_encode(['foo' => 'bar', 'num' => 123]));

    try {
        $process2->mustRun();
        $output2 = $process2->getOutput();
    } catch (ProcessFailedException $e) {
        $output2 = "Error: " . $process2->getErrorOutput();
    }

    return response()->json([
        'argv_example' => $output1,
        'stdin_example' => $output2,
    ]);
});
```
