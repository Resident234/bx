<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

if($arResult["NavPageCount"] > 1)
{
?>
<div class="news-wrap__button-wrap news-wrap__button-show-more">
<?
	$bFirst = true;

	if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
?>
    <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" class="btn ajax-link btn_green-border-center">Показать ещё</a>
<?
	endif;
?>
</div>
<?
}
?>