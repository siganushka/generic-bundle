# Model

通用的 `id` 主键字段，统一全局主键类型为 `auto_increment`。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\ResourceInterface;
use Siganushka\GenericBundle\Model\ResourceTrait;

class Foo implements ResourceInterface
{
    use ResourceTrait;

    // ...
}

$foo = new Foo();
$foo->getId(): ?int;                    // 返回主键 ID
$foo->isNew(): bool;                    // 检测是否为新实体（没有被持久化）
$foo->isEqualTo(?self $target): bool;   // 检测是否与另一个实体相等
```
