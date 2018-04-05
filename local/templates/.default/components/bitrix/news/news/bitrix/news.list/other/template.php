<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="news-slider">
    <div class="news-slider__container container">
        <h2 class="news-slider__title">
            <span class="news-slider__title-span">другие</span>
            новости
        </h2>

        <div class="news-slider__list swiper-container" id="news-slider">
            <div class="swiper-wrapper">
                <? foreach ($arResult["ITEMS"] as $arItem): ?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

                    $img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => 375, 'height' => 370), BX_RESIZE_IMAGE_EXACT);
                    ?>
                    <a class="swiper-slide display-block" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                        <div class="news-item" style="background-image: url(<?=$img['src']?>)">
                            <div class="news-item__content-wrap">
                                <div class="news-item__text-wrap">
                                    <h4 class="news-item__title"><?=$arItem['NAME']?></h4>
                                    <h5 class="news-item__subtitle"><?=$arItem['PROPERTIES']['SUBTITLE']['VALUE']?></h5>
                                    <p class="news-item__date"><?=strtolower($arItem['DISPLAY_ACTIVE_FROM'])?></p>
                                    <p class="news-item__desc js-news-slider-desc">
                                        <?=$arItem['~PREVIEW_TEXT']?>
                                    </p>
                                </div>
                                <button class="news-item__button">подробнее</button>
                            </div>
                        </div>
                    </a>
                <? endforeach; ?>
            </div>
        </div>
        <button class="news-slider__button-prev dark-arrow-style"></button>
        <button class="news-slider__button-next dark-arrow-style"></button>
    </div>
</div>




