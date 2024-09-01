# 类型验证

### 国内手机号

国内手机号码验证类型约束（仅验手机号证格式，不验证真实性）。

```php
// ./src/Entity/User.php

use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;

class User
{
    /*
     * @PhoneNumber
     */
    private ?string $phoneNumber = null;
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
    private ?string $versionName = null;
}
```
