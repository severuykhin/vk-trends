# gulp-plumber

* Отслеживает ошибки в коде и не прерывает gulp.watch

## Установка

> $ npm install --save-dev gulp-plumber

* Устанавливаем gulp-plumber локально с помощью ключа --save-dev

* Подключаем переменную

```js
var plumber = require('gulp-plumber')
```

* Далее льем в поток в то место где нужна проверка кода

```js
.pipe(plumber())
```

И когда Plumber видит ошибку он выводит в консоль примерно следующее:

> Plumber found unhandled error: Error in plugin 'gulp-uglify'
> Unexpected token : punc ({)


 