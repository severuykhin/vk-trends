# gulp-csscomb

* Инструмент, который делает CSS-код красивым

## Установка

* Устанавливаем gulp-concat локально с помощью ключа --save-dev


> $ npm install --save-dev gulp-csscomb


* Подключаем переменную:

```js
var csscomb = require('gulp-csscomb')
```

* создаем задачу и прописываем в нее следующее

```js
gulp.task('styles', function() {
  return gulp.src('main.css') - берем файл main.css
    .pipe(csscomb()) - структурируем его
    .pipe(gulp.dest('/css')); - выводим в папку css
});
```
