- [Khai báo jquery trong vue,](#khai-báo-jquery-trong-vue)
- [Khai báo và dùng component](#khai-báo-và-dùng-component)

# Khai báo jquery trong vue,
```js
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.10.0/axios.min.js"></script>
<script src="{{ asset("vue-3.js") }}"></script>
// phải khai báo jquery trước vue
<script>
    const app = Vue.createApp({

    })
    app.config.globalProperties.$jquery = $;
    app.config.globalProperties.$lodash = _;
    app.config.globalProperties.$axios = axios;
    app.mount("#root");
</script>
```

# Khai báo và dùng component

- khai báo
  
    ```js
    <script type="text/x-template" id="pagination">
        <!-- HTML template của component ở đây -->
        <div class="text-center">
            <h2>Paginator</h2>
        </div>
    </script>

    <script>
        const PaginationComponent = {
            props: ['pagination'],
            template: '#pagination',
            methods: {}
        };
        // Khai báo component toàn cục
        window.GlobalComponents = window.GlobalComponents || {};
        window.GlobalComponents.PaginationComponent = PaginationComponent;
    </script>
    ```
- sử  dụng
  
    ```js
    app.component('Pagination', window.GlobalComponents.PaginationComponent);

    // trên giao diện html hoặc blade
    <Pagination :pagination="paginate"></Pagination>
    ```