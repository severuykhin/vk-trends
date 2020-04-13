# gulp-uglify

* Данный плагин служит для минификации js-файлов - он удаляет пробелы, запятые, точки с запятой. В результате js-файл получается меньшим по размеру.

## Установка

* Устанавливаем gulp-uglify локально с помощью ключа --save-dev

> $ npm install --save-dev gulp-uglify

* Подключаем переменную

```js
var uglify = require('gulp-uglify');
```

* создаем задачу и прописываем в нее следующее

```js
gulp.task('compress', function() {
  return gulp.src('lib/*.js') - берет все файлы с расширением .js
    .pipe(uglify()) - минифицирует их
    .pipe(gulp.dest('/js/')); - и выводит в директорию js
});
```