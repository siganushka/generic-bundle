# 全局配置

### Doctrine 表前缀

为 `Doctrine` 实体添加表前缀，依赖 `siganushka/doctrine-contracts` 组件。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        table_prefix: app_ # 可选，默认值为 null 时不添加表前缀
```

### Doctrine 实体覆盖

将 `Doctrine` 实体转化为抽象类 `MappedSuperclass` 后并由其它实体继承，用于实体的可选覆盖。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        mapping_override:
            App\Entity\Foo: App\Entity\MyFoo        # Foo 将被动态转化为 MappedSuperclass 后并由 MyFoo 继承
            App\Entity\Bar: App\Entity\CustomBar    # Bar 将被动态转化为 MappedSuperclass 后并由 CustomBar 继承
```

### Doctrine 字段重排

按照实体表的主键、外键、普通字段、公共字段（排序、状态、更新、创建时间）的顺序重新排列数据库字段，也可以使用 `siganushka:generic:schema-resort` 命令重新排列已生成的数据表。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        schema_resort: true # 可选，默值开启时自动排序，禁用后将按照 Doctrine 默认排序
```

### Serializer 序列化器

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    serializer:
        entity_mapping: false               # 自动化对实体按照一定规则添加序列化组，默认关闭
        form_error_normalizer: false        # 表单错误序列化器，默认关闭
        knp_pagination_normalizer: false    # 分页数据序列化器，默认关闭
```
