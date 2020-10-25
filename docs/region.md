# Features

导入行政区划相关实体，在 `doctrine.orm.mappings` 下新增配置。

```yaml
# ./config/packages/doctrine.yaml

doctrine:
    orm:
        mappings:
            SiganushkaGenericBundle:
                type: annotation
                dir: 'Model'
                prefix: 'Siganushka\GenericBundle\Model'
```

导入路由，前端获取数据路由名为 `siganushka_generic_region`。

```yaml
# ./config/routes.yaml

siganushka_generic:
    resource: "@SiganushkaGenericBundle/Resources/config/routing/routes.php"
```

为实体添字段，默认为省 `province`、市 `city`、区 `district` 三级。

```php
// src/Entity/Foo.php

use Siganushka\GenericBundle\Model\RegionSubjectInterface;
use Siganushka\GenericBundle\Model\RegionSubjectTrait;

class Foo implements RegionSubjectInterface
{
    use RegionSubjectTrait;

    // ...
}
```

添加表单字段

```php
// src/Form/FooType.php

use Siganushka\GenericBundle\Form\Type\RegionSubjectType;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // region 为虚拟名称，可随便填写
            ->add('region', RegionSubjectType::class)

            // 也可以添加其它参数，比如占位提示和必填
            // ->add('region', RegionSubjectType::class, [
            //     'province_options' => [
            //         'placeholder' => '--- 请选择 ---',
            //         'constraints' => new NotBlank(),
            //     ],
            //     'city_options' => [
            //         'placeholder' => '--- 请选择 ---',
            //         'constraints' => new NotBlank(),
            //     ],
            //     'district_options' => [
            //         'placeholder' => '--- 请选择 ---',
            //         'constraints' => new NotBlank(),
            //     ],
            // ])
            // ...
        ;
    }

    // ...
}
```

客户端实现联动效果，以 `jquery` 获取数据为例：

```javascript
$(function() {
  var $province = $('#{{ form.region.province.vars.id }}')
  var $city = $('#{{ form.region.city.vars.id }}')
  var $district = $('#{{ form.region.district.vars.id }}')

  var update = function(parent, $target) {
    $.getJSON('{{ path("siganushka_generic_region") }}', { parent: parent }, function(r) {
      var options = []
      $.each(r, function(idx, el) {
        options.push('<option value="'+ el.code +'">'+ el.name +'</option>')
      })
      $target.html(options.join('')).trigger('change')
    })
  }

  $province.on('change', function (event) {
    update(event.currentTarget.value, $city)
  })

  $city.on('change', function (event) {
    update(event.currentTarget.value, $district)
  })
})
```

获取数据时如果想排除某些数据，可以使用 `RegionFilterEvent` 事件过滤，比如过滤掉直辖市：

```php
// src/EventSubscriber/RemoveDirectlyRegionSubscriber.php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Siganushka\GenericBundle\Event\RegionFilterEvent;
use Siganushka\GenericBundle\Model\RegionInterface;

class RemoveDirectlyRegionSubscriber implements EventSubscriberInterface
{
    const DIRECTLY_CODES = [110000, 120000, 310000, 500000];

    public function onRegionFilterEvent(RegionFilterEvent $event)
    {
        $regions = array_filter($event->getRegions(), function(RegionInterface $region) {
            return !in_array($region->getCode(), self::DIRECTLY_CODES);
        });

        $event->setRegions(array_values($regions));
    }

    public static function getSubscribedEvents()
    {
        return [
            RegionFilterEvent::class => 'onRegionFilterEvent',
        ];
    }
}
```

更新行政区划数据（来原 Github）

```bash
$ php bin/console siganushka:region:update
```
