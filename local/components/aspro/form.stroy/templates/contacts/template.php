<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<div class="row">
	<div class="styled-block">
		<div class="maxwidth-theme">
			<div class="col-md-12">
				<div class="form contacts<?=($arResult['isFormErrors'] == 'Y' ? ' error' : '')?>">
                    <? if ($arResult["isFormNote"] == "Y" && $arResult['isFormErrors'] == "N") { ?>
                        <div class="form-header">
                            <div class="text margin-bottom__20px js-form-main-message">
                                <?= $arResult["FORM_NOTE"] ?>
                            </div>
                        </div>
                    <? } ?>
						<?=$arResult["FORM_HEADER"]?>
							<div class="row" >
								<div class="col-md-12 col-sm-12">
									<div class="row">
										<?if($arResult['isFormErrors'] == 'Y'):?>
											<div class="col-md-12">
												<div class="form-error alert alert-danger">
													<?=$arResult['FORM_ERRORS_TEXT']?>
												</div>
											</div>
										<?endif;?>
										<div class="col-md-6 col-sm-6">
											<?if(is_array($arResult["QUESTIONS"])):?>
												<?foreach( $arResult["QUESTIONS"] as $FIELD_SID => $arQuestion ){
													if( $FIELD_SID == "MESSAGE" ) continue;
													if( $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden' ){
														echo $arQuestion["HTML_CODE"];
													}else{?>
														<div class="row margin-top__20px margin-bottom__20px" data-SID="<?=$FIELD_SID?>">
															<div class="form-group">
																<div class="col-md-12">
																	<?=$arQuestion["CAPTION"]?>
																	<div class="input">
																		<?=$arQuestion["HTML_CODE"]?>
																	</div>
																	<?if( !empty( $arQuestion["HINT"] ) ){?>
																		<div class="hint"><?=$arQuestion["HINT"]?></div>
																	<?}?>
																</div>
															</div>
														</div>
													<?}
												}?>
											<?endif;?>
										</div>
										<?if($arResult["QUESTIONS"]["MESSAGE"]):?>
											<div class="col-md-6 col-sm-6">
												<div class="row margin-top__20px margin-bottom__20px" data-SID="MESSAGE">
													<div class="form-group">
														<div class="col-md-12">
															<?=$arResult["QUESTIONS"]["MESSAGE"]["CAPTION"]?>
															<div class="input">
																<?=$arResult["QUESTIONS"]["MESSAGE"]["HTML_CODE"]?>
															</div>
															<?if( !empty( $arResult["QUESTIONS"]["MESSAGE"]["HINT"] ) ){?>
																<div class="hint"><?=$arResult["QUESTIONS"]["MESSAGE"]["HINT"]?></div>
															<?}?>
														</div>
													</div>
												</div>
											</div>
										<?endif;?>
									</div>
									<?
									$frame = $this->createFrame()->begin('');
									$frame->setBrowserStorage(true);
									?>
									<?if( $arResult["isUseCaptcha"] == "Y" ){?>
										<div class="row captcha-row">
											<div class="col-md-7 col-sm-7 col-xs-7">
												<div class="form-group">
													<?=$arResult["CAPTCHA_CAPTION"]?>
													<div>
														<?=$arResult["CAPTCHA_IMAGE"]?>
														<span class="refresh"><a href="javascript:;" rel="nofollow"><?=GetMessage("REFRESH")?></a></span>
													</div>
												</div>
											</div>
											<div class="col-md-5 col-sm-5 col-xs-5">
												<div class="form-group" style="margin-top:25px;">
													<div class="input <?=$arResult["CAPTCHA_ERROR"] == "Y" ? "error" : ""?>">
														<?=$arResult["CAPTCHA_FIELD"]?>
													</div>
												</div>
											</div>
										</div>
									<?}else{?>
										<div style="display:none;"></div>
									<?}?>
									<?$frame->end();?>
									<div class="row">
										<div class="col-md-12 col-sm-12 pull-right" style="margin-top: 5px;">
											<div class="pull-left required-fileds">
												<i class="star">*</i><?=GetMessage("FORM_REQUIRED_FILEDS")?>
											</div>
											<div class="pull-right">
												<?=str_replace('class="', 'class="btn-lg ', $arResult["SUBMIT_BUTTON"])?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?=$arResult["FORM_FOOTER"]?>

				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){

        $('form input[name="PHONE"]').inputmask("mask", { "mask": "(999) 999-9999" });
        $('form input[name="PHONE"]').blur(function(){
            if( $(this).val() == base_mask || $(this).val() == '' ){
                if( $(this).hasClass('required') ){
                    $(this).parent().find('div.error').html(BX.message("JS_REQUIRED"));
                }
            }
        });

        $('form').validate({
			highlight: function( element ){
				$(element).parent().addClass('error');
			},
			unhighlight: function( element ){
				$(element).parent().removeClass('error');
			},
			submitHandler: function( form ){
                $(".js-form-main-message").html("");
				if( $('.contacts form[name="<?=$arResult["IBLOCK_CODE"]?>"]').valid() ){
					$(form).find('button[type="submit"]').attr("disabled", "disabled");
					form.submit();
				}
			},
			errorPlacement: function( error, element ){
				error.insertBefore(element);
			}
		});


	});
</script>