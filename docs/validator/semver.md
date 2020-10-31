# Validator

语义化的版本号类型约束，依赖 `composer/semver` 组件。

```php
// ./src/Entity/Client.php

use Siganushka\GenericBundle\Validator\Constraints\Semver;

class Client
{
    /*
    * @Semver()
    */
    private $clientVersion;
}
```
