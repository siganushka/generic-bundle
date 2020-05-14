# Model

通用的 `sort` 字段，抽象于常用的排序、显示顺序等场景。

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\SortableInterface;
use Siganushka\GenericBundle\Model\SortableTrait;

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
