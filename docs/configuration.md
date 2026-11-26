# 全局配置

### Doctrine 表前缀

为 `Doctrine` 实体添加表前缀，依赖 `siganushka/doctrine-contracts` 组件。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    doctrine:
        table_prefix: app_ # 默认值为 null
```

### Serializer 序列化器

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    serializer:
        form_error_normalizer: false        # 表单错误序列化器，默认关闭
        knp_pagination_normalizer: false    # 分页数据序列化器，默认关闭
```

### 请求格式

强制指定请求格式，避免 `Symfony` 依据 `Accept` 请求头自行协商（例如让 `/api/*` 的错误响应始终为 `JSON`）。

```yaml
# ./config/packages/siganushka_generic.yaml

siganushka_generic:
    request_format:
        - { pattern: '^/(api|shop)', format: json }
        - { pattern: '^/server', format: xml }
```
