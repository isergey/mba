<?php
class MBAUser
{
    /*
     * Лучше паблики не делать, но пока сойдет
     */
    public $id = null;
    public $username = null; // логин
    public $password = null;
    public $firstName = null;
    public $lastName = null;
    public $phone = null;
    public $email = null;
    public $dateJoined = null; // DateTime


    public function __construct(){

    }

}
