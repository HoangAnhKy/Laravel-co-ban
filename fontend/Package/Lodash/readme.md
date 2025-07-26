# Cài đặt lodash
- cài bằng npm

    ```sh
    npm install lodash
    ```

- cdn

    ```js
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    ```

# Một số ví dụ dùng Lodash

## 1. _.cloneDeep(obj) → Sao chép sâu object

```js
const obj = { a: 1, b: { c: 2 } };
const newObj = _.cloneDeep(obj);
```

## 2. _.get(obj, path, defaultValue) → Lấy giá trị an toàn theo path
```js
const user = { info: { name: 'Sếp' } };
const name = _.get(user, 'info.name', 'Ẩn danh');  // → 'Sếp'
```

## 3. _.merge(obj1, obj2) → Gộp object sâu
```js
const obj1 = { a: 1, b: { c: 2 } };
const obj2 = { b: { d: 3 } };
const result = _.merge(obj1, obj2);  // → { a:1, b:{ c:2, d:3 } }
```

## 4. _.uniq(array) → Lọc phần tử trùng trong array

```js
const arr = [1, 2, 2, 3];
const unique = _.uniq(arr); // → [1, 2, 3]
```

## 5. _.debounce(func, wait) → Giới hạn tần suất gọi hàm

```js
const save = _.debounce(() => console.log('Lưu...'), 1000);
window.addEventListener('resize', save);
```

## 6. _.map, _.filter, _.reduce → Giống Array nhưng thêm nhiều tính năng

```js
const users = [{ name: 'A' }, { name: 'B' }];
const names = _.map(users, 'name'); // → ['A', 'B']

_.filter(posts, p => p.active && _.get(p, 'user.role') === 'admin');

_.reduce(cartItems, (sum, item) => sum + item.quantity, 0);
```

# Top hữu ích

## _.pickBy + _.identity

Loại bỏ: false, 0, '', null, undefined, NaN
Giữ lại: Giá trị "truthy"

- syntax: 
  
  ```js
  _.pickBy(obj, _.identity);
  _.pickBy(obj, callback); // _.pickBy(obj, (value, key) => typeof value === 'string' && value !== '');

  ```
- ví dụ: Lọc các param không rỗng trước khi gọi API

    ```js
    const rawParams = {
    order_number: '',
    orderer_name: 'Nguyen Van A',
    date_from: null,
    date_to: '',
    page: 1,
    };

    const cleanParams = _.pickBy(rawParams, _.identity);
    // → { orderer_name: 'Nguyen Van A', page: 1 }
    ```

## _.forOwn(obj, callback)

Duyệt qua key-value của object

```js
const sortParams = {
  sort_order_date: 'desc',
  sort_total_price: 'asc',
  sort_order_id: '',
};

_.forOwn(sortParams, (value, key) => {
  if (value && key !== 'sort_order_id') {
    sortParams[key] = ''; // reset các sort khác
  }
});
```

## _.mapValues(obj, callback) Chỉnh sửa tất cả giá trị của object theo điều kiện

```js
const sortParams = {
  sort_order_date: 'desc',
  sort_total_price: 'asc',
  sort_order_id: '',
};

const updatedSort = _.mapValues(sortParams, (val, key) =>
  key !== 'sort_total_price' ? '' : val
);

// → { sort_order_date: '', sort_total_price: 'asc', sort_order_id: '' }
```

## _.omitBy(obj, callback) Xóa các key theo điều kiện

```js
const searchParams = {
  order_number: '123',
  sort_order_date: 'asc',
  sort_total_price: '',
  page: 2,
};

const filtered = _.omitBy(searchParams, (value, key) => key.startsWith('sort_'));
// → { order_number: '123', page: 2 }
```

## _.groupBy(obj, callback), _.orderBy(obj, callback)

- nhóm / sắp xếp dữ liệu
```js
const orders = [
  { id: 1, date: '2024-01-01', price: 100 },
  { id: 2, date: '2024-01-01', price: 200 },
  { id: 3, date: '2024-01-02', price: 150 },
];

const grouped = _.groupBy(orders, 'date');
/*
{
  "2024-01-01": [ {id:1,...}, {id:2,...} ],
  "2024-01-02": [ {id:3,...} ]
}
*/

const orders = [
  { id: 1, price: 200 },
  { id: 2, price: 100 },
  { id: 3, price: 150 },
];

const sorted = _.orderBy(orders, ['price'], ['desc']);
// → [{id:1, price:200}, {id:3, price:150}, {id:2, price:100}]
```

##  _.difference(arr1, arr2)

```js
_.difference([1, 2, 3], [2, 3]);
// → [1]
```

## _.isEmpty(value)

```js
_.isEmpty([]);       // → true
_.isEmpty({});       // → true
_.isEmpty('');       // → true
```
## _.isEqual(a, b)→ So sánh sâu (deep compare)


```js
_.isEqual({ a: 1 }, { a: 1 }); // → true
```

## _.chunk(array, size)

```js
_.chunk([1, 2, 3, 4, 5], 2);
// → [[1,2],[3,4],[5]]
```

## _.startCase, _.camelCase, _.kebabCase

```js
_.startCase('helloWorld');   // → 'Hello World'
_.camelCase('hello world');  // → 'helloWorld'
_.kebabCase('hello world');  // → 'hello-world'
```

### obj

```js
/*
ví dụ
syntax
kết qua
*/

 let o = {};
_.set(o, 'user.info.name', 'Sếp'); 
//  _.set(obj, path, value)
// o = { user: { info: { name: 'Sếp' } } }

_.has({ a: { b: 1 } }, 'a.b'); 
// _.has(obj, path)
// → true

_.defaults(obj, defaultObj)
_.defaults({ a: 1 }, { a: 3, b: 2 }); // → { a: 1, b: 2 }
_.keys(params);
_.values(params);
```
