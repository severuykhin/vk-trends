# gulp-concat

* Склеивает файлы одного типа в один

## Установка

> $ npm install --save-dev gulp-concat

* Устанавливаем gulp-concat локально с помощью ключа --save-dev

 * Подключаем переменную:

```js
var concat = require('gulp-concat')
```

* создаем задачу и прописываем в нее следующее

```js
    gulp.src(url) – путь к вашим css или js файлам файлам
        .pipe(concat('styles.css')) – в какой файл собирать
        .pipe(gulp.dest(url)); – путь куда собирать ваши файлы
```