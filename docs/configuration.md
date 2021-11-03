# 全局配置

### Doctrine 表前缀

该参数为 `Doctrine` 实体添加表前缀。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    table_prefix: app_ # 可选，默认值为 null 时不添加表前缀
```

### 日期格式/时区

该参数为 `Serializer` 提供自定义日期格式/时区。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    datetime_format: 'Y-m-d H:i:s'      # 可选，默认值为 Y-m-d H:i:s
    datetime_timezone: 'Asia/Shanghai'  # 可选，默认值为 null 时由系统决定当前时区
```

### JSON 中文编码

该参数为 `Serializer` 和 `JsonResponse` 提供中文编码。

详见：[让 JSON 更懂中文](https://www.laruence.com/2011/10/10/2239.html)

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    json_encode_options: 271 # 可选，默认值为 271 时支持中文编码
```
