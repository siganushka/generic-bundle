# 行政区划

### 更新实体

```bash
$ php bin/console doctrine:schema:update --force
```

### 导入数据

```bash
$ php bin/console siganushka:region:update
```

默认情况下由 `Symfony Flex` 自动导入路由，导入文件为 `./config/routes/siganushka_generic.yaml`，如果未正常导入，可选择手动导入，路由名称为 `siganushka_generic_region`。

```yaml
# ./config/routes.yaml

siganushka_generic:
    resource: "@SiganushkaGenericBundle/Resources/config/routing/routes.xml"
```

### 关联实体

为实体添字段，默认为省 `province`、市 `city`、区 `district` 三级。

```php
// src/Entity/Foo.php

use Siganushka\GenericBundle\Entity\RegionSubjectInterface;
use Siganushka\GenericBundle\Entity\RegionSubjectTrait;

class Foo implements RegionSubjectInterface
{
    use RegionSubjectTrait;

    // ...
}
```

### 表单字段

```php
// src/Form/FooType.php

use Siganushka\GenericBundle\Form\Type\RegionSubjectType;

class FooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // region 为虚拟名称，可随便填写
            ->add('region', RegionSubjectType::class, [
                // 也可以添加其它参数，比如占位提示和必填
                // 'province_options' => [
                //     'placeholder' => '--- 请选择 ---',
                //     'constraints' => new NotBlank(),
                // ],
                // 'city_options' => [
                //     'placeholder' => '--- 请选择 ---',
                //     'constraints' => new NotBlank(),
                //  ],
                // 'district_options' => [
                //     'placeholder' => '--- 请选择 ---',
                //     'constraints' => new NotBlank(),
                // ],
            ])
        ;
    }

    // ...
}
```

### 前端联动

客户端实现联动效果，以 `jquery` 获取为例：

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

### 数据过滤

获取数据时如果想排除某些数据，可以使用 `RegionFilterEvent` 事件过滤，比如过滤掉直辖市：

```php
// src/EventSubscriber/RemoveDirectlyRegionSubscriber.php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Siganushka\GenericBundle\Event\RegionFilterEvent;
use Siganushka\GenericBundle\Entity\RegionInterface;

class RemoveDirectlyRegionSubscriber implements EventSubscriberInterface
{
    const DIRECTLY_CODES = [110000, 120000, 310000, 500000];

    public function onRegionFilterEvent(RegionFilterEvent $event)
    {
        $regions = array_filter($event->getRegions(), function(RegionInterface $region) {
            return !in_array($region->getCode(), self::DIRECTLY_CODES);
        });

        $event->setRegions($regions);
    }

    public static function getSubscribedEvents()
    {
        return [
            RegionFilterEvent::class => 'onRegionFilterEvent',
        ];
    }
}
```
