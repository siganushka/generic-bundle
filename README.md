# Generic bundle for symfony

[![Build Status](https://travis-ci.org/siganushka/generic-bundle.svg?branch=master)](https://travis-ci.org/siganushka/generic-bundle)
[![Latest Stable Version](https://poser.pugx.org/siganushka/generic-bundle/v/stable)](https://packagist.org/packages/siganushka/generic-bundle)
[![Latest Unstable Version](https://poser.pugx.org/siganushka/generic-bundle/v/unstable)](https://packagist.org/packages/siganushka/generic-bundle)
[![License](https://poser.pugx.org/siganushka/generic-bundle/license)](https://packagist.org/packages/siganushka/generic-bundle)

通用的功能性 [Bundle](https://symfony.com/doc/current/bundles.html)，提取于各项目中的可复用代码。

### 安装

```bash
$ composer require siganushka/generic-bundle
```

### 功能

- Configuration
	- [table_prefix](docs/configuration/table_prefix.md)
	- [json_encode_options](docs/configuration/json_encode_options.md)

- Model
	- [Resource](docs/model/resource.md)
	- [Sortable](docs/model/sortable.md)
	- [Enable](docs/model/enable.md)
	- [Versionable](docs/model/versionable.md)
	- [Timestampable](docs/model/timestampable.md)

- Validator
	- [PhoneNumber](docs/validator/phone_number.md)
	- [Semver](docs/validator/semver.md)

- Registry
	- [Registry](docs/registry/registry.md)
	- [AliasableInterface](docs/registry/registry.md#AliasableInterface)
