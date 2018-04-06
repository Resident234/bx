<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

require_once(__DIR__ . '/geo.php');

class StandardElementListComponent extends CBitrixComponent
{
	/**
	 * кешируемые ключи arResult
	 * @var array()
	 */
	protected $cacheKeys = array();
	
	/**
	 * дополнительные параметры, от которых должен зависеть кеш
	 * @var array
	 */
	protected $cacheAddon = array();
	
	/**
	 * парамтеры постраничной навигации
	 * @var array
	 */
	protected $navParams = array();

    /**
     * вохвращаемые значения
     * @var mixed
     */
	protected $returned;

    /**
     * тегированный кеш
     * @var mixed
     */
    protected $tagCache;
	
	/**
	 * подключает языковые файлы
	 */
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}
	
    /**
     * подготавливает входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = array(
            'IBLOCK_TYPE' => trim($params['IBLOCK_TYPE']),
            'IBLOCK_ID' => intval($params['IBLOCK_ID']),
            'IBLOCK_CODE' => trim($params['IBLOCK_CODE']),
            'SHOW_NAV' => ($params['SHOW_NAV'] == 'Y' ? 'Y' : 'N'),
            'COUNT' => intval($params['COUNT']),
            'SORT_FIELD1' => strlen($params['SORT_FIELD1']) ? $params['SORT_FIELD1'] : 'ID',
            'SORT_DIRECTION1' => $params['SORT_DIRECTION1'] == 'ASC' ? 'ASC' : 'DESC',
            'SORT_FIELD2' => strlen($params['SORT_FIELD2']) ? $params['SORT_FIELD2'] : 'ID',
            'SORT_DIRECTION2' => $params['SORT_DIRECTION2'] == 'ASC' ? 'ASC' : 'DESC',
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
			'AJAX' => $params['AJAX'] == 'N' ? 'N' : $_REQUEST['AJAX'] == 'Y' ? 'Y' : 'N',
			'FILTER' => is_array($params['FILTER']) && sizeof($params['FILTER']) ? $params['FILTER'] : array(),
            'CACHE_TAG_OFF' => $params['CACHE_TAG_OFF'] == 'Y'
        );
        return $result;
    }
	
	/**
	 * определяет читать данные из кеша или нет
	 * @return bool
	 */
	protected function readDataFromCache()
	{
		global $USER;
		if ($this->arParams['CACHE_TYPE'] == 'N')
			return false;

		if (is_array($this->cacheAddon))
			$this->cacheAddon[] = $USER->GetUserGroupArray();
		else
			$this->cacheAddon = array($USER->GetUserGroupArray());

		return !($this->startResultCache(false, $this->cacheAddon, md5(serialize($this->arParams))));
	}

	/**
	 * кеширует ключи массива arResult
	 */
	protected function putDataToCache()
	{
		if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
		{
			$this->SetResultCacheKeys($this->cacheKeys);
		}
	}

	/**
	 * прерывает кеширование
	 */
	protected function abortDataCache()
	{
		$this->AbortResultCache();
	}

    /**
     * завершает кеширование
     * @return bool
     */
    protected function endCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N')
            return false;

        $this->endResultCache();
    }
	
	/**
	 * проверяет подключение необходиимых модулей
	 * @throws LoaderException
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('iblock'))
			throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));

        if (!Main\Loader::includeModule('main'))
            throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));

        if (!Main\Loader::includeModule('highloadblock'))
            throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));

        if (!Main\Loader::includeModule('sale'))
            throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));
	}
	
	/**
	 * проверяет заполнение обязательных параметров
	 * @throws SystemException
	 */
	protected function checkParams()
	{
		if ($this->arParams['IBLOCK_ID'] <= 0 && strlen($this->arParams['IBLOCK_CODE']) <= 0)
			throw new Main\ArgumentNullException('IBLOCK_ID');
	}
	
	/**
	 * выполяет действия перед кешированием 
	 */
	protected function executeProlog()
	{
		if ($this->arParams['COUNT'] > 0)
		{
			if ($this->arParams['SHOW_NAV'] == 'Y')
			{
				\CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');
				$this->navParams = array(
					'nPageSize' => $this->arParams['COUNT']
				);
	    		$arNavigation = \CDBResult::GetNavParams($this->navParams);
				$this->cacheAddon = array($arNavigation);
			}
			else
			{
				$this->navParams = array(	
					'nTopCount' => $this->arParams['COUNT']
				);
			}
		}
		else
			$this->navParams = false;
	}

    /**
     * Определяет ID инфоблока по коду, если не был задан
     */
	protected function getIblockId()
    {
        if ($this->arParams['IBLOCK_ID'] <= 0)
        {
            if (class_exists('Settings'))
            {
                $this->arParams['IBLOCK_ID'] = \SiteSettings::getInstance()->getIblockId($this->arParams['IBLOCK_CODE']);
                if ($this->arParams['IBLOCK_ID'] && $this->arParams['CACHE_TAG_OFF'])
                    \CIBlock::disableTagCache($this->arParams['IBLOCK_ID']);
            }
        }

        if ($this->arParams['IBLOCK_ID'] <= 0)
        {
            $sort = array(
                'id' => 'asc'
            );
            $filter = array(
                'TYPE' => $this->arParams['IBLOCK_TYPE'],
                'CODE' => $this->arParams['IBLOCK_CODE']
            );
            $iterator = \CIBlock::GetList($sort, $filter);
            if ($iblock = $iterator->GetNext())
                $this->arParams['IBLOCK_ID'] = $iblock['ID'];
            else
            {
                $this->abortDataCache();
                throw new Main\ArgumentNullException('IBLOCK_ID');
            }
        }
        $this->arResult['IBLOCK_ID'] = $this->arParams['IBLOCK_ID'];
        $this->cacheKeys[] = 'IBLOCK_ID';
    }

	/**
	 * получение текущего местоположения пользоввателя
	 */
	protected function getCurrentLocation()
	{
        global $APPLICATION;

        //if (!$_SESSION['GEO']['city']) {
        //if ($APPLICATION->get_cookie('city')) {
        //    $_SESSION['GEO']['city'] = $APPLICATION->get_cookie('city');
        //} else {
        $geo = new Geo();
        $geo_data = $geo->get_geobase_data();

        $_SESSION['GEO']['city'] = $geo_data['city'];
        $APPLICATION->set_cookie("city", $geo_data['city']);

        $arCurrentLocationFormatted = array();
        $arCurrentCoordinates = array();
        if (!empty($geo_data['country'])) $arCurrentLocationFormatted[] = $geo_data['country'];
        if (!empty($geo_data['district'])) $arCurrentLocationFormatted[] = $geo_data['district'];
        if (!empty($geo_data['region'])) $arCurrentLocationFormatted[] = $geo_data['region'];
        if (!empty($geo_data['city'])) $arCurrentLocationFormatted[] = $geo_data['city'];

        if (!empty($geo_data['lat'])) $arCurrentCoordinates[] = $geo_data['lat'];
        if (!empty($geo_data['lng'])) $arCurrentCoordinates[] = $geo_data['lng'];

        if (!empty($arCurrentCoordinates)) $arCurrentLocationFormatted[] = implode(" - ", $arCurrentCoordinates);

        $this->arResult['CURRENT_LOCATION_FORMATTED'] = implode(", ", $arCurrentLocationFormatted);
        $this->arResult['CURRENT_CITY_HASH'] = md5(implode("_", array($geo_data['district'], $geo_data['region'], $geo_data['city'])));

        //}
        //}

	}

    /**
     * получение id текущего пользователя
     */
    protected function getCurrentUserData()
    {
        global $USER;
        $arUserInfo = array();

        if ($USER->IsAuthorized()){
            $currentUserID = $USER->GetID();// $USER->GetLogin() $USER->GetFullName()
            $arUserInfo["ID"] = $currentUserID;
            $arUserInfo["LOGIN"] = $USER->GetLogin();
            $arUserInfo["NAME"] = $USER->GetFirstName();
            $arUserInfo["LAST_NAME"] = $USER->GetLastName();
        }else{
            $currentFUserID = CSaleBasket::GetBasketUserID(); //FUSER_ID , и это не одно и тоже, что USER_ID

            $res = Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("ID", "UF_*", "NAME", "LAST_NAME", "LOGIN"),
                "filter"=>Array("=UF_FUSER_ID" => $currentFUserID),
            ));
            if ($arUser = $res->fetch()) {
                $currentUserID = $arUser['ID'];
                $arUserInfo["ID"] = $currentUserID;
                $arUserInfo["LOGIN"] = $arUser['LOGIN'];
                $arUserInfo["NAME"] = $arUser['NAME'];
                $arUserInfo["LAST_NAME"] = $arUser['LAST_NAME'];

            }else{
                $user = new CUser;
                $intRandUserNumber = rand(0, PHP_INT_MAX);
                $intRandUserPassword = rand(0, PHP_INT_MAX);

                $arFields = Array(
                    "NAME" => "User" . $intRandUserNumber,
                    "LAST_NAME" => "Anonymous" . $intRandUserNumber,
                    "EMAIL" => "Anonymous" . $intRandUserNumber . "@mail.ru",
                    "LOGIN" => "login" . $intRandUserNumber,
                    "LID" => "ru",
                    "ACTIVE" => "Y",
                    "GROUP_ID" => array(3),
                    "PASSWORD" => $intRandUserPassword,
                    "CONFIRM_PASSWORD" => $intRandUserPassword,
                    "UF_FUSER_ID" => $currentFUserID

                );

                $arUserInfo["LOGIN"] = $arFields['LOGIN'];
                $arUserInfo["NAME"] = $arFields['NAME'];
                $arUserInfo["LAST_NAME"] = $arFields['LAST_NAME'];

                $currentUserID = $user->Add($arFields);
                $arUserInfo["ID"] = $currentUserID;
            }
        }

        return $arUserInfo;

    }

    /**
     * добавление данных о текущем пользователе в HL влок
     */
    protected function addUserDataToHLblock(){
        $arCurrentUserData = $this->getCurrentUserData();

        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(3)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        $rsData = $strEntityDataClass::getList(array(
            'select' => array("ID", 'UF_ID'),
            'order' => array('ID' => 'ASC'),
            'limit' => '1',
            'filter' => array('UF_ID' => $arCurrentUserData["ID"]),
        ));
        if ($arData = $rsData->Fetch()) {
            //обновить LAST_LOCATION_F
            $data = array(
                "UF_LAST_LOCATION_F" => $this->arResult['CURRENT_LOCATION_FORMATTED'],
                "UF_CITY_HASH" => $this->arResult['CURRENT_CITY_HASH']
            );
            $strEntityDataClass::update($arData["ID"], $data);
        }else{
            //добавляем пользователя
            $data = array(
                "UF_ID"=> $arCurrentUserData["ID"],
                "UF_LOGIN"=> $arCurrentUserData["LOGIN"],
                "UF_NAME"=> $arCurrentUserData["NAME"],
                "UF_LAST_NAME"=> $arCurrentUserData["LAST_NAME"],
                "UF_LAST_LOCATION_F" => $this->arResult['CURRENT_LOCATION_FORMATTED'],
                "UF_CITY_HASH" => $this->arResult['CURRENT_CITY_HASH']
            );
            $strEntityDataClass::add($data);
        }
    }


    /**
     * получение пользователей из текущего города
     */
    protected function getUsersFromCurrentCity()
    {
        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(3)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        $rsData = $strEntityDataClass::getList(array(
            'select' => array("ID", "UF_ID", "UF_LOGIN", "UF_NAME", "UF_LAST_NAME", "UF_LAST_LOCATION_F"),
            'order' => array('ID' => 'ASC'),
            //'limit' => '1',
            'filter' => array('UF_CITY_HASH' => $this->arResult['CURRENT_CITY_HASH']),
        ));

        $currentUserData = $this->getCurrentUserData();


        $arUsersFromCurrentCityList = array();
        while ($arData = $rsData->Fetch()) {
            if($currentUserData["ID"] == $arData["UF_ID"]) continue;
            $arUser = array();
            if(!empty($arData["UF_LOGIN"])) $arUser[] = "<b>Логин:</b> " . $arData["UF_LOGIN"] . "<br>";
            if(!empty($arData["UF_NAME"])) $arUser[] = "<b>Имя:</b> " . $arData["UF_NAME"] . "<br>";
            if(!empty($arData["UF_LAST_NAME"])) $arUser[] = "<b>Фамилия:</b> " . $arData["UF_LAST_NAME"] . "<br>";
            if(!empty($arData["UF_LAST_LOCATION_F"])) $arUser[] = "<b>Последнее местоположение:</b> " . $arData["UF_LAST_LOCATION_F"] . "<br><hr>";
            $strUserInfo = implode("", $arUser);
            $arUsersFromCurrentCityList[] = $strUserInfo;
        }

        $strUsersFromCurrentCityList = implode("", $arUsersFromCurrentCityList);
        $this->arResult['UsersFromCurrentCityList'] = $strUsersFromCurrentCityList;


    }

	/**
	 * выполняет действия после выполения компонента, например установка заголовков из кеша
	 */
	protected function executeEpilog()
	{
		if ($this->arResult['IBLOCK_ID'] && $this->arParams['CACHE_TAG_OFF'])
            \CIBlock::enableTagCache($this->arResult['IBLOCK_ID']);
	}
	
	/**
	 * выполняет логику работы компонента
	 */
	public function executeComponent()
	{
		global $APPLICATION;
		try
		{
			$this->checkModules();
			$this->checkParams();
			$this->executeProlog();

			if ($this->arParams['AJAX'] == 'Y')
				//$APPLICATION->RestartBuffer();
			if (!$this->readDataFromCache())
			{
			    $this->getIblockId();
                $this->getHLEntityDataClass();
				$this->getCurrentLocation();
                $this->addUserDataToHLblock();
                $this->getUsersFromCurrentCity();
                $this->putDataToCache();
				$this->includeComponentTemplate();
			}
			$this->executeEpilog();

			if ($this->arParams['AJAX'] == 'Y')
				//die();

			return $this->returned;
		}
		catch (Exception $e)
		{
			$this->abortDataCache();
			ShowError($e->getMessage());
		}
	}
}
?>