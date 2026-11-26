# 查询过滤

通过 `QueryFilter` 属性声明式定义查询条件，由 `GenericEntityRepository` 转换为 `Doctrine` 的 `Criteria`，避免为每个筛选项手写 `andWhere()`。

### 定义 DTO

```php
// ./src/Dto/ProductFilter.php

use Doctrine\Common\Collections\Expr\Comparison;
use Siganushka\GenericBundle\Attribute\QueryFilter;

class ProductFilter
{
    public function __construct(
        #[QueryFilter(expr: Comparison::CONTAINS)]
        public readonly ?string $name = null,
        #[QueryFilter(field: 'price', expr: Comparison::GTE)]
        public readonly ?int $minPrice = null,
        #[QueryFilter(field: 'price', expr: Comparison::LTE)]
        public readonly ?int $maxPrice = null,
        #[QueryFilter(expr: Comparison::IN)]
        public readonly ?array $categoryIds = null,
    ) {
    }
}
```

### 参数

| 参数 | 默认值 | 说明 |
| --- | --- | --- |
| `field` | 属性名 | 实体字段名，属性名与字段名不一致时使用 |
| `expr` | `Comparison::EQ` | 比较表达式，支持字符串或子属性数组 |
| `when` | `null` | 回调或可调用数组，返回 `false` 时跳过该条件 |
| `forceOnNull` | `false` | 是否将 `null`、空字符串作为有效条件参与查询 |

支持的表达式：`EQ`、`NEQ`、`LT`、`LTE`、`GT`、`GTE`、`IN`、`NIN`、`CONTAINS`、`MEMBER_OF`、`STARTS_WITH`、`ENDS_WITH`。

未开启 `forceOnNull` 时，值为 `null`、空字符串或空数组的条件会被自动跳过；开启后，`null` 或空字符串会生成 `IS NULL` 条件，可用于筛选“未设置”的数据。

```php
#[QueryFilter(forceOnNull: true)]
public readonly ?string $parent = null;
```

`when` 可用于条件联动，例如仅在某个字段有值时才启用另一个筛选：

```php
#[QueryFilter(when: [self::class, 'isEnabled'])]
public readonly ?string $keyword = null;

public static function isEnabled(self $dto): bool
{
    return null !== $dto->categoryId;
}
```

属性值为对象时，可对子属性分别指定表达式，字段名仍取 `field`（默认属性名）：

```php
#[QueryFilter(expr: ['startAt' => Comparison::GTE, 'endAt' => Comparison::LTE])]
public readonly ?DateRangeDto $createdAt = null;
```

### 在仓库中使用

继承 `GenericEntityRepository` 的仓库会被自动注册为 `doctrine.repository_service`，无需手动配置：

```php
// ./src/Repository/ProductRepository.php

use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;

class ProductRepository extends GenericEntityRepository
{
    public function createQueryBuilderWithFilter(string $alias, ProductFilter $dto): QueryBuilder
    {
        return $this->createQueryBuilderWithOrderBy($alias)
            ->addCriteria(self::createCriteriaFromDto($dto))
        ;
    }
}
```

`GenericEntityRepository` 同时提供：

- `createQueryBuilderWithOrderBy()`：按主键倒序创建 `QueryBuilder`，可传入 `\SortDirection::Ascending` 改为正序；
- `createNew()`：按构造函数参数创建实体实例；
- `createCriteriaFromDto()`：将 `QueryFilter` 定义的 `DTO` 转换为 `Criteria`。

### 与 CRUD 集成

`GetCollectionTrait` 会把 `queryDtoClass` 反序列化后传给 `createEntityQueryBuilder()`，在控制器中覆盖该方法即可复用同一套过滤逻辑：

```php
protected function createEntityQueryBuilder(ProductFilter $dto): QueryBuilder
{
    return $this->entityManager->getRepository(Product::class)->createQueryBuilderWithFilter('entity', $dto);
}
```

### 内置 DTO

- `PageQueryDto`：提供带校验的 `page`、`size` 字段，可作为分页查询 `DTO` 的基类；
- `DateRangeDto`：提供 `startAt`、`endAt` 两个时间字段，配合数组形式的 `expr` 使用。
