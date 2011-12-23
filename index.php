<?php
//require_once 'adapters/interfaces.php';
//require_once 'adapters/MBALDAPAdapter.php';
require_once 'adapters/MbaLibCmsRestAdapter.php';



global $adapter;
$adapter= 'rest';


class MbaUserManager{
    public static function  getAdapter(){
        global $adapter;
        if ($adapter === 'rest'){
            return new MbaUserLibCmsRestAdapter('http://libportal.megazi.ru');
        }

    }

}

class MbaOrganisationManager{
    public static function  getAdapter(){
        global $adapter;
        if ($adapter === 'rest'){
            //return new MbaOrganisationLibCmsRestAdapter('http://libportal.megazi.ru');
            return new MbaOrganisationLibCmsRestAdapter('http://127.0.0.1:8000');
        }

    }
}

try{

$userManager = MbaUserManager::getAdapter();

$orgManager = MbaOrganisationManager::getAdapter();



$user = $userManager->authUser('root', 'superksob');
var_dump($user);

//$user = $userManager->authUser('root1', 'superksob');
//var_dump($user);
//
//
//$user = $userManager->getUserById('1');
//var_dump($user);
//
//
//$user = $userManager->getUserById('-10');
//var_dump($user);
//
//
//$user = $userManager->getUserByUsername('root');
//var_dump($user);
//
//
//$user = $userManager->getUserByUsername('root1');
//var_dump($user);
//
//
//$user = $userManager->getUserByUsername('root');
//
//$orgs = $orgManager->getUserOrgs($user);
//var_dump($orgs);
//
$orgs = $orgManager->getOrgsByName('Центральная районная библиотека им.Л.Соболева');
var_dump($orgs);

$orgs = $orgManager->getOrgsByIllService('ill@mail.ru');
var_dump($orgs);

//$org = $orgManager->getOrgByCode('19017901');
//var_dump($org);
//
//$org = $orgManager->getOrgByCode('190179011');
//var_dump($org);
//
$org = $orgManager->getOrgById('1');
var_dump($org);
//
//$org = $orgManager->getOrgById('100');
//var_dump($org);

}catch (Exception $e){
    echo $e->getMessage();
}