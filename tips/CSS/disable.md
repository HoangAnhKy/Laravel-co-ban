# disable toàn bộ một thẻ tr

```html
<table>
  <tr class="disabled-row">
    <td><input type="text" value="ABC"></td>
    <td><select><option>A</option><option>B</option></select></td>
    <td><button>Gửi</button></td>
  </tr>
</table>
```

```css
.disabled-row input,
.disabled-row select,
.disabled-row button {
  pointer-events: none; /* vô hiệu hóa */
  opacity: 0.5;
  cursor: not-allowed;
}
```