# Configuration

默认值为 `JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE`。

详见：[让 JSON 更懂中文](https://www.laruence.com/2011/10/10/2239.html)

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    json_encode_options: 271 # 可选，默认值为 271，支持中文编码
```
