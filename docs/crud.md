# CRUD 控制器

提供基于 `Doctrine`、`Form` 和 `Serializer` 的两套 `CRUD` `trait`，通过组合即可获得标准路由，无需手写增删改查逻辑。

### REST API

```php
// ./src/Controller/Api/ProductController.php

use App\Entity\Product;
use App\Form\ProductType;
use Siganushka\GenericBundle\Controller\Crud\DeleteItemTrait;
use Siganushka\GenericBundle\Controller\Crud\GetCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\GetItemTrait;
use Siganushka\GenericBundle\Controller\Crud\PostCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\PutItemTrait;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products')]
class ProductController
{
    use GetCollectionTrait;
    use PostCollectionTrait;
    use GetItemTrait;
    use PutItemTrait;
    use DeleteItemTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: Product::class,
            entityForm: ProductType::class,
        );
    }
}
```

| Trait | 路由 | 响应 |
| --- | --- | --- |
| `GetCollectionTrait` | `GET /` | 分页数据或数组，默认使用 `KnpPaginator` 分页 |
| `PostCollectionTrait` | `POST /` | 创建成功 `201`，表单校验失败 `422` |
| `GetItemTrait` | `GET /{id<\d+>}` | 单条数据 |
| `PutItemTrait` | `PUT/PATCH /{id<\d+>}` | 更新成功 `200`，表单校验失败 `422` |
| `DeleteItemTrait` | `DELETE /{id<\d+>}` | 删除成功 `204` |

`PUT` 按全量提交处理，`PATCH` 按部分提交处理，`clearMissing` 为 `false`。

### 配置

`configureCrud()` 通常在控制器构造函数中调用，参数含义：

| 参数 | 默认值 | 说明 |
| --- | --- | --- |
| `entityName` | 必填 | 实体类名 |
| `entityIdentifier` | `id` | 实体查询字段 |
| `entityForm` | `null` | 表单类名，`POST`、`PUT`、`PATCH` 必需 |
| `queryDtoClass` | `null` | 查询参数 `DTO`，从 `query` 反序列化后传给 `createEntityQueryBuilder()` |
| `serializationCollectionGroups` | `{alias}.collection` | 集合序列化分组 |
| `serializationItemGroups` | `{alias}.item` | 单条序列化分组 |
| `serializationCollectionContext` | 分组上下文 | 集合序列化上下文，设置后覆盖分组参数 |
| `serializationItemContext` | 分组上下文 | 单条序列化上下文，设置后覆盖分组参数 |
| `transactionUsed` | `false` | 是否在数据库事务中执行写操作 |
| `paginationUsed` | `true` | 集合是否分页 |

`{alias}` 为实体类名转下划线小写，如 `Product` 为 `product`，`ProductCategory` 为 `product_category`。

### 授权

默认所有操作均放行，按业务覆盖 `isGrantedForOperation()` 即可，返回 `false` 时抛出 `AccessDeniedException`：

```php
protected function isGrantedForOperation(string $operation, object $entity): bool
{
    return match ($operation) {
        self::OPERATION_READ => true,
        self::OPERATION_CREATE, self::OPERATION_UPDATE, self::OPERATION_DELETE => $this->security->isGranted('ROLE_ADMIN'),
        default => false,
    };
}
```

### 事件

写操作会按实体类名分发事件，事件名为 `实体类名.动作`，例如 `App\Entity\Product.created`：

| 事件类 | 事件名后缀 | 触发时机 |
| --- | --- | --- |
| `EntityBeforeCreateEvent` | `before_create` | 创建前，事务之前 |
| `EntityCreatedEvent` | `created` | 创建后 |
| `EntityBeforeUpdateEvent` | `before_update` | 更新前 |
| `EntityUpdatedEvent` | `updated` | 更新后 |
| `EntityBeforeDeleteEvent` | `before_delete` | 删除前 |
| `EntityDeletedEvent` | `deleted` | 删除后 |

```php
use Siganushka\GenericBundle\Event\EntityCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Product::class.'.created')]
class ProductCreatedListener
{
    public function __invoke(EntityCreatedEvent $event): void
    {
        $product = $event->getEntity();
    }
}
```

### Web 页面

```php
// ./src/Controller/Admin/ProductController.php

use Siganushka\GenericBundle\Controller\Crud\Web\DeleteTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\EditTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\NewTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\ShowTrait;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/products')]
class ProductController
{
    use IndexTrait;
    use NewTrait;
    use ShowTrait;
    use EditTrait;
    use DeleteTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: Product::class,
            entityForm: ProductType::class,
        );
    }
}
```

| Trait | 路由 | 模板 |
| --- | --- | --- |
| `IndexTrait` | `GET /` | `product/index.html.twig` |
| `NewTrait` | `GET/POST /new` | `product/form.html.twig` |
| `ShowTrait` | `GET /{id<\d+>}` | `product/show.html.twig` |
| `EditTrait` | `GET/POST /{id<\d+>}/edit` | `product/form.html.twig` |
| `DeleteTrait` | `GET /{id<\d+>}/delete` | 无，处理完成后重定向 |

模板目录取控制器类名转下划线小写并去掉 `controller`，如 `ProductController` 为 `product/`，`ProductCategoryController` 为 `product_category/`。

- `IndexTrait` 默认向模板传入 `pagination`，关闭分页后传入 `items`；
- `ShowTrait` 传入 `entity`；
- `NewTrait`、`EditTrait` 传入 `form`，成功后重定向到 `app_{别名}_index` 路由，其中别名为控制器类名转小写并去掉下划线，如 `ProductController` 为 `product`，`ProductCategoryController` 为 `productcategory`；
- `DeleteTrait` 是带 `CSRF` 校验的 `GET` 请求，令牌 `id` 为 `delete{id}`，参数名为 `_token`。

### 注意事项

- 非自增主键，例如 `UUID`，需在控制器类上覆盖 `id` 约束：`#[Route('/users', requirements: ['id' => Requirement::UUID_V7])]`；
- `REST` 相关 `trait` 依赖 `symfony/serializer` 与 `knplabs/knp-components`，`Web` 相关 `trait` 依赖 `twig/twig`；
- 控制器需要注册为服务并开启自动装配，以注入 `EntityManagerInterface`、`EventDispatcherInterface`、`FormFactoryInterface`。
