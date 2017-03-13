##监听数据库操作 listen database by doctrine
使用场景为：每次操作数据库前后可以监听到。通过doctrine给定的监听机制。
scenes to be used: if you want to listen when operate database, doctrine give some listener for you.

###code
```
//AppBundle\EventListener\PreFlushExampleListener
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class PreFlushExampleListener
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function preFlush(PreFlushEventArgs $args)
    {
        //you can do anything here
        die('haved persist, wait flush')
    }
}
```
service:

```
    app.pre_flush_example_listener:
        class: AppBundle\EventListener\PreFlushExampleListener
            arguments: ['@service_container']
        tags:
            - { name: doctrine.event_listener, event: preFlush }
```

that is enough...

there is also have some listener [doctrine listener](http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/events.html)  

only change preflush for what you want.
