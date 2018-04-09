<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заполнение формы");
?>

	<?$APPLICATION->IncludeComponent(
		"aspro:form.stroy", "contacts",
		Array(
			"IBLOCK_TYPE" => "aspro_stroy_form",
			"IBLOCK_ID" => 4,
			"USE_CAPTCHA" => "N",
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_JUMP" => "Y",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "100000",
			"AJAX_OPTION_ADDITIONAL" => "",
			//"IS_PLACEHOLDER" => "Y",
			"SUCCESS_MESSAGE" => "Форма успешно отправлена",
			"SEND_BUTTON_NAME" => "Отправить",
			"SEND_BUTTON_CLASS" => "btn btn-default",
			"DISPLAY_CLOSE_BUTTON" => "Y",
			"CLOSE_BUTTON_NAME" => "Закрыть",
			"CLOSE_BUTTON_CLASS" => "jqmClose btn btn-default bottom-close",
			"DEFAULT_VALUES" => array("CURRENT_LOCATION_FORMATTED" => $_SESSION['GEO']['CURRENT_LOCATION_FORMATTED']),
            "HIDDEN_VALUES" => array("CURRENT_LOCATION_FORMATTED"),

        )
	);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>