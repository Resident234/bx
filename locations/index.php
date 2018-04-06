<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->IncludeComponent(
    "custom:locations",
    "",
    array(
        'IBLOCK_TYPE' => "news",
        'IBLOCK_ID' => 1,
        'IBLOCK_CODE' => "",
        'SHOW_NAV' => 'N',
        'COUNT' => 10,
        'SORT_FIELD1' => 'ID',
        'SORT_DIRECTION1' => 'ASC',
        'SORT_FIELD2' => 'ID',
        'SORT_DIRECTION2' => 'ASC',
        'CACHE_TIME' => 3600,
        'AJAX' => 'N',
        'FILTER' => array(),
        'CACHE_TAG_OFF' => 'N'
    ),
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>