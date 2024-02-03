# WP Toolkit | 我的 WordPress 外掛
一句話講完 WP Toolkit :

> WP Toolkit 是針對開發人員使用的 WordPress Plugin，理念是 library as plugin ，整合了 Redux Framework 並擴充了許多好用的欄位、debug_log 等方便的功能...

<br><br><br>

[x] 常用的 rest api, 例如之前的 get_post_meta 或 get_nonce <br>
[x] 隱藏一些 REDUX 的預設跟不必要的通知 <br>
[x] 一些常用的好用功能  例如之前的 debug_log <br>

<br><br><br>

## 📦 Install

如果你要將此套件做為你的套件依賴，推薦使用 TGM Plugin 來做套件依賴

<br><br><br>

## 如何自己擴充欄位

<br><br>

#### 1. 複製範例檔案

複製 `\inc\redux_custom_fields\example` 這個檔案，比如說 `\inc\redux_custom_fields\my_field`

<br><br>

#### 2. 將檔案內的 `example` 改成 `my_field` ， `Example` 改成 `My_Field` ， 資料夾名稱、檔名也都要改

因為 REDUX 是會解析檔名的，所以檔名也要跟著改

<br><br>

#### 3. 在 `\inc\index.php` 的 `load_extensions` 載入你的欄位

REDUX 會自動解析並載入 `set_extensions` 的路徑參數，所以不需要自己 include

只要在 `load_extensions` 裡面新增一行就好

```php
	public function load_extensions($redux_object): void
	{
		// 新增下面這行
		\Redux::set_extensions($opt_name, Utils::get_plugin_dir() . '/inc/redux_custom_fields/my_field');
	}
```

#### 4. 使用 TailwindCSS 來開發

如果你的 `WP_DEBUG` 為 `true`，那麼整個 wp-toolkit 會引入 tailwind CDN ，只需要加上前綴 `tw-` 就可以使用 tailwind 的 class

> 如果不使用前綴， tailwind 會與 WordPress 後台的 CSS 衝突

在此套件根目錄使用以下指令來打包 tailwind CSS

`npx tailwindcss -i ./inc/redux_custom_fields/bundle.css -o ./inc/redux_custom_fields/bundle-min.css --minify`

<br><br><br>

## 已經有的欄位

現有欄位可以參考 [REDUX 官網](https://devs.redux.io/core-fields/)

- [number](https://github.com/j7-dev/wp-toolkit/tree/master/inc/redux_custom_fields/number)

