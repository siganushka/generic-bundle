# Generic bundle for symfony

一个通用的功能性 `bundle`，提取于工作中常见的可复用代码。

> 尝试性项目，请勿在生产环境中使用。

### 安装

```php
$ composer require siganushka/generic-bundle:dev-master
```

### 注册

```php
// ./src/bundles.php

<?php

return [
    // ...
    Siganushka\GenericBundle\SiganushkaGenericBundle::class => ['all' => true],
];
```

### 功能

- 每张表添加表前缀

```yaml
# ./config/packages/framework.yaml

siganushka_generic:
    table_prefix: tb_ # 可选项，如果不设置或设置为 null 对不添加表前缀
```

- 通用的 `id` 主键字段，统一全局主键类型（`auto_increment` 或 `UUID`）

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\ResourceInterface;
use Siganushka\GenericBundle\Model\ResourceTrait;

class Foo implements ResourceInterface
{
    use ResourceTrait;

    // ...
}

// $foo = new Foo();
// $foo->getId(): ?int;
// $foo->isNew(): bool;
// $foo->isEqualTo(?self $target): bool;
```

- 通用的 `updatedAt` 和 `createdAt` 时间字段，并在更新、创建时自动更新

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\TimestampableInterface;
use Siganushka\GenericBundle\Model\TimestampableTrait;

class Foo implements TimestampableInterface
{
    use TimestampableTrait;

    // ...
}

// $foo = new Foo();
// $foo->getUpdatedAt(): ?\DateTimeInterface;
// $foo->setUpdatedAt(?\DateTimeInterface $updatedAt);
// $foo->getCreatedAt(): ?\DateTimeImmutable;
// $foo->setCreatedAt(?\DateTimeImmutable $createdAt);
```

- 通用的 `enabled` 字段，抽象于常用的比如状态、是否有效等场景

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\EnableInterface;
use Siganushka\GenericBundle\Model\EnableTrait;

class Foo implements EnableInterface
{
    use EnableTrait;

    // ...
}

// $foo = new Foo();
// $foo->isEnabled(): ?bool;
// $foo::setEnabled(?bool $enabled);
```

- 通用的 `sort` 字段，抽象于常用的排序、显示顺序等场景

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\SortableInterface;
use Siganushka\GenericBundle\Model\SortableTrait;

class Foo implements SortableInterface
{
    use SortableTrait;

    // ...
}

// $foo = new Foo();
// $foo->getSort(): ?int;
// $foo->setSort(?int $sort);
// $foo->setSort(Foo::DEFAULT_SORT);
```

- 通用的 `version` 字段，由 `Doctrine ORM` 层自动更新，典型的乐观锁版本字段

```php
// ./src/Entity/Foo.php

use Siganushka\GenericBundle\Model\VersionableInterface;
use Siganushka\GenericBundle\Model\VersionableTrait;

class Foo implements VersionableInterface
{
    use VersionableTrait;

    // ...
}

// $foo = new Foo();
// $foo->getVersion(): ?int;
// $foo->setVersion(?int $version);
```

- 国内手机号验证类型约束

```php
// ./src/Entity/User.php

use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;

class User
{
    /*
    * @PhoneNumber()
    */
    private $phoneNumber;
}
```

- 语义化的版本号类型约束

```php
// ./src/Entity/Client.php

use Siganushka\GenericBundle\Validator\Constraints\Semver;

class Client
{
    /*
    * @Semver()
    */
    private $clientVersion;
}
```

- 用于实体之外的唯一字段类型约束

```php
// ./src/Model/Member.php 

use Siganushka\GenericBundle\Validator\Constraints\UniqueField;

# 检测 Memger::name 的值 在 User::username 实体中是否存在
class Member
{
    /*
    * @UniqueField(entityClass=App\Entity\User, field=username)
    */
    private $name;
}
```

- 通用的 `Registry` 注册器模式

```php
// ./src/Channel/ChannelInterface.php

interface ChannelInterface
{
    public function method1(): string;
    public function method2(): string;
    public function method3(): string;
}
```

```php
// ./src/Channel/FooChannel.php

class FooChannel implements ChannelInterface
{
    // ...
}
```

```php
// ./src/Channel/BarChannel.php

class BarChannel implements ChannelInterface
{
    // ...
}
```

```php
// ./src/Channel/ChannelRegistry.php

use Siganushka\GenericBundle\Registry\AbstractRegistry;

class ChannelRegistry extends AbstractRegistry
{
    public function __construct()
    {
        parent::__construct(ChannelInterface::class);
    }
}
```

```php
$registry = new ChannelRegistry();
$registry->register(new FooChannel());
$registry->register(new BarChannel());

$registry->get(FooChannel::class);  // return instanceof FooChannel
$registry->has(BarChannel::class);  // return true
$registry->values();                // return array of instanceof ChannelInterface
$registry->keys();                  // return ['App\Channel\FooChannel', 'App\Channel\BarChannel']
```

具有别名 `alias` 的注册器模式

```php
// ./src/Channel/FooChannel.php

use Siganushka\GenericBundle\Registry\AliasableServiceInterface

class FooChannel implements ChannelInterface, AliasableServiceInterface
{
    public function getAlias(): string
    {
        return 'foo';
    }

    // ...
}
```

```php
// ./src/Channel/BarChannel.php

use Siganushka\GenericBundle\Registry\AliasableServiceInterface

class BarChannel implements ChannelInterface, AliasableServiceInterface
{
    public function getAlias(): string
    {
        return 'bar';
    }

    // ...
}
```

```php
$registry = new ChannelRegistry();
$registry->register(new FooChannel());
$registry->register(new BarChannel());

$registry->get('foo');  // return instanceof FooChannel
$registry->has('bar');  // return true
$registry->values();    // return array of instanceof ChannelInterface
$registry->keys();      // return ['foo', 'bar']
```
