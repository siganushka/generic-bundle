# Generic bundle for symfony

[![Build Status](https://travis-ci.org/siganushka/generic-bundle.svg?branch=master)](https://travis-ci.org/siganushka/generic-bundle)

通用的功能性 `bundle`，提取于各项目中的可复用代码。

### 安装

```bash
$ composer require siganushka/generic-bundle
```

### 注册

```php
// ./config/bundles.php

<?php

return [
    // ...
    Siganushka\GenericBundle\SiganushkaGenericBundle::class => ['all' => true],
];
```

### 功能

- Configuration
	- [table_prefix](docs/configuration/table_prefix.md)
	- [unescaped_unicode_json_response](docs/configuration/unescaped_unicode_json_response.md)

- Entity
	- [Resource](docs/model/resource.md)
	- [Timestampable](docs/model/timestampable.md)
	- [Enable](docs/model/enable.md)
	- [Sortable](docs/model/sortable.md)
	- [Versionable](docs/model/versionable.md)

- Validator
	- [PhoneNumber](docs/validator/phone_number.md)
	- [Semver](docs/validator/semver.md)
	- [UniqueField](docs/validator/unique_field.md)

- Registry
	- [Registry](docs/registry/registry.md)
	- [AliasableServiceInterface](docs/registry/registry.md#AliasableServiceInterface)
