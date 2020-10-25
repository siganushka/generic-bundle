# Features

导入行政区划相关实体，在 `doctrine.orm.mappings` 下新增配置。

```yaml
# ./config/packages/doctrine.yaml

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

为实体添字段

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
            ->add('region', RegionSubjectType::class)
        ;
    }
}
```

添加其参数，比如添加必选和提示字符：

```php

$builder
    ->add('region', RegionSubjectType::class, [
        'province_options' => [
            'placeholder' => '--- 请选择 ---',
            'constraints' => new NotBlank(),
        ],
        'city_options' => [
            'placeholder' => '--- 请选择 ---',
            'constraints' => new NotBlank(),
        ],
        'district_options' => [
            'placeholder' => '--- 请选择 ---',
            'constraints' => new NotBlank(),
        ],
    ])
;
```

客户端实现联动效果，以 `jquery` 获取数据为例：

```javascript
$(function() {
  $('#PROVINCE_ID,#CITY_ID').on('change', function(event) {
    var $target = (event.currentTarget.id === 'PROVINCE_ID')
        ? $('#CITY_ID')
        : $('#DISTRICT_ID')

    // 占位提示由后端定义，使其保持一致
    var placeholder = $target.data('placeholder')
    var data = { parent: event.currentTarget.value }

    $.getJSON('{{ path("siganushka_generic_region") }}', data, function(r) {
        var options = ['<option value="">'+ placeholder +'</option>']

        $.each(r, function(idx, el) {
            options.push('<option value="'+ el.code +'">'+ el.name +'</option>')
        })

        $target.html(options.join('')).trigger('change')
    })
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
