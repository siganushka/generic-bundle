# Enable

通用的 `enabled` 字段，抽象于常用的比如状态、是否有效等场景。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\EnableInterface;
use Siganushka\GenericBundle\Model\EnableTrait;

class Foo implements EnableInterface
{
    use EnableTrait;

    // ...
}

$foo = new Foo();
$foo->isEnabled(): ?bool;           // 返回状态、是否有效
$foo->setEnabled(?bool $enabled);   // 设置状态、是否有效
```
