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

### 表单 html5 验证

关闭 `Form` 组件的 `HTML5` 验证

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    form:
        html5_validation: true # 为 false 时关闭 HTML5 验证
```
