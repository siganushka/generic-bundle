# Features

注册行政区划路由。

```yaml
# ./config/routes.yaml

siganushka_generic:
    resource: "@SiganushkaGenericBundle/Resources/config/routing/routes.php"
```

更新行政区划数据（来源腾讯地图）。

```bash
$ php bin/console siganushka:region:update {KEY} # key 为腾讯地图令牌
```
