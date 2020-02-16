# Generic bundle for symfony

> 尝试性项目，请勿在生产环境中使用。

### step 1

install bundle

```php
$ composer require siganushka/generic-bundle:dev-master
```

### step 2

register bundle for kernel

```php
// ./src/bundles.php

<?php

return [
    // ...
    Siganushka\GenericBundle\SiganushkaGenericBundle::class => ['all' => true],
];
```
