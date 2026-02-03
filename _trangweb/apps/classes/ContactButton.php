<?php
namespace classes;
use models\Posts;
use models\Users;
use models\PostsComments;
use models\CashFlow;
use Storage, Assets, PageOption, Route;
/*
# Nút treo bên dưới trang
*/
class ContactButton{
	public static function show(){
		$out="<nav>";
		$nav = Storage::setting('contact_button', []);
		if( Route::folder() != "lien-he" && !empty( Storage::option('contact_contact_form') ) && empty( user('id') ) ){
			$nav[] = [
				"link"       => 'javascript: showContactForm()',
				"title"      => "Yêu cầu gọi lại",
				"icon"       => "/files/uploads/2019/08/send-icon.png",
				"count"      => 0,
				"background" => "#995aca",
			];
			$contactForm = '
				<section class="main-layout modal-form pd-20" id="contact-wrap">
					<form method="POST" id="contact-form">
						<div class="center" style="padding-bottom: 10px; color: #0B9BB5; line-height: 1.6">
							Quý khách vui lòng để lại thông tin
							<br>
							Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất, xin cảm ơn.
						</div>
						<div class="contact-input modal-input">
							<i class="fa fa-user"></i>
							<input type="text" class="input width-100" name="contact[name]" placeholder="Họ và tên">
						</div>
						<div class="contact-input modal-input">
							<i class="fa fa-phone"></i>
							<input type="text" class="input width-100" name="contact[phone]" placeholder="Số điện thoại">
						</div>
						<div class="contact-input">
							<textarea name="contact[message]" class="width-100" placeholder="'.__('Quý khách đang muốn thiết kế website về lĩnh vực gì?').'" rows="5"></textarea>
						</div>
						<div class="contact-notify alert-danger center hidden">
							
						</div>
						<div class="contact-input center">
							<button type="button" class="btn btn-primary" style="font-size: 16px; padding: 15px 35px; border-radius: 40px !important">'.__('ĐỒNG Ý').'</button>
						</div>
					</form>
				</section>
			';
			Assets::footer(modalForm("contact-form", __('GỬI YÊU CẦU THIẾT KẾ WEBISTE'), $contactForm, '570px', false, true));
		}
		
		//Tạo menu
		$count=$countNav=0;
		foreach($nav??[] as $item){
			$modalID='modal-button-nav-'.$countNav;
			if( empty($item["hidden"]) ){
				$out.='
				<a data-modal="'.$modalID.'" '.( empty($item["newTab"]) ? '' : 'target="_blank"').' class="'.(isset($item["modal"]) ? 'modal-click' : '').'" href="'.$item["link"].'" style="background-color: '.$item['background'].'">
					'.(strpos($item['icon'], "/") === false ? '
						<i style="min-width: 17px" class="fa '.$item['icon'].'"></i>
					' : '
						<img src="'.$item['icon'].'">
					').'
					<span style="background-color: '.$item['background'].'"> '.$item['title'].'</span>
				</a>
				';
			}
			$count = 0;
			$countNav++;
		}
		$out.='</nav>';
		$out.=($count>0 ? "<span>$count</span>" : "");
		if( !permission("member") ){
			$out .= '
			<style>
				@media (max-width: 768px){
					.contact-button{
						bottom: 20px
					}
				}
			</style>
			';
		}
		//Hiện nội dung
		if($countNav>0){
			return '
				<div class="contact-button-wrap hidden"></div>
				<div class="contact-button" id="contact-button-bottom">
					'.$out.'
					<a class="pulsing-button">
						<span>
							<img src="/files/uploads/2019/08/messenger-icon.png"  class="pulsing-button-active">
						</span>
						<i class="fa fa-times" style="display: none"></i>
					</a>

				</div>
				'.Assets::show("/assets/general/js/contact-button.js").'
			';
		}
	}
}