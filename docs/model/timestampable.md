# Timestampable

通用的 `updatedAt` 和 `createdAt` 时间字段，并在更新、创建时自动更新。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\TimestampableInterface;
use Siganushka\GenericBundle\Model\TimestampableTrait;

class Foo implements TimestampableInterface
{
    use TimestampableTrait;

    // ...
}

$foo = new Foo();
$foo->getUpdatedAt(): ?\DateTimeInterface;          // 返回更新时间，为 null 时表明记录未被修改过
$foo->setUpdatedAt(?\DateTimeInterface $updatedAt); // 设置更新时间，由系统自动填充
$foo->getCreatedAt(): ?\DateTimeImmutable;          // 返回创建时间，该字段在创建后不可修改
$foo->setCreatedAt(?\DateTimeImmutable $createdAt); // 设置创建时间，由系统自动填充
```
