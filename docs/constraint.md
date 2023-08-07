# 类型验证

### 国内手机号

国内手机号码验证类型约束。

```php
// ./src/Entity/User.php

use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;

class User
{
    /*
    * @PhoneNumber
    */
    private $phoneNumber;
}
```

### 语义化版本号

语义化的版本号类型约束，依赖 `composer/semver` 组件。

```php
// ./src/Entity/Client.php

use Siganushka\GenericBundle\Validator\Constraints\Semver;

class Client
{
    /*
    * @Semver
    */
    private $versionName;
}
```
