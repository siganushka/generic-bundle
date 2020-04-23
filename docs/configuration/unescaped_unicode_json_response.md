# Unescaped Unicode Json Response

为 `JsonResponse` 添加中文支持，详见：[让 JSON 更懂中文](https://www.laruence.com/2011/10/10/2239.html)

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    unescaped_unicode_json_response: true # 可选项，默认为 true，为 false 时使用默认编码。
```
