<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>
<?
if($arResult["NavPageCount"] > 1)
{
	$bFirst = true;

	if ($arResult["NavPageNomer"] > 1):
		if ($arResult["nStartPage"] > 1):
			$bFirst = false;
			if($arResult["bSavePage"]):
?>
            <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1" class="search-page__pagination-item">1</a>
<?
			else:
?>
            <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="search-page__pagination-item search-page__pagination-item_active">1</a>
<?
			endif;
			if ($arResult["nStartPage"] > 2):
?>
			<a class="search-page__pagination-item">...</a>
<?
			endif;
		endif;
	endif;

	do
	{
		if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
?>
        <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" class="search-page__pagination-item search-page__pagination-item_active"><?=$arResult["nStartPage"]?></a>
<?
		elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
?>
        <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="search-page__pagination-item"><?=$arResult["nStartPage"]?></a>
<?
		else:
?>
        <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" class="search-page__pagination-item"><?=$arResult["nStartPage"]?></a>
<?
		endif;

		$arResult["nStartPage"]++;
		$bFirst = false;
	} while($arResult["nStartPage"] <= $arResult["nEndPage"]);

	if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
		if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
			if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
?>
                <a class="search-page__pagination-item">...</a>
<?
			endif;
?>
        <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>" class="search-page__pagination-item"><?=$arResult["NavPageCount"]?></a>
<?
		endif;
	endif;
}
?>