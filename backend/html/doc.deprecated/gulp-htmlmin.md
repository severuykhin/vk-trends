# gulp-htmlmin

* Минификация htmlmin кода


## Установка

* Устанавливаем gulp-htmlmin локально с помощью ключа --save-dev

> $ npm install gulp-htmlmin --save-dev

* Запускаем gulp-htmlmin подключением переменной:

```js
var htmlmin = require('gulp-htmlmin')
```

* Далее прописываем в задачу gulp.task следующее:

```js
    gulp.src('index.html') - указываем путь к фалу/ам с разметкой/ми
        .pipe(htmlmin()) - минификация html кода
        .pipe(gulp.dest('app/html')) - вывод в папку html
```

* Ваш html код превратится в однострочный html код
