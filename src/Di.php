<?php
namespace caylof;


/**
 * 依赖注入类
 *
 * <pre>
 * $di = new Di;
 * // 注入一个接口将来用哪个类来实现。
 * // 比如某个类的构造方法中依赖IRabbit接口，而这个类实例化是要用到LazyRabbit具体的类。
 * $di->set('caylof\test\IRabbit', LazyRabbit::className());
 *
 * // 注入一个依赖类，匿名函数中返回一个配置项数组，配置项中包涵class和args两项，args项可省略。
 * // 其中class表示具体的类名称，args表示构造方法中所需的参数表。
 * // 如果匿名函数中返回的的数组中不包括class项，则视为普通数组，容器中不会对其进行依赖转化。
 * $di->set('game', function() {
 *     return [
 *         'class' => Game::className(),
 *         'args' => [
 *             'title' => 'new game'
 *         ]
 *     ];
 * });
 *
 * // 直接注入一个类
 * $di->set('rabbit', Rabbit::className());
 *
 *
 * // 以匿名函数的方式注入一个类，当然匿名函数中可以是任意值。
 * $di->set('rabbit', function() {
 *     return new Rabbit();
 * });
 *
 * // 获取一个类实例，get方法中的参数为类名称，包括命名空间
 * $di->get(Game::className());
 *
 * // 获取一个类实例，get方法中参数为注入时的别名
 * $di->get('rabbit');
 * </pre>
 *
 * @package caylof
 * @author caylof<caylof@sina.com>
 */
class Di {

    /**
     * @var array
     */
    private $registry = [];

    /**
     * 注入依赖
     * 
     * @param string $name
     * @param string|Closure $dependency
     * @return null
     */
    public function set($name, $dependency) {
        $this->registry[$name] = $dependency;
    }

    /**
     * 获取实例
     *
     * @param string $name 类名称(包括namespace)或注入时的别名
     * @return mixed
     */
    public function get($name) {
        $className = isset($this->registry[$name]) ? $this->registry[$name] : $name;
        if (is_callable($className)) {
            $option = $className();
            if (!is_array($option) || !array_key_exists('class', $option)) {
                return $option;
            }
            $className = $option['class'];
            $optArgs = isset($option['args']) ? $option['args'] : null;
        }
        if (!class_exists($className)) {
            throw new \BadMethodCallException(sprintf('class "%s" not found', $className, 1));
        }
        $rc = new \ReflectionClass($className);
        if (!$rc->isInstantiable()) {
            throw new \BadMethodCallException(sprintf('class "%s" cannot be instantiable', $className), 1);
        }

        $constructor = $rc->getConstructor();
        if (is_null($constructor)) {
            return $rc->newInstance();
        }
        $params = $constructor->getParameters();
        $args = [];
        foreach ($params as $param) {
            $paramName = $param->getName();
            if (isset($optArgs) && isset($optArgs[$paramName])) {
                $args[] = $optArgs[$paramName];
                continue;
            }
            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
                continue;
            }

            $paramClass = $param->getClass();
            if (is_null($paramClass)) {
                throw new \InvalidArgumentException(sprintf('argument "$%s" must can be instantiable', $paramName), 2);
            }
            $args[] = $this->get($paramClass->name);
        }

        return $rc->newInstanceArgs($args);
    }

    public function getShared($name) {
        static $share = [];
        return isset($share[$name]) ? $share[$name] : $share[$name] = $this->get($name);
    }

}
