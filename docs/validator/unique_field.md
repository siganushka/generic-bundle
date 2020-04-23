# Unique Field

用于实体之外的唯一字段类型约束，**该类型约束不可用于 Dcotrine 实体**。

```php
// ./src/Model/Member.php 

use Siganushka\GenericBundle\Validator\Constraints\UniqueField;

class Member
{
    /*
    * 检测该值在 App\Entity\User::username 中是否存在
    *
    * @UniqueField(entityClass=App\Entity\User, field=username)
    */
    private $name;
}
```
