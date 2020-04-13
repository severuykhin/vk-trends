# gulp-postcss

C помощью этого обработчика можно запускать различные переменные и обработчики css кода

## Установка

> $ npm install --save-dev gulp-postcss


* Устанавливаем postcss локально с помощью ключа --save-dev

* Подключаем переменную

```js
var postcss = require('gulp-postcss')
```

* Далее пишем в необходимую нам задачу

```js
.pipe(postcss([ autoprefixer({ browsers: ['last 2 versions'] }) ]))
```

* Запускает автопрефиксер для 2 последних версий браузера

 