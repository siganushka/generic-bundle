# CHANGELOG for 1.0.x

- 重命名 `ResourceInterface::isEqualTo` 方法改为 `equals`（与 `EquatableInterface::isEqualTo` 冲突）
- 重命名 `SortableInterface::getSort` 方法为 `getSorted`。
- 修改 `SortableInterface::DEFAULT_SORT` 常量改为 `DEFAULT_SORTED`。
- 修改 `SortableInterface::DEFAULT_SORT` 默认值 `255` 改为 `0`
- 移除 `DisableHtml5ValidateTypeExtension` 类
- 移除 `siganushka_generic.disable_html5_validation` 配置项
