# Features

注册行政区划路由。

```yaml
# ./config/routes.yaml

api_regions:
    path: /api/regions
    controller: Siganushka\GenericBundle\Controller\RegionController
```

更新行政区划数据（来源腾讯地图）。

```bash
$ php bin/console siganushka:region:update {KEY} # key 为腾讯地图令牌
```
