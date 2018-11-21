<?php
namespace App;

class Container
{
    protected $binds;

    protected $instances;

    public function bind($abstract, $concrete)
    {
        if ($concrete instanceof Closure) {
            $this->binds[$abstract] = $concrete;
        } else {
            $this->instances[$abstract] = $concrete;
        }
    }

    public function make($abstract, $parameters = [])
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        array_unshift($parameters, $this);

        return call_user_func_array($this->binds[$abstract], $parameters);
    }
    public function sd()
    {
        $container = new Container;

// 向该 超级工厂添加超人的生产脚本
        $container->bind('superman', function($container, $moduleName) {
            return new Superman($container->make($moduleName));
        });

// 向该 超级工厂添加超能力模组的生产脚本
        $container->bind('xpower', function($container) {
            return new XPower;
        });

// 同上
        $container->bind('ultrabomb', function($container) {
            return new UltraBomb;
        });

// ****************** 华丽丽的分割线 **********************
// 开始启动生产
        $superman_1 = $container->make('superman', 'xpower');
        $superman_2 = $container->make('superman', 'ultrabomb');
        $superman_3 = $container->make('superman', 'xpower');
    }
}

class Asd
{
    public function asd()
    {
        $superman = new Superman([
            'Fight' => [9, 100],
            'Shot' => [99, 50, 2]
        ]);
    }

}
class Superman
{
    protected $power;

    public function __construct(array $modules)
    {
        // 初始化工厂
        $factory = new SuperModuleFactory;

        // 通过工厂提供的方法制造需要的模块
        foreach ($modules as $moduleName => $moduleOptions) {
            $this->power[] = $factory->makeModule($moduleName, $moduleOptions);
        }
        // 创建超人

    }
}



class SuperModuleFactory
{
    public function makeModule($moduleName, $options)
    {
        switch ($moduleName) {
            case 'Fight':
                return new Flight($options[0], $options[1]);
            case 'Force':
                return new Force($options[0]);
            case 'Shot':
                return new Shot($options[0], $options[1], $options[2]);
        }
    }
}


class Flight
{
    protected $speed;
    protected $holdtime;
    public function __construct($speed, $holdtime) {}
}

class Force
{
    protected $force;
    public function __construct($force) {}
}

class Shot
{
    protected $atk;
    protected $range;
    protected $limit;
    public function __construct($atk, $range, $limit) {}
}