# 通用实体模型

### Resource

通用的 `id` 主键字段，统一全局主键类型为 `auto_increment`。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Entity\ResourceInterface;
use Siganushka\GenericBundle\Entity\ResourceTrait;

class Foo implements ResourceInterface
{
    use ResourceTrait;

    // ...
}

$foo = new Foo();
$foo->getId(): ?int;                    // 返回主键 ID
$foo->isEqualTo(?self $target): bool;   // 检测是否相等，使用主键比较，未持久化的实体返回 false
```

### Enable

通用的 `enabled` 字段，抽象于常用的比如状态、是否有效等场景。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Entity\EnableInterface;
use Siganushka\GenericBundle\Entity\EnableTrait;

class Foo implements EnableInterface
{
    use EnableTrait;

    // ...
}

$foo = new Foo();
$foo->isEnabled(): ?bool;           // 返回状态、是否有效
$foo->setEnabled(?bool $enabled);   // 设置状态、是否有效
```

### Sortable

通用的 `sort` 字段，抽象于常用的排序、显示顺序等场景。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Entity\SortableInterface;
use Siganushka\GenericBundle\Entity\SortableTrait;

class Foo implements SortableInterface
{
    use SortableTrait;

    // ...
}

$foo = new Foo();
$foo->getSort(): ?int;              // 返回排序值
$foo->setSort(?int $sort);          // 设置排序值
$foo->setSort(Foo::DEFAULT_SORT);   // 设置为默认值
```

### Versionable

通用的 `version` 字段，典型的乐观锁版本字段，由 `Doctrine` 自动维护，

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Entity\VersionableInterface;
use Siganushka\GenericBundle\Entity\VersionableTrait;

class Foo implements VersionableInterface
{
    use VersionableTrait;

    // ...
}

$foo = new Foo();
$foo->getVersion(): ?int;           // 获取当前版本
$foo->setVersion(?int $version);    // 设置当前版本，由 Doctrine 自动维护，不需要手动设置
```

### Timestampable

通用的 `updatedAt` 和 `createdAt` 时间字段，并在更新、创建时自动维护，其中 `createdAt` 字段不可修改。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Entity\TimestampableInterface;
use Siganushka\GenericBundle\Entity\TimestampableTrait;

class Foo implements TimestampableInterface
{
    use TimestampableTrait;

    // ...
}

$foo = new Foo();
$foo->getUpdatedAt(): ?\DateTimeInterface;          // 返回更新时间，为 null 时表明记录从未被修改
$foo->setUpdatedAt(?\DateTimeInterface $updatedAt); // 设置更新时间，由系统自动填充
$foo->getCreatedAt(): ?\DateTimeImmutable;          // 返回创建时间，该字段在创建后不可修改
$foo->setCreatedAt(?\DateTimeImmutable $createdAt); // 设置创建时间，由系统自动填充
```
