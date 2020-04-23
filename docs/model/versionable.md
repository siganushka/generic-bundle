# Versionable

通用的 `version` 字段，由 `Doctrine` 自动更新，典型的乐观锁版本字段。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\VersionableInterface;
use Siganushka\GenericBundle\Model\VersionableTrait;

class Foo implements VersionableInterface
{
    use VersionableTrait;

    // ...
}

$foo = new Foo();
$foo->getVersion(): ?int;           // 获取当前版本
$foo->setVersion(?int $version);    // 设置当前版本，由 Doctrine 自动设置，不需要手机设置该值
```
