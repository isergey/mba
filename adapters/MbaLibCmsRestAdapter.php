<?php

require_once 'interfaces.php';
require_once 'classes/MBAUser.php';
require_once 'classes/MBAOrganisation.php';
require_once 'pest/PestJSON.php';


class AdapterException extends Exception {}

/**
 * Переопределяет некоторые методы из PestJSON для удобства
 */
class LibCmsPestJSON extends PestJSON {
    public static  $sessionId = null;

    public function __construct($serverUrl, $username=null, $password=null, $authUrl='/api/auth/'){
        parent::__construct($serverUrl);
        if ($username !== null and $password !== null){
            $response = $this->get($authUrl, array(
                'username'=>$username,
                'password'=>$password,
            ));
            if (isset($response['api.sessionid'])){
                LibCmsPestJSON::$sessionId = $response['api.sessionid'];
            }
        }
    }

    public function get($url, $args = array()){
        $httpArgs = array();
        if (LibCmsPestJSON::$sessionId !== null){
            $httpArgs[]= 'api.sessionid='.urldecode(LibCmsPestJSON::$sessionId);
        }

        foreach($args as $key=>$value){
            $httpArgs[] = urlencode($key).'='.urlencode($value);
        }
        $url = $url.'?'.join($httpArgs, '&');

        $response = parent::get($url);
        if (isset($response['status']) === True and $response['status'] === 'error'){
            if (isset($response['error'])){
                throw new AdapterException($response['error']);
            };
        }

        if (isset($response['status']) === True and $response['status'] === 'ok'){
            if (isset($response['response']) === True){
                return $response['response'];
            }
        }
    }

    public function post($url, $data, $headers = array()){
        $response = parent::post($url, $data, $headers);
        if (isset($response['error'])){
            throw new AdapterException($response['error']['message']);
        };

        if (isset($response['status']) === True and $response['status'] === 'ok'){
            if (isset($response['response']) === True){
                return $response['response'];
            }
        }
    }
}


class MbaUserLibCmsRestAdapter implements  MBAUserManagerAdapter {

    private $connection = null;

    /**
     * @param $serverUrl адрес сервера
     */
    public function __construct($serverUrl, $username=null, $password=null){
        $this->connection = new LibCmsPestJSON($serverUrl, $username, $password);

    }


    public function authUser($username, $password){

        $username = urlencode($username);
        $password = urlencode($password);

        $response = $this->connection->get("/participants/api/auth_user/", array(
            'username'=>$username,
            'password'=>$password,
        ));

        if (count($response) === 0){
            return null;
        }

        return $this::mbaUserFromArray($response);
    }

    public function getUserById($id){
        $id = urlencode($id);
        $response = $this->connection->get("/participants/api/get_user/",array('id'=>$id));
        if (count($response) === 0){
            return null;
        }
        return $this::mbaUserFromArray($response);
    }

    public function  getUserByUsername($username){
        $username = urlencode($username);
        $response = $this->connection->get("/participants/api/get_user/", array('username'=>$username));
        if (count($response) === 0){
            return null;
        }
        return $this::mbaUserFromArray($response);
    }

//    public function getUserOrgs(MBAUser $user, $lazy=false){
//        $id = urlencode($user->id);
//        $response = $this->connection->get("/participants/api/get_user_orgs/?id=$id");
//        return $response;
//    }

    public static function mbaUserFromArray(array $userArray){
        $user = new MBAUser();

        if (isset($userArray['id']) === true){
            $user->id = $userArray['id'];
        }

        if (isset($userArray['username']) === true){
            $user->username = $userArray['username'];
        }

        if (isset($userArray['password']) === true){
            $user->password = $userArray['password'];
        }

        if (isset($userArray['first_name']) === true){
            $user->firstName = $userArray['first_name'];
        }

        if (isset($userArray['last_name']) === true){
            $user->lastName = $userArray['last_name'];
        }

        if (isset($userArray['email']) === true){
            $user->email = $userArray['email'];
        }

        if (isset($userArray['phone']) === true){
            $user->phone = $userArray['phone'];
        }

        if (isset($userArray['date_joined']) === true){
            $user->dateJoined = new DateTime($userArray['date_joined']);
        }

        return $user;
    }
}



class MbaOrganisationLibCmsRestAdapter implements MBAOrganisationManagerAdapter{
    private $connection = null;

    /**
     * @param $serverUrl адрес сервера
     */
    public function __construct($serverUrl,$username=null, $password=null){
        $this->connection = new LibCmsPestJSON($serverUrl, $username, $password);

    }


    /**
     * Поиск организации по атрибуту
     * @param array $attr
     * @param $value
     * @param bool $lazy
     * @return array
     */
    private function getOrgByAttribute($attr, $value, $lazy=false){
        $response = $this->connection->get('/participants/api/get_org/', array($attr=>$value,'lazy'=>$lazy));
        if (count($response) === 0){
            return null;
        }
        return $this::mbaOrganisationFromArray($response);
    }

