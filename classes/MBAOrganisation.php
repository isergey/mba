<?php
class MBAOrganisation
{
    public $id = null;
    public $parentId = null; //Родительская организация
    public $code = null;
    public $name = null;
    public $country = null;
    public $city = null;
    public $district = null;

    public $mail = null;
    public $plans = null;
    public $postalAddress = null;
    public $latitude = null;
    public $longitude = null;

    public $illService = null;
    public $mailAccess = null;
    public $eddService = null;
    public $httpService = null;
    public $phone = null;

    public function __construct(){}

}
