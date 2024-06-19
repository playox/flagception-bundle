Route condition
-------------------------
You can use route attributes for checking the feature state in controllers. This is activated.

```php
// src/AppBundle/Controller/BlogController.php
// src/Controller/BlogController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BlogController extends Controller
{
    /**
     * @Route("/blog/{page}", condition="is_feature('feature_123')")
     */
    public function listAction($page)
    {
        // ...
    }

    /**
     * @Route("/blog/{slug}")
     */
    public function showAction($slug)
    {
        // ...
    }
}
```