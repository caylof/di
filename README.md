依赖注入容器
============

在面向对象编程中，类与类之间应该“低耦合”，而大多数情况下单一类完成某个职责的情况很少，类之间相互调用才能体现出类的真正强大之处，因此类之间又要有所依赖。

通常类之间依赖通过构造函数或者setter来注入，下面举个简单的栗子。

例如要设计“龟兔赛跑”这么一个故事，首先应该有两个类：兔子类（`Rabbit`）、乌龟类（`Tortoise`）。

```php
class Rabbit implements IRabbit {
    public function run() {}
}

class Tortoise implements ITortoise {
    public function crawl() {}
}
```

然后再设计一个比赛类（`Game`），它依赖兔子类（`Rabbit`）和乌龟类（`Tortoise`），我们以构造函数的方式注入其中以供后续调用。

```php
class Game {

    private $rabbit;
    private $tortoise;

    // 构造函数注入依赖
    public function __construct(IRabbit $rabbit, ITortoise $tortoise) {
        $this->rabbit = $rabbit;
        $this->tortoise = $tortoise;
    }

    public function getResult() {}
}
```

最后这几个类之间的相互调用来完成“龟兔赛跑”这么一个比赛。

```php
$rabbit = new Rabbit();
$tortoise = new Tortoise();
$game = new Game($rabbit, $tortoise);
$game->getResult();
```

比赛类`Game`并没有强依赖于兔子类`Rabbit`和乌龟类`Tortoise`，这里仅在构造函数中注入了兔子类`Rabbit`和乌龟类`Tortoise`各一个实例，然后调用方法`getResult()`以获取比赛结果。
在注入过程中，以不同的实例注入，将会得到不同的比赛结果，比如我们注入的兔子实例`$rabbit`是一只懒惰的兔子，那么比赛结果就是兔子比赛失败。

到这里，上面的例设计子看似完美，其实还存在这么一个问题：就是每次在实例化比赛类`Game`前，必须要先实例化出兔子类`Rabbit`和乌龟类`Tortoise`。如果要在多处实例化`Game`类，那么这个实例化过程就显得很重复。

换个场景，这样设计的类如果依赖注入有很多，那么其实例化的过程更是显得重复，更如是依赖之间又相互依赖，依赖之间有先后关系，那么情况更是复杂。试想下面的场景：

```php
$component = new Component(
    new A(),
    new B(),
    new C(new D()),
    ...
);
```

仅仅一个实例化过程，就显得如此的繁琐，更何况还要在多处调用。所以“依赖注入”迫切需要“自动化”来取缔手工注入的造成的重复和麻烦。因此，“依赖注入容器”应运而生。

什么是“依赖注入容器”呢，我的理解，就是将所有依赖都放入一个“容器”中，这个容器能够自动地维护这些依赖关系，从而能够从容器中轻松地取了一个类的实例。比如使用了容器后,下面实例化起来也就显得简单多了。

```php
$di = new Di();
$component = $di->get(Component::className());
```

这里的`/src/Di.php`正是这样一个简单的实现。