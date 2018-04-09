<?

AddEventHandler("main", "OnBeforeProlog", "getCurrentLocation");
function getCurrentLocation()
{

    if (strpos($_SERVER['REQUEST_URI'], '/bitrix/') === 0) return;

    $arLocationDefault = array('CURRENT_CITY_HASH' => md5(
        implode("_", array(
            "Центральный федеральный округ", "Москва", "Москва"
        ))),
        'CURRENT_LOCATION_FORMATTED' => "RU, Центральный федеральный округ, Москва, Москва, 55.755787 - 37.617634",
    );

    global $APPLICATION;

    if (!$_SESSION['GEO']['CURRENT_CITY_HASH'] || !$_SESSION['GEO']['CURRENT_LOCATION_FORMATTED']) {
        if ($APPLICATION->get_cookie('CURRENT_CITY_HASH') &&
            $APPLICATION->get_cookie('CURRENT_LOCATION_FORMATTED')) {

            $_SESSION['GEO']['CURRENT_CITY_HASH'] = $APPLICATION->get_cookie('CURRENT_CITY_HASH');
            $_SESSION['GEO']['CURRENT_LOCATION_FORMATTED'] = $APPLICATION->get_cookie('CURRENT_LOCATION_FORMATTED');

        } else {
            $geo = new geoglobal();
            $geo_data = $geo->get_geobase_data();

            $arCurrentLocationFormatted = array();
            $arCurrentCoordinates = array();

            if (!empty($geo_data['country'])) $arCurrentLocationFormatted[] = $geo_data['country'];
            if (!empty($geo_data['district'])) $arCurrentLocationFormatted[] = $geo_data['district'];
            if (!empty($geo_data['region'])) $arCurrentLocationFormatted[] = $geo_data['region'];
            if (!empty($geo_data['city'])) $arCurrentLocationFormatted[] = $geo_data['city'];

            if (!empty($geo_data['lat'])) $arCurrentCoordinates[] = $geo_data['lat'];
            if (!empty($geo_data['lng'])) $arCurrentCoordinates[] = $geo_data['lng'];

            if (!empty($arCurrentCoordinates)) $arCurrentLocationFormatted[] = implode(" - ", $arCurrentCoordinates);

            $CURRENT_CITY_HASH = md5(implode("_", array($geo_data['district'], $geo_data['region'], $geo_data['city'])));
            $CURRENT_LOCATION_FORMATTED = implode(", ", $arCurrentLocationFormatted);

            if(empty($CURRENT_CITY_HASH)) $CURRENT_CITY_HASH = $arLocationDefault['CURRENT_CITY_HASH'];
            if(empty($CURRENT_LOCATION_FORMATTED)) $CURRENT_LOCATION_FORMATTED = $arLocationDefault['CURRENT_LOCATION_FORMATTED'];

            $_SESSION['GEO']['CURRENT_CITY_HASH'] = $CURRENT_CITY_HASH;
            $_SESSION['GEO']['CURRENT_LOCATION_FORMATTED'] = $CURRENT_LOCATION_FORMATTED;

            $APPLICATION->set_cookie("CURRENT_CITY_HASH", $CURRENT_CITY_HASH);
            $APPLICATION->set_cookie("CURRENT_LOCATION_FORMATTED", $CURRENT_LOCATION_FORMATTED);

        }
    }

}
