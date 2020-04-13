# gulp-html 

* Валидатор HTML-кода

## Установка

* Устанавливаем gulp-html локально с помощью ключа --save-dev

> $ npm install --save-dev gulp-html

* Подключаем переменную

```js
htmlValidator = require('gulp-html')
```

* создаем задачу и прописываем в нее следующее

```js
gulp.task('html', function() {
    gulp.src('index.html') - берем файл index.html
        .pipe(htmlValidator()) - валидируем код html
        .pipe(gulp.dest('/public')); - выводим в папку public
});
```
