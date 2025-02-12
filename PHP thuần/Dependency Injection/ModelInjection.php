<?php

require 'UserModel.php';

class ModelInjection{
    private $model;

    public function __construct(User $user = new User()) {
        $this->model = $user;
    }

    public function getModel(){
        return $this->model;
    }
}


