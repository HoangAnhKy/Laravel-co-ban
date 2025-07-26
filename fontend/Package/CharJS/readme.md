# Thư viện vẽ biểu đồ

- Type: có nhiều loại, nhưng chỉ tìm hiểu một số loại bên dưới. Tham khảo thêm [tại đây](https://www.chartjs.org/docs/latest/configuration/title.html)
    - [bar](#cách-dùng-với-bar): biểu đồ  cột dọc
    - [pie/doughnut](#demo):biểu đồ  tròn
    - [line](#line):biểu đồ  đường
  
- **Ngoài ra có thể  kết hợp bar + line**
- thêm sự kiện click 

    ```js
    options: {
        onClick: (evt, elements) => {
            if (elements.length > 0) {
                const index = elements[0].index;
                const courseName = labels[index];
                const value = dataValues[index];
                alert(`Bạn vừa bấm vào: ${courseName} (${value}`);
            }
        },
    }
    ```
## bar

- Khai báo trước `canvas` để  vẽ
```html
 <canvas id="myChart" width="400" height="200"></canvas>
```
- tạo `script` để  vẽ lên 

```js  
<script>
    const courses = @json($courses);  // lấy dữ liệu bên php
    const labels = [];
    const dataValues = [];

    // Lọc dữ liệu
    courses.forEach((course) => {
        labels.push(course.name);
        dataValues.push(course.users_count);
    });

    // xác định data vẽ, hiện tại đang vẽ 2 cột cho một giá trị bên trong `labels`
    const data = {
        labels: labels, // tên cột
        datasets: [
            {
                label: 'Số học viên', // tên của cột trên góc trên
                data: dataValues,
                backgroundColor: 'rgba(54, 162, 235, 0.5)', // màu nên của cột 
                borderColor: 'rgba(54, 162, 235, 1)', // màu viền của cột 
                borderWidth: 1 // chiều rộng của cột 
            },  
            {
                label: 'Số học viên 2',
                data: dataValues.map((value) => (value * 0.5 )),
                backgroundColor: 'rgba(235,54,54,0.5)',
                borderColor: 'rgb(235,54,54)',
                borderWidth: 1
            }]
    };

    // cài đặt thêm thông tin hiển thị
    const config = {
        type: 'bar', // type của bảng
        data: data, // lấy data được cấu hình bên trên để vẽ
        options: {
            responsive: true, // có responsive khi thay đổi màn
            scales: {
                y: {
                    beginAtZero: true // bắt đầu vẽ từ 0
                }
            },
            layout: {
                padding: 50, // khoảng cách của bảng trong canvas
            },
            plugins: {
                subtitle: { // set thêm tile hiện thị cùa bảng
                    display: true,
                    text: 'Custom Chart Subtitle',
                    padding: {
                        top: 10,
                        bottom: 30
                    }

                }
            }
        }
    };

    const ctx = $("#myChart")[0].getContext("2d"); // lấy giá trị 2d của mychar để  vẽ
    new Chart(ctx, config); // Vẽ charjs với config đã cài ở trên vào mychar
</script>
```

# pie/doughnut

```php
@extends("layout-vue")
@section("content")
    <div class="container">
        <h1>Chart.js Example</h1>
        <div style="max-width: 300px; margin: auto;"> <!-- Center and size -->
            <canvas id="myChart"></canvas>
        </div>
    </div>
@endsection

@push("js")
    <script>
        const courses = @json($courses);
        const labels = [];
        const dataValues = [];

        courses.forEach((course) => {
            labels.push(course.name);
            dataValues.push(course.users_count);
        });

        const data = {
            labels: labels,
            datasets: [{
                label: 'Số học viên',
                data: dataValues,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)'
                ],
                borderColor: 'white',
                borderWidth: 1
            }]
        };

        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false, // Quan trọng để chiều cao không bị bóp
                plugins: {
                    subtitle: {
                        display: true,
                        text: 'Tỉ lệ học viên theo khóa học',
                        padding: {
                            top: 10,
                            bottom: 30
                        }
                    }
                }
            }
        };

        const ctx = $("#myChart")[0].getContext("2d");
        new Chart(ctx, config);
    </script>
@endpush
```

# line 

```php
@extends("layout-vue")
@section("content")
    <div class="container">
        <h1>Biểu đồ đường – Chart.js Line Chart</h1>
        <div style="max-width: 600px; margin: auto;">
            <canvas id="myChart" style="height: 300px;"></canvas>
        </div>
    </div>
@endsection

@push("js")
<script>
    const courses = @json($courses);
    const labels = [];
    const dataValues = [];

    courses.forEach((course) => {
        labels.push(course.name);
        dataValues.push(course.users_count);
    });

    const data = {
        labels: labels,
        datasets: [{
            label: 'Số học viên',                           //  Tên hiển thị trong legend
            data: dataValues,                               //  Mảng giá trị Y tương ứng với từng label (X)
            fill: false,                                    //  Không tô màu bên dưới đường line (mặc định true với area chart)
            borderColor: 'rgb(75, 192, 192)',             //  Màu đường line
            tension: 0.1,                                   //  Độ cong của đường (0 là thẳng, càng cao càng cong)
            pointBackgroundColor: 'rgb(75, 192, 192)'     //  Màu của các điểm tròn trên line
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                subtitle: {
                    display: true,
                    text: 'Số học viên theo từng khóa học',
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                }
            }
        }
    };

    const ctx = $("#myChart")[0].getContext("2d");
    new Chart(ctx, config);
</script>
@endpush
```