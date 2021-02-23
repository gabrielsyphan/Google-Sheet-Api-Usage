<?php


namespace Source\Models;


use CoffeeCode\DataLayer\DataLayer;

class Ticket extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ost78_ticket", [], 'ticket_id', false);
    }
}
