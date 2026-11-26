# 表单扩展

安装即生效的表单行为增强，以及常用的自定义表单类型和数据转换器。

### 全局扩展

以下扩展会自动应用到项目的表单上，无需在表单类中显式声明：

| 扩展 | 作用 |
| --- | --- |
| `CsrfTypeExtension` | `stateless` 路由自动关闭 `CSRF` 并允许额外字段，依据 `_stateless` 属性判断 |
| `ButtonTypeExtension` | 按钮默认文案为 `generic.submit`，翻译域为 `messages` |
| `ChoiceTypeExtension` | 选择框默认占位文案为 `generic.choice` |
| `CollectionTypeExtension` | 集合增删按钮的视图变量，见下文 |
| `MoneyTypeExtension` | 金额字段默认使用整数输入，并自动追加上限校验 |

`MoneyTypeExtension` 的默认行为：

- `input` 为 `integer`、`html5` 为 `true`，币种默认 `USD`；
- `scale` 按币种小数位自动计算，`divisor` 与 `step` 随之联动；
- 自动追加 `LessThanOrEqual(2147483647)` 约束，避免金额转换成分为单位后溢出 `int32`，可通过表单选项 `currency` 覆盖币种。

### 集合按钮

设置 `allow_add` 或 `allow_delete` 后，视图会注入按钮变量，模板中直接渲染即可：

```php
$builder->add('items', CollectionType::class, [
    'entry_type' => ItemType::class,
    'allow_add' => true,
    'allow_delete' => true,
]);
```

- `allow_add`：表单视图提供 `add_button`；
- `allow_delete`：每个子项视图和 `prototype` 提供 `delete_button`；
- 默认文案为 `generic.add`、`generic.remove`，可通过 `add_button_options`、`delete_button_options` 覆盖。

```twig
{{ form_row(form.items.vars.add_button) }}
```

### 时间范围类型

`DateRangeType` 生成 `startAt`、`endAt` 两个字段，并自动为结束时间追加“不早于开始时间”的约束：

```php
use Siganushka\GenericBundle\Form\Type\DateRangeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

$builder->add('period', DateRangeType::class, [
    'entry_type' => DateTimeType::class, // 支持 DateType、DateTimeType、TimeType
]);
```

- 默认 `entry_type` 为 `DateType`；
- `startAt` 默认不显示标签，`endAt` 默认标签为 `generic.to`；
- 可分别通过 `start_options`、`end_options` 传入子字段选项。

配合 [实体模型](doctrine.md) 中的 `DateRange` 内嵌对象使用。

### 实体转标识符

`EntityToIdentifierTransformer` 用于“表单提交标识符、模型持有实体”的场景，例如下拉框只提交用户 `ID`：

```php
use Siganushka\GenericBundle\Form\DataTransformer\EntityToIdentifierTransformer;

$builder->add('user', TextType::class);
$builder->get('user')->addModelTransformer(
    new EntityToIdentifierTransformer($managerRegistry, User::class, 'id')
);
```

- `transform()`：实体转换为标识符字符串；
- `reverseTransform()`：按标识符查询实体，不存在时抛出 `TransformationFailedException`。

### 翻译

组件内置 `messages.zh_CN.php`、`messages.en.php` 与对应的 `validators` 翻译，`generic.*` 文案无需在项目中重复定义。