    /**
     * Поиск организаций по атрибуту
     * @param array $attr
     * @param $value
     * @param bool $lazy
     * @return array
     */
    private function getOrgsByAttribute($attr, $value, $lazy=false){
        $response = $this->connection->get('/participants/api/find_orgs/', array($attr=>$value,'lazy'=>$lazy));
        $orgs = array();
        foreach ($response as $orgArray){
            $orgs[] = $this::mbaOrganisationFromArray($orgArray);
        }
        return $orgs;
    }



    /**
     * Возвращает организацию соответвующую идентификатору
     * @abstract
     * @param $id идентификатор организации
     * @return MBAOrganisation
     * @return null
     */
    public function getOrgById($id){
        return $this->getOrgByAttribute('id', $id);
    }

    /**
     * Возвращает организацию соответвующую сигле
     * @abstract
     * @param $code идентификатор организации
     * @return MBAOrganisation
     * @return null
     */
    public function getOrgByCode($code){
        return $this->getOrgByAttribute('code', $code);
    }

    /**
     * Возвращает список организаций, которым принадлежит пользователь
     * @abstract
     * @param MBAUser $user
     * @param bool $lazy ленивая загрузка. false - получить список организаций, true - список идентификаторов организаций
     */
     public function getUserOrgs(MBAUser $user, $lazy=false){
         $response = $this->connection->get('/participants/api/get_user_orgs/', array('id'=>$user->id,'lazy'=>$lazy));
         if (count($response) === 0){
             return null;
         }
         $orgs = array();
         foreach ($response as $orgArray){
             $orgs[] = $this::mbaOrganisationFromArray($orgArray);
         }
         return $orgs;
     }





    /**
     * Поиск организаций по названию
     * @param string $name
     * @param bool $lazy ленивая загрузка. false - получить список организаций, true - список идентификаторов организаций
     */
     public function getOrgsByName($name, $lazy=false){
         return $this->getOrgsByAttribute('name', $name, $lazy);
     }


    /**
     * Поиск организаций по ill_service
     * @param string $name
     * @param bool $lazy ленивая загрузка. false - получить список организаций, true - список идентификаторов организаций
     */
     public function getOrgsByIllService($addr, $lazy=false){
         return $this->getOrgsByAttribute('ill_service', $addr, $lazy);
     }


    /**
     * Поиск организаций по адресу edd
     * @param $mail
     * @param bool $lazy
     * @return array
     */
    public function getOrgsByEddService($addr, $lazy=false){
        return $this->getOrgsByAttribute('edd_service', $addr, $lazy);
    }


    /**
     * @param $mail
     * @param bool $lazy
     * @return array
     */
    public function getOrgsByMail($mail, $lazy=false){
        return $this->getOrgsByAttribute('mail', $mail, $lazy);
    }


    public static function mbaOrganisationFromArray(array $orgArray){
        $org = new MBAOrganisation();

        if (isset($orgArray['id']) === true){
            $org->id = $orgArray['id'];
        }

        if (isset($orgArray['parent_id']) === true){
            $org->parentId = $orgArray['parent_id'];
        }

        if (isset($orgArray['code']) === true){
            $org->code = $orgArray['code'];
        }

        if (isset($orgArray['name']) === true){
            $org->name = $orgArray['name'];
        }

        if (isset($orgArray['country']) === true){
            $org->country = $orgArray['country'];
        }

        if (isset($orgArray['city']) === true){
            $org->city = $orgArray['city'];
        }

        if (isset($orgArray['district']) === true){
            $org->district = $orgArray['district'];
        }

        if (isset($orgArray['mail']) === true){
            $org->mail = $orgArray['mail'];
        }

        if (isset($orgArray['plans']) === true){
            $org->plans = $orgArray['plans'];
        }

        if (isset($orgArray['postal_address']) === true){
            $org->postalAddress = $orgArray['postal_address'];
        }

        if (isset($orgArray['latitude']) === true){
            $org->latitude = $orgArray['latitude'];
        }

        if (isset($orgArray['longitude']) === true){
            $org->longitude = $orgArray['longitude'];
        }

        if (isset($orgArray['ill_service']) === true){
            $org->illService = $orgArray['ill_service'];
        }

        if (isset($orgArray['mail_access']) === true){
            $org->mailAccess = $orgArray['mail_access'];
        }

        if (isset($orgArray['edd_service']) === true){
            $org->eddService = $orgArray['edd_service'];
        }

        if (isset($orgArray['http_service']) === true){
            $org->httpService = $orgArray['http_service'];
        }

        if (isset($orgArray['phone']) === true){
            $org->phone = $orgArray['phone'];
        }

        return $org;
    }
}



