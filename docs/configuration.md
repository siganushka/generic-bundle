# 全局配置

### Doctrine 表前缀

为 `Doctrine` 实体添加表前缀，依赖 `siganushka/doctrine-contracts` 组件。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        table_prefix: app_ # 默认值为 null
```

### Doctrine 字段重排

按照实体表的主键、外键、普通字段、公共字段（排序、状态、更新、创建时间）的顺序重新排列数据库字段，也可以使用 `siganushka:generic:schema-resort` 命令重新排列已生成的数据表。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        schema_resort: true # 默认值为 true
```

### Serializer 序列化器

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    serializer:
        form_error_normalizer: false        # 表单错误序列化器，默认关闭
        knp_pagination_normalizer: false    # 分页数据序列化器，默认关闭
```
