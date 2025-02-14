<?php

// khai báo service
class Container{
    protected $providers = [];

    public function bind($key, callable $callback){
        $this->providers[$key] = $callback;
    }

    public function make($key){
        return $this->providers[$key]();
    }
}

// khai báo class User
class User{
    protected $name;
    protected $age;

    public function __construct($name, $age){
        $this->name = $name;
        $this->age = $age;
    }

    public function getName(){
        return $this->name;
    }

    public function getAge(){
        return $this->age;
    }
}

// khai báo class UserDetail Dependency injection 
class UserDetail{
    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function getUser(){
        return $this->user;
    }
}

// khởi tạo container
$container = new Container();

// bind
$container->bind('user', function(){
    // dependency injection
    return new UserDetail(new User('John', 20));
});

// user
print_r($container->make('user'));