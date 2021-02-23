<?php


namespace Source\Models;


use CoffeeCode\DataLayer\DataLayer;

class Status extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ost78_ticket_status", [], 'id', false);
    }
}
