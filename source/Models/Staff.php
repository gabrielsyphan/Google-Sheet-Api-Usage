<?php


namespace Source\Models;


use CoffeeCode\DataLayer\DataLayer;

class Staff extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ost78_staff", [], 'staff_id', false);
    }
}
