# csv-helper

### Добавить репозиторий в `composer.json`
```
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/d52b8/csv-helper.git"
    }
]
```

### Подключить библиотеку CsvHelper
```
composer require "d52b8/csv-helper"
```

### Использовать библиотеку CsvHelper
```php
<?php
use d52b8\CsvHelper;

$csv = CsvHelper::read('/path/to/file.csv');
foreach ($csv as $row) {
    print_r($row);
}
```