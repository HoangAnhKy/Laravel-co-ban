# Load sự kiện với one

- `Trường hợp dùng`: Chỉ chạy 1 lần duy nhất, sau đó tự hủy
- `Giải nghĩa:` do là khi dùng `on` ở trong hàm nó sẽ liên tục khai báo lắng nghe sự kiện có thể  dẫn đến duplicate dữ liệu hoặc call api nhiều lần

    | Hàm     | Số lần chạy                                      |
    | ------- | ------------------------------------------------ |
    | `on()`  | Chạy mỗi lần sự kiện xảy ra, dùng `off()` để hủy |
    | `one()` | Chỉ chạy **1 lần duy nhất**, sau đó tự hủy       |


# Load dữ liệu với chuỗi dữ liệu liên tiếp

trường hợp là: khi dùng ajax để  call dữ liệu các option cho select `city -> district -> ward` mỗi khi thay đổi thì mới load lên

- vấn đề  khi sử  dụng: khi nó chạy jquery để thay dữ liệu nó sẽ thay cho `city`, còn `district` đang load các option lên nó không có đợi load xong mà nó change luôn => `district` không load được 

    ```js
    function setData(data){
        // ...
        $('#city').val(data.city).trigger('change');
        $('#district').val(data.district).trigger('change');
        $('#ward').val(data.ward);
    }
    ```

- cách giải quyết: khi nó load xong `thì tạo một event` cho các sự kiện sau lắng nghe và thực thi
  
  - tạo event:
    ```js
    $('#district').on('change', function () {
        let districtCode = $(this).val();
        $('#ward').html('<option value="">Đang load...</option>');
        if (!districtCode) return;
        $.get(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`, function (data) {
            let wards = data.wards;
            wards.sort((a, b) =>a.name.localeCompare(b.name, 'vi', { numeric: true }));
            let html = '<option value="">Chọn phường/xã</option>';
            $.each(wards, function (i, item) {
                html += `<option value="${item.code}">${item.name}</option>`;
            });
            $('#ward').html(html).trigger('loaded'); // load hết lên option chọn mới tạo event
        });
    });
    ```
  - load cái đầu tiên trước để  kích hoạt các hiệu ứng còn lại. sau đó lắng nghe nó có sự kiện đó thực thi
    ```js
    // set city trước
    $('#city').val(data.city ?? "").trigger('change');
    // đợi district load xong
    $('#district').one('loaded', function () {
        $('#district').val(data.district ?? "").trigger('change');
        // đợi ward load xong
        $('#ward').one('loaded', function () {
            $('#ward').val(data.ward ?? "");
        });
    });
    ```
tóm lại flow sẽ là: `set city -> district loaded -> set district -> tương tự cho các hiệu ứng phía sau`