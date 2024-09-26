<?php

class Employees
{
    public $customer_id;
    public $address;
    public $phone;

    public function __construct(public $name, public $email) {
        $this->name = $name;
        $this->email = $email;
    }
}