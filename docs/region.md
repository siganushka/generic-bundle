# Features

注册路由。

```yaml
# ./config/routes.yaml

siganushka_generic:
    resource: "@SiganushkaGenericBundle/Resources/config/routes.php" # 导入接口
```

更新行政区划数据（来源腾讯地图）。

```bash
$ php bin/console siganushka:region:update {KEY} # key 为腾讯地图令牌
```
