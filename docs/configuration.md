# 全局配置

### Doctrine 表前缀

为 `Doctrine` 实体添加表前缀。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    table_prefix: app_ # 可选，默认值为 null，为 null 时不添加表前缀
```

### JSON 中文编码

为 `JsonResponse` 和 `Serializer` 提供中文编码，详见：[让 JSON 更懂中文](https://www.laruence.com/2011/10/10/2239.html)

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    json_encode_options: 271 # 可选，默认值为 271，支持中文编码
```

### HTML5 验证

禁止 `form` HTML5 验证。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    disable_html5_validation: true # 可选，默认值为 true，为 false 时开启 HTML5 验证
```
