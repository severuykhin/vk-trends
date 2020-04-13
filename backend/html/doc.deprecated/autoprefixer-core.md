# autoprefixer-core

* Его задача состоит в том что бы ставить префиксы автоматически

## Установка

> $ npm install --save-dev autoprefixer-core

* Устанавливаем autoprefixer-core локально с помощью ключа --save-dev

* Запускаем autoprifixer-core подключением переменной:

```js
var autoprefixer = require('autoprefixer-core')
```

* Далее прописываем в задачу gulp.task следующее:

```js
.pipe(Путь к файлу .css([ autoprefixer({ browsers: ['last 2 versions'] }) ]))
```

* В конце указывает какое кол-во версий браузеров контролировать
