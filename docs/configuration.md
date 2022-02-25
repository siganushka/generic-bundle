# 全局配置

### Doctrine 表前缀

该参数为 `Doctrine` 实体添加表前缀。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        table_prefix: test_ # 可选，默认值为 null 时不添加表前缀
```
``

### JSON 中文编码

该参数为 `Serializer` 和 `JsonResponse` 提供中文编码。

详见：[让 JSON 更懂中文](https://www.laruence.com/2011/10/10/2239.html)

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    json:
        encoding_options: 271 # 可选，默认值为 271 时支持中文编码
```
