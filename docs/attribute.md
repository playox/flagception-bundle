Attribute
-------------------------
We recommend to use the route attribute solution, because using annotations has performance issues.
A `NotFoundHttpException` will be thrown if you request an action or class with inactive feature flag.


```php
# FooController.php

use Flagception\Bundle\FlagceptionBundle\Attribute\Feature;

#[Feature("feature_123")]
class FooController
{

    #[Feature("feature_789")]
    public function barAction()
    {
    }

    public function fooAction()
    {
    }
}
```
