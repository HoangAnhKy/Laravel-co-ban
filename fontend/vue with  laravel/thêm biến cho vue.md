# khai báo jquery trong vue,
```vue
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset("vue-3.js") }}"></script>
// phải khai báo jquery trước vue
<script>
    const app = Vue.createApp({

    })
    app.config.globalProperties.$jquery = $;
    app.mount("#root");
</script>
```