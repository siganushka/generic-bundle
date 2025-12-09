# 类型验证

### 手机号码验证

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

### 汉字姓名验证

汉字姓名验证类型约束（仅验证姓名格式，不验证真实性）。

```php
// ./src/Entity/User.php

use Siganushka\GenericBundle\Validator\Constraints\CnName;

class User
{
    /*
     * @CnName
     */
    private ?string $realName = null;
}
```

### 语义化版本号验证

语义化版本号验证类型约束，依赖 `composer/semver` 组件。

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
