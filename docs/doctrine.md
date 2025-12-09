# 通用实体模型

### Resource

通用的实体资源 `id` 主键，统一全局主键类型为 `auto_increment`。

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

通用的 `enabled` 字段，适用物状态、是否启用等场景。

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
$foo->setEnabled(bool $enabled);    // 设置状态、是否启用
```

### Sortable

通用的 `sort` 字段，抽象于常用的排序、优先级等场景。

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
$foo->getSort(): ?int;      // 返回排序值
$foo->setSort(?int $sort);  // 设置排序值
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
$foo->setVersion(?int $version);    // 设置当前版本，由 Doctrine 自动填充，不需要手动设置
```

### Timestampable

通用的 `updatedAt` 和 `createdAt` 时间字段，并在更新、创建时自动填充。

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
$foo->getCreatedAt(): ?\DateTimeImmutable;          // 返回创建时间
$foo->setCreatedAt(\DateTimeImmutable $createdAt);  // 设置创建时间，由系统自动填充
$foo->getUpdatedAt(): ?\DateTimeImmutable;          // 返回更新时间，为 null 时表明从未被修改
$foo->setUpdatedAt(?\DateTimeImmutable $updatedAt); // 设置更新时间，由系统自动填充
```

### CreatableInterface

通用的 `createdAt` 时间字段，如果实体永远不需要修改（不需要 `updatedAt` 字段），使用此接口代替 `TimestampableInterface`。

```php
// ./src/Entity/Foo.php

use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\CreatableTrait;

class Foo implements CreatableInterface
{
    use CreatableTrait;

    // ...
}

$foo = new Foo();
$foo->getCreatedAt(): ?\DateTimeImmutable;          // 返回创建时间，该字段在创建后不可修改
$foo->setCreatedAt(\DateTimeImmutable $createdAt);  // 设置创建时间，由系统自动填充
```

### DeletableInterface

通用的 `deletedAt` 逻辑删除字段，删除后 `ORM` 查询结果将自动过滤已删除数据。

```php
// ./src/Entity/Foo.php

use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\Contracts\Doctrine\DeletableTrait;

class Foo implements DeletableInterface
{
    use DeletableTrait;

    // ...
}

$foo = new Foo();
$foo->getDeletedAt(): ?\DateTimeImmutable;          // 返回删除时间，为 null 时表明未被删除
$foo->setDeletedAt(?\DateTimeImmutable $deletedAt); // 设置删除时间，使用 EntityManager::remove 删除数据时自动填充
```

### Nestable

通用的嵌套结构 `MappedSuperclass`，用于实现任何树形结构，比如无限级分类、菜单等。

```php
// ./src/Entity/Category.php

use Doctrine\Common\Collections\Collection;
use Siganushka\GenericBundle\Entity\Nestable;

class Category extends Nestable
{
    // ...
}

$category = new Category();
$category->getParent(): ?self;          // 获取父节点
$category->getChildren(): Collection;   // 获取子节点
$category->getAncestors(): array;       // 获取所有祖先节点
$category->getSiblings(): array;        // 获取所有同级节点（兄弟节点）
$category->getDescendants(): array;     // 获取所有后代节点
$category->getDepth(): int;             // 获取节点深度
$category->isRoot(): bool;              // 是否为根节点
$category->isLeaf(): bool;              // 是否为叶子节点
```
