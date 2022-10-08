# 全局配置

### Doctrine 表前缀

该参数为 `Doctrine` 实体添加表前缀。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        table_prefix: test_ # 可选，默认值为 null 时不添加表前缀
```
