@extends("Default")
@php
    define("PAGE", [
        "name"        =>"Đăng ký tên miền giá rẻ",
        "title"       =>"Đăng ký tên miền giá rẻ",
        "description" =>"",
        "loading"     =>0,
        "background"  =>"",
        "image"       =>"",
        "canonical"   =>"",
        "robots"      =>"index,follow"
    ]);
@endphp


@section("main")
    @parent
    <section class="main-layout">
		<?php
		$data = \models\InetDomainSuffix::get_suffix();
		?>

        <main class="flex flex-large" style="margin-top: 30px">
            <section class="width-70 flex-margin">
                <section class="section">
                    <div class="section-heading">
                        <i class="bx bx-pencil"></i>
                        ĐĂNG KÝ TÊN MIỀN
                    </div>
                    <div class="section-body" style="padding-bottom: 30px">
                        <div id="check-domain-form">
                            <div class="check-domain-input">
                                <span class="primary-color">WWW.</span>
                                <input class="input width-100" id="input-check-domain-name" onkeyup="register_domain_scripts.enter_keyboard(event)" type="text" name="domain"
                                       style="font-size: 15px"
                                       placeholder="Nhập tên miền bạn muốn đăng ký, có thể ấn enter để kiểm tra"
                                       value="{{ ($_GET['domain'] ?? '') }}"
                                >
                                <div>
                                    <button type="button"
                                            class="btn btn-gradient"
                                            onclick="register_domain_scripts.submit()" value="Kiểm tra"
                                            style="font-size: 15px">
                                        KIỂM TRA
                                    </button>
                                </div>
                            </div>
                            <div id="check-domain-msg" class="alert alert-danger">
                            </div>
                            <div class="domain-check-list">

                            </div>
                            <div id="domain-check-list-more">

                            </div>
                        </div>
                    </div>
                </section>
            </section>
            <section class="width-30 flex-margin">
				<?php  $icon = 'fa fa-times'; ?>
                @include(APPS_ROOT.'/pages/admin/views/panel/includes/Cart.php')
            </section>
            <style></style>
        </main>
        <div class="modal hidden modal-allow-close modal-allow-scroll" id="whois-modal">
            <div class="modal-body" style="max-width: 450px">
                <div class="modal-content">
                    <div class="heading modal-heading">
                        <span>
                            Thông tin tên miền
                        </span>
                        <i class="modal-close link fa"></i>
                    </div>
                    <div>
                        <div class="bg pd-10" id="whois-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <main class="flex flex-large" style="margin-top: 50px">
            @foreach(['vn' => 'Bảng giá tên miền Việt Nam', 'global' => 'Bảng giá tên miền quốc tế'] as $type => $name)
                <section class="width-50 flex-margin">
                    <section class="section">
                        <div class="section-heading">
                            {{ $name }}
                        </div>
                        <div class="section-body">
                            <table class="table table-border table-hover width-100 table-pricing">
                                <thead>
                                    <tr>
                                        <th  style="background-image: linear-gradient(-45deg, #f0b50f,#bb108e); width: 120px">
                                            Đuôi
                                        </th>
                                        <th style="background-image: linear-gradient(-45deg,#f00f8f,#1094bb);">
                                            Chi phí năm đầu
                                        </th>
                                        <th style="background-image: linear-gradient(-45deg,#c3f00f,#1094bb);">
                                            Chi phí gia hạn
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                        @if( !$item->is_vn &&  $type == 'vn' || $item->is_vn &&  $type == 'global' )
                                            @continue
                                        @endif
                                        <tr>
                                            <td style="width: 120px">
                                                <b>
                                                    {{ $item->suffix }}
                                                </b>
                                            </td>
                                            <td>
                                                @if( $item->reg_price_origin > $item->reg_price )
                                                    <s style="color: gray; font-size: small">{{ number_format($item->reg_price_origin) }}</s>
                                                    {{ number_format($item->reg_price) }} ₫
                                                @else
                                                    {{ number_format($item->reg_price) }} ₫
                                                @endif
                                            </td>
                                            <td>
                                                @if( $item->renew_price_origin > $item->renew_price )
                                                    <s style="color: gray; font-size: small">{{ number_format($item->renew_price_origin) }}</s>
                                                    {{ number_format($item->renew_price) }} ₫
                                                @else
                                                    {{ number_format($item->renew_price) }} ₫
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </section>
            @endforeach
        </main>
        <style>
            .input-check-domain > input{
                padding: 15px 20px;
            }
            .input-check-domain > .input{
                border-radius: 30px 0 0 30px
            }
            .input-check-domain > .btn-primary{
                border-radius: 0 30px 30px 0
            }
            .domain-check-list {
                margin-top: 20px;
                border: 1px solid #EAEAEA;
                border-bottom: none !important;
                background: white;
            }
            .domain-check-item {
                border-bottom: 1px solid #EAEAEA;
            }
            .domain-check-item:hover {
                background: #e9f2fa;
            }
            .domain-check-item > div {
                padding: 10px 5px;
            }
            .domain-check-price > s{
                font-size: 12px;
                color: gray;
            }
            .domain-check-price > span{
                color: var(--primary-color);
                font-size: 16px;
                font-weight: bold;
                margin-left: 5px;
            }
            .domain-check-price > i{
                font-size: 13px;
                color: #a34331;
            }
            .btn-whois{
                background-color: #CFCFCF !important;
                border: 1px solid #CFCFCF !important;
                color: #525151 !important;
            }
            .btn-whois:hover{
                background-color: #bdb7b7 !important;
                border: 1px solid #CFCFCF !important;
            }
            .domain-check-loading {
                font-size: 25px;
            }
            #check-domain-msg {
                margin: 20px 0;
                display: none;
            }
            @media (min-width: 767px) {
                .input-check-domain > .input{
                    width: 320px;
                    box-shadow: none !important;
                }
            }
            .domain-ext-list:hover{
                background: #EAEAEA
            }
            .domain-price-heading{
                padding: 20px;
                color: white;
                font-size: 25px;
                text-align: center
            }
            .domain-ext-list>div{
                padding: 20px
            }
            .domain-ext-list:nth-child(odd){
                background: #eeeeee
            }
            .check-domain-input{
                position: relative;
                overflow: hidden
            }
            .check-domain-input input,
            .check-domain-input button{
                padding: 15px;
                font-size: 20px;
            }
            .check-domain-input>div{
                position: absolute;
                right: 0;
                top: 0;
                height: 100%;
            }
            .check-domain-input input{
                padding-left: 55px;
                border: 1px solid {{ Storage::setting("theme__form_gradient_background1") }};
            }
            .check-domain-input button{
                right: 0;
                height: 100%;
                top: 0;
                background: {{ Storage::setting("theme__form_gradient_background1") }}
    }
            .check-domain-input button:before{
                position: absolute;
                content: "";
                top: 0;
                height: 100%;
                left: -29px;
                border-right: 30px solid {{ Storage::setting("theme__form_gradient_background1") }};
                border-bottom: 0px solid transparent;
                border-top: 50px solid transparent;
                border-left: 0 solid transparent;
            }
            .check-domain-input span{
                position: absolute;
                font-weight: bold;
                left: 5px;
                top: 50%;
                transform: translate(0, -50%);
            }
            #check-domain-form .primary-color{
                color: {{ Storage::setting("theme__form_gradient_background1") }} !important
            }
            #domain-check-list-more {
                margin-top: 10px;
            }
            .domain-check-item > div:nth-child(2){
                width: calc(100% - 40% - 120px) !important;
            }
            .domain-check-action{
                padding-right: 15px !important;
                width: 120px !important;
            }
            @media (max-width: 768px){
                .domain-price-heading{
                    font-size: 15px;
                    padding: 10px 5px
                }
                #check-domain-form > .flex > div {
                    width: 100% !important;
                }
                #check-domain-form > .flex > div:last-child {
                    display: none;
                }
            }
            .table-pricing th {
                color: white;
            }

        </style>
        <script type="text/javascript">
            var suffix_list = @json( array_map('trim', explode(PHP_EOL, Storage::setting('builder_inet_domain_check_default') ) ) );
            var suffix_more_list = @json( array_map('trim', explode(PHP_EOL, Storage::setting('builder_inet_domain_check_more') ) ) );
            var domain_data = @json( $data );
            const register_domain_scripts = {
                /*
                * Ấn nút kiểm tra tên miền
                * */
                submit: () => {
                    $('#check-domain-msg').hide();
                    $('.domain-check-list').html('');
                    var domain = $('#input-check-domain-name').val();
                    if( domain.length == 0 ){
                        return $('#check-domain-msg').html('Vui lòng nhập tên miền').show();
                    }
                    var domain_split = domain.split('.');
                    var domain_name = removeAccents( domain_split[0] ).replace(/ /g, '').toLowerCase();
                    delete domain_split[0];
                    var domain_suffix = domain_split.join('.').substring(1);
                    delete suffix_list_new;
                    var suffix_list_new = suffix_list.slice();
                    var domain_name_fix = domain_name;
                    if( domain_suffix.length > 0 ){
                        var index = suffix_list_new.indexOf(domain_suffix);
                        if (index !== -1) {
                            suffix_list_new.splice(index, 1);
                        }
                        suffix_list_new.unshift(domain_suffix);
                        domain_name_fix = domain_name+'.'+domain_suffix;
                    }
                    $('#input-check-domain-name').val( domain_name_fix );
                    var content = '';
                    $.each(suffix_list_new, function (i, suffix) {
                        content += `
                    <div class="flex flex-middle domain-check-item domain-check-item-checking" data-domain="${domain_name}.${suffix}">
                        <div style="width: 40%; font-size: 20px; font-weight: bold; text-align: left; padding-left: 15px">
                            <span class="domain-check-name">${domain_name}.</span><span class="domain-check-suffix">${suffix}</span>
                            <input type="hidden" name="domain" value="${domain_name}.${suffix}">
                        </div>
                        <div style="width: 40%" class="right domain-check-price">
                        </div>
                        <div style="width: 20%" class="center domain-check-action">
                            <i class="bx bx-loader bx-spin domain-check-loading"></i>
                        </div>
                    </div>
                `;
                    });
                    $('.domain-check-list').html(content);

                    var check_more = '';
                    $.each(suffix_more_list, function (i, suffix) {
                        check_more += `
                    <div class="flex flex-middle domain-check-item" data-domain="${domain_name}.${suffix}">
                        <div style="width: 40%; font-size: 20px; font-weight: bold; text-align: left; padding-left: 15px">
                            <span class="domain-check-name">${domain_name}.</span><span class="domain-check-suffix">${suffix}</span>
                            <input type="hidden" name="domain" value="${domain_name}.${suffix}">
                        </div>
                        <div style="width: 40%" class="right domain-check-price">
                        </div>
                        <div style="width: 20%" class="center domain-check-action">
                            <button class="btn-primary btn-sm" type="button" onclick="register_domain_scripts.check_more(this)">
                                <i class="bx bx-search"></i>
                                Kiểm tra
                            </button>
                        </div>
                    </div>
                `;
                    });
                    $('#domain-check-list-more').html(`
                <div class="panel panel-default">
                    <div class="heading link">Kiểm tra thêm các đuôi khác</div>
                    <div class="panel-body hidden" style="display: none; padding: 0">${check_more}</div>
                </div>
            `);
                    panelClickInstall();
                    register_domain_scripts.check();
                },

                /*
                * Ấn nút enter
                * */
                enter_keyboard: (even) => {
                    if( even.keyCode == 13 ){
                        register_domain_scripts.submit();
                    }
                },

                /*
                * Tiến hành kiểm tra từng tên miền
                * */
                _checking_domain: false,
                check: () => {
                    var first_domain_el = $('.domain-check-item-checking').eq(0);
                    if( first_domain_el.length == 0 ){
                        register_domain_scripts._checking_domain = false;
                        return;
                    }
                    register_domain_scripts._checking_domain = true;
                    var domain = first_domain_el.find('input[name="domain"]').val();
                    $.ajax({
                        url: '/api/services/check-domain',
                        data: {domain: domain},
                        type: 'GET',
                        dataType: 'JSON',
                        success: (res) => {
                            var domain_suffix = first_domain_el.find('.domain-check-suffix').text();
                            var d_data = domain_data[domain_suffix];
                            console.log(domain_suffix);
                            if( res.message.length > 0 || typeof d_data == 'undefined'){
                                // Có lỗi
                                first_domain_el.find('.domain-check-price').html(`<i>${res.message}</i>`);
                                // Tên miền đã có người đăng ký
                                first_domain_el.find('.domain-check-action').html(`
                            <button class="btn-info btn-whois btn-sm" type="button" onclick="register_domain_scripts.whois('${domain}')">
                                <i class="bx bx-show"></i>
                                Whois
                            </button>
                        `);
                            }else if(res.is_available){
                                // Tên miền có thể đăng ký
                                var domain_is_added_to_cart = $('.cart-domain-item[data-domain="'+domain+'"]').length > 0 ? true : false;
                                first_domain_el.find('.domain-check-action').html(`
                            <button class="btn-info btn-sm domain-check-add-cart" type="button" onclick="register_domain_scripts.add_to_cart('${domain}')" style="${domain_is_added_to_cart ? 'display: none' : ''}">
                                <i class="bx bx-plus"></i>
                                Chọn mua
                            </button>
                            <button class="btn-danger btn-sm domain-check-remove-cart" type="button" onclick="register_domain_scripts.add_to_cart('${domain}')" style="${domain_is_added_to_cart ? '' : 'display: none'}">
                                <i class="bx bx-x"></i>
                                Bỏ chọn
                            </button>
                        `);
                                var origin_price = '';
                                if( d_data.reg_price_origin > d_data.reg_price ){
                                    var origin_price = `
                                <s>
                                    ${numberFormat(d_data.reg_price_origin)} ₫
                                </s>
                            `;
                                }
                                first_domain_el.find('.domain-check-price').html(`
                            ${origin_price}
                            <span>
                                 ${numberFormat(d_data.reg_price)} ₫
                            </span>
                        `);
                            }

                            // Chuyển sang kiểm tra domain khác hoặc xong
                            first_domain_el.removeClass('domain-check-item-checking');
                            register_domain_scripts.check();
                        },
                        error: () => {
                            setTimeout(function () {
                                register_domain_scripts.check();
                            }, 1000);
                        }
                    });
                },

                /*
                * Whois
                * */
                whois: (domain) => {
                    $('#loading').show();
                    $.ajax({
                        url: '/api/services/whois',
                        data: {domain: domain},
                        type: 'GET',
                        dataType: 'JSON',
                        success: (res) => {
                            var nameserver = '';
                            $.each(res.nameServer, function (i, item) {
                                nameserver += `<div>${item}</div>`;
                            });
                            console.log(res);
                            $('#whois-body').html(`
                        <table class="table table-border width-100">
                            <tbody>
                                <tr>
                                    <td class="width-40 menu">Tên miền</td>
                                    <td class="width-60 menu"><a href="//${res.domainName}" target="_blank"><b>${res.domainName}</b></a></td>
                                </tr>
                                <tr class="hidden">
                                    <td class="width-40 menu">Đăng ký tại</td>
                                    <td class="width-60 menu">${res.registrar}</td>
                                </tr>
                                <tr>
                                    <td class="width-40 menu">Chủ thể</td>
                                    <td class="width-60 menu">${res.registrantName}</td>
                                </tr>
                                <tr>
                                    <td class="width-40 menu">Ngày đăng ký</td>
                                    <td class="width-60 menu">${res.creationDate}</td>
                                </tr>
                                <tr>
                                    <td class="width-40 menu">Ngày hết hạn</td>
                                    <td class="width-60 menu">${res.expirationDate}</td>
                                </tr>
                                <tr>
                                    <td class="width-40 menu">Name server</td>
                                    <td class="width-60 menu">${nameserver}</td>
                                </tr>
                            </tbody>
                        </table>
                    `);
                            if( res.domainName.indexOf('.vn') === -1 ){
                                $('#whois-body').append(`
                            <div class="menu primary-bg" style="margin-top: 20px">Chi tiết</div>
                            <div class="pd-10">${res.rawtext}</div>
                        `);
                            }
                            $('#whois-modal').show();
                            $('#loading').hide();
                        },
                        error: () => {
                            setTimeout(function () {
                                register_domain_scripts.whois(domain);
                            }, 1000);
                        }
                    });
                },

                /*
                * Thêm vào giỏ hàng
                * */
                add_to_cart: (domain) => {
                    $('#loading').show();
                    $.ajax({
                        url: '/api/services/add-to-cart',
                        data: {value: domain, 'type': 'domain'},
                        type: 'GET',
                        dataType: 'JSON',
                        success: (res) => {
                            $('#loading').hide();
                            register_domain_scripts.refresh_cart();
                            $('.domain-check-item[data-domain="'+domain+'"]').find('.domain-check-action > button').hide();
                            if(res.is_add){
                                $('.domain-check-item[data-domain="'+domain+'"]').find('.domain-check-remove-cart').show();
                            }else{
                                $('.domain-check-item[data-domain="'+domain+'"]').find('.domain-check-add-cart').show();
                            }
                        },
                        error: () => {
                            setTimeout(function () {
                                register_domain_scripts.add_to_cart(domain);
                            }, 1000);
                        }
                    });
                },

                /*
                * Tải lại danh sách giỏ hàng
                * */
                refresh_cart: () => {
                    $('#loading').show();
                    $.ajax({
                        url: '',
                        type: 'GET',
                        dataType: 'HTML',
                        success: (res) => {
                            $('#cart-body').html( $(res).find('#cart-body').html() );
                            $('#loading').hide();
                        },
                        error: () => {
                            setTimeout(function () {
                                register_domain_scripts.refresh_cart();
                            }, 1000);
                        }
                    });
                },

                /*
                * Ấn kiểm tra 1 tên miền
                * */
                check_more: (self) => {
                    var parent_el = $(self).parents('.domain-check-item');
                    parent_el.addClass('domain-check-item-checking');
                    parent_el.find('.domain-check-action').html(`<i class="bx bx-loader bx-spin domain-check-loading"></i>`);
                    if( !register_domain_scripts._checking_domain ){
                        register_domain_scripts.check();
                    }
                },
            };

            function removeAccents(str) {
                return str.normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd').replace(/Đ/g, 'D');
            }
            @if( isset($_GET['domain']) )
            $(document).ready(function () {
                register_domain_scripts.submit();
            });
            @endif
        </script>
        <link rel="stylesheet"
              href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    </section>
@endsection


@section("script")

@endsection


@section("footer")
    @parent
@endsection
