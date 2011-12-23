<?php
/*
 * Интерфейс для адаптера работы с организациями
 */
interface MBAOrganisationManagerAdapter{
    /**
     * Возвращает организацию соответвующую идентификатору
     * @abstract
     * @param $id идентификатор организации
     * @return MBAOrganisation
     * @return null
     */
    public function getOrgById($id);

    /**
     * Возвращает массив организаций, соответвующих идентификаторам
     * @abstract
     * @param array $ids
     * @return array MBAOrganisation
     */
    //public function getOrgByIdIn(array $ids);

    /**
     * Возвращает организацию соответвующую сигле
     * @abstract
     * @param $code идентификатор организации
     * @return MBAOrganisation
     * @return null
     */
    public function getOrgByCode($code);

    /**
     * Возвращает организацию соответвующую сигле
     * @abstract
     * @param array $codes идентификатор организации
     * @return MBAOrganisation
     */
    //public function getOrgByCodeIn(array $codes);

    /**
     * @abstract
     * @param MBAOrganisation $organisation организация
     * @param bool $lazy загружать полностью организации или возращать только их идентификаторы
     * @return array MBAOrganisations or $ids
     */
    //public function getChildren(MBAOrganisation $organisation, $lazy=True);

    /**
     * Возвращает предков организации
     * @abstract
     * @param MBAOrganisation $organisation организация
     * @param bool $lazy загружать полностью организации или возращать только их идентификаторы
     * @return array MBAOrganisations or $ids
     */
    //public function getAncestors(MBAOrganisation $organisation, $lazy=True);

    /**
     * Возвращает список организаций, которым принадлежит пользователь
     * @abstract
     * @param MBAUser $user
     * @param bool $lazy ленивая загрузка. false - получить список организаций, true - список идентификаторов
     */
     public function getUserOrgs(MBAUser $user, $lazy=false);

    /**
     * Поиск организаций по названию
     * @param string $name
     * @param bool $lazy ленивая загрузка. false - получить список организаций, true - список идентификаторов организаций
     */
     public function getOrgsByName($name, $lazy=false);


    /**
     * Поиск организаций по ill_service
     * @param string $name
     * @param bool $lazy ленивая загрузка. false - получить список организаций, true - список идентификаторов организаций
     */
     public function getOrgsByIllService($addr, $lazy=false);


    /**
     * Поиск организаций по адресу edd
     * @param $mail
     * @param bool $lazy
     * @return array
     */
    public function getOrgsByEddService($addr, $lazy=false);


    /**
     * @param $mail
     * @param bool $lazy
     * @return array
     */
    public function getOrgsByMail($mail, $lazy=false);
}



/*
 * Интерфейс для адаптера работы с организациями
 */
interface MBAUserManagerAdapter {
    /**
     *
     * @abstract
     * @param $username
     * @param $password
     * @return MBAUser если пользователь аутентифицирован
     * @return null если не прошел аутентификацию
     */
    public function authUser($username, $password);
    /**
     * Возвращает пользователя соответвующего идентификатору
     * @abstract
     * @param $id идентификатор пользователя
     * @return MBAUser
     * @return null
     */
    public function getUserById($id);

    /**
     * Возвращает массив пользователей, соответвующих идентификаторам
     * @abstract
     * @param array $ids
     * @return array MBAUser
     */
    //public function getUserByIdIn($ids);

    /**
     * Возвращает пользователя в соответвии с его именем
     * @abstract
     * @param $username
     */
    public function getUserByUsername($username);


}