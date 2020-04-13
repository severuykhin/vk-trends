# gulp-csso

* Минификация css кода

## Установка

> $ npm install gulp-csso --save-dev

* Устанавливаем gulp-csso локально с помощью ключа --save-dev

* Запускаем gulp-csso подключением переменной:

```js
var cssmin = require('gulp-csso')
```

* Далее прописываем в задачу gulp.task следующее:

```js
    gulp.src('css/common.css') - указываем путь к фалу/ам со стилями
      .pipe(cssmin()) - минификация css кода
      .pipe(gulp.dest('app/css')); - вывод в папку css
```

* После сравнения файлов html и css из файла со стилями будут удалены лишние стили
