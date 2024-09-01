# 通用实体模型

### Resource

通用的 `id` 主键字段，统一全局主键类型为 `auto_increment`。

```php
// ./src/Entity/Foo.php

use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;

class Foo implements ResourceInterface
{
    use ResourceTrait;

    // ...
}

$foo = new Foo();
$foo->getId(): ?int; // 返回主键 ID
```

### Enable

通用的 `enabled` 字段，抽象于常用的比如状态、是否启用等场景。

```php
// ./src/Entity/Foo.php

use Siganushka\Contracts\Doctrine\EnableInterface;
use Siganushka\Contracts\Doctrine\EnableTrait;

class Foo implements EnableInterface
{
    use EnableTrait;

    // ...
}

$foo = new Foo();
$foo->isEnabled(): bool;            // 返回状态、是否启用
$foo->setEnabled(?bool $enabled);   // 设置状态、是否启用
```

### Sortable

通用的 `sorted` 字段，抽象于常用的排序、优先级等场景。

```php
// ./src/Entity/Foo.php

use Siganushka\Contracts\Doctrine\SortableInterface;
use Siganushka\Contracts\Doctrine\SortableTrait;

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

通用的 `version` 字段，典型的乐观锁版本字段，由 `Doctrine` 自动维护。

```php
// ./src/Entity/Foo.php

use Siganushka\Contracts\Doctrine\VersionableInterface;
use Siganushka\Contracts\Doctrine\VersionableTrait;

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

use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;

class Foo implements TimestampableInterface
{
    use TimestampableTrait;

    // ...
}

$foo = new Foo();
$foo->getUpdatedAt(): ?\DateTimeInterface;          // 返回更新时间，为 null 时表明从未被修改
$foo->setUpdatedAt(?\DateTimeInterface $updatedAt); // 设置更新时间，由系统自动填充
$foo->getCreatedAt(): ?\DateTimeImmutable;          // 返回创建时间，该字段在创建后不可修改
$foo->setCreatedAt(?\DateTimeImmutable $createdAt); // 设置创建时间，由系统自动填充
```
