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

将 `Doctrine` 实体转化为抽象类 `MappedSuperclass` 后并由其它实体继承，常用于实体的可选覆盖。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        mapping_override:
            App\Entity\Foo: App\Entity\MyFoo
            App\Entity\Bar: App\Entity\CustomBar
```

### Serializer 序列化器

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    serializer:
        form_error_normalizer: true     # 表单错误序列化器，默认开启
        knp_pagination_normalizer: true # 分页数据序列化器，默认开启
```
