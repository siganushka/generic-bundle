# 序列化

依赖 `symfony/serializer`，并提供表单错误、分页数据的序列化器，以及序列化配置导出命令。

### 表单错误

通过 [全局配置](configuration.md) 开启后，已提交且校验失败的表单可直接序列化为 `Problem Details` 结构：

```yaml
siganushka_generic:
    serializer:
        form_error_normalizer: true
```

```php
$json = $serializer->serialize($form, 'json');
```

输出示例：

```json
{
    "type": "https://symfony.com/errors/form",
    "title": "Unprocessable Entity",
    "status": 422,
    "detail": "name: 该值不能为空。",
    "errors": {
        "name": {
            "error": "该值不能为空。"
        },
        "items": {
            "error": null,
            "children": {
                "0": {
                    "error": "该值不能为空。"
                }
            }
        }
    }
}
```

- `detail` 取第一个错误，非根表单会带上字段标签；
- `errors` 为树形结构，记录每个子字段的错误信息；
- 可通过上下文常量 `FormErrorNormalizer::TYPE`、`STATUS`、`ERRORS` 覆盖默认值。

`CRUD` 的 `POST`、`PUT`、`PATCH` 在表单校验失败时即使用该序列化器返回 `422`。

### 分页数据

开启后 `KnpPaginator` 的分页对象会序列化为固定结构：

```yaml
siganushka_generic:
    serializer:
        knp_pagination_normalizer: true
```

```json
{
    "totalCount": 128,
    "currentPage": 1,
    "itemsPerPage": 10,
    "items": []
}
```

键名可通过上下文常量覆盖，例如：

```php
$json = $serializer->serialize($pagination, 'json', [
    KnpPaginationNormalizer::TOTAL_COUNT_KEY => 'total',
    KnpPaginationNormalizer::ITEMS_KEY => 'list',
]);
```

### 导出序列化配置

将实体上的序列化分组、忽略字段等配置导出为 `config/serializer` 下的 `YAML` 文件：

```bash
$ php bin/console siganushka:generic:serializer-dump
$ php bin/console siganushka:generic:serializer-dump 'App\Entity\Product'
```

> 命令执行前会清空 `config/serializer` 目录，请勿在该目录存放非导出生成的文件。
