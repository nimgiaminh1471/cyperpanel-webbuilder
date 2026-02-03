@php
    if( isset($_GET['renew']) ){
        // Gia hạn
        unset($_SESSION['carts']['domain']);
        $checkout_api = '/api/services/checkout-renew';
    }else{
        // Đăng ký mới
        unset($_SESSION['carts']['domain_renew']);
        $checkout_api = '/api/services/checkout';
    }
    $services = \classes\ServicesCart::get_services();
    Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
@endphp
<form class="flex flex-large" style="margin-top: 30px">
    <section class="width-70 flex-margin">
        <section class="section">
            <div class="section-heading">
                <i class="bx bx-list-ol"></i>
                Danh sách dịch vụ
            </div>
            @if( empty($services) )
                <div id="services-list">
                    <div class="alert-danger">
                        Bạn chưa chọn dịch vụ cần đăng ký
                    </div>
                </div>
            @else
                <div class="section-body" style="padding-bottom: 30px">
                <div class="table-responsive" id="services-list">
                    <table class="table table-border width-100" style="min-width: 500px">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center">
                                    STT
                                </th>
                                <th>
                                    Tên dịch vụ
                                </th>
                                <th style="width: 130px">
                                    Chọn thời hạn
                                </th>
                                <th style="width: 180px; text-align: right">
                                    Thành tiền
                                </th>
                                <th style="width: 60px; text-align: center">
                                    Bỏ
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $i = 0; @endphp
                        @foreach($services as $id => $item)
                            @php $i++; $user_id = $item->user_id ?? null; @endphp
                            <tr class="checkout-item" data-type="{{ $item->type }}" data-value="{{ $item->value }}">
                                <td style="text-align: center">
                                    {{ $i }}
                                </td>
                                <td>
                                    <div>
                                        <span class="primary-color">
                                            {{ $item->label }}
                                        </span>
                                    </div>
                                    <div>
                                        <b>
                                            {{ $item->value }}
                                        </b>
                                    </div>
                                </td>
                                <td>
                                    <select name="services[{{ $id }}][year]" class="checkout-item-year" onchange="__checkout.update_price()">
                                        @foreach($item->price as $y => $p_item)
                                            <option value="{{ $y }}" data-price="{{ $p_item->price }}" data-orgprice="{{ $p_item->price_origin }}" data-pricevat="{{ $p_item->price_vat }}">
                                                {{ $p_item->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="text-align: right" class="checkout-item-price">

                                </td>
                                <td style="text-align: center">
                                    <button type="button" class="btn-danger btn-sm" onclick="__checkout.remove(this, '{{ $item->type }}', '{{ $item->value }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </section>
        @if( empty($user_id) )
            <section class="section">
            <div class="section-heading">
                <i class="bx bx-user"></i>
                Thông tin cá nhân
            </div>
            <div class="section-body" style="padding-bottom: 30px">
                @if( permission('accountant') )
                    <link href="/assets/select2/select2.min.css" rel="stylesheet" />
                        <div class="select2-outer position-relative ">
                            <select class="select2 form-control select2-select-user-ajax" name="user[id]" data-me="{{ user('id') }}">
                                <option value="">
                                    Chọn người dùng
                                </option>
                            </select>
                        </div>
                        <div class="right pd-10">
                            <button type="button" class="btn btn-primary btn-sm" onclick="__checkout.add_user()">
                                <i class="fa fa-plus"></i>
                                Tạo user khách hàng mới
                            </button>
                        </div>
                    @php
                        function get_user_data($user_id, $column){
                            if( empty($user_id) ){
                                return;
                            }
                            return user($column, $user_id);
                        }
                    @endphp
                    <div id="checkout-user-info" class="hidden">
                        <div style="margin-bottom: 20px" id="reg-type">
                            <label class="check radio">
                                <input type="radio" name="reg_type" data-value="per" onchange="__checkout.change_reg_type(this)" checked> Cá nhân
                                <s></s>
                            </label>
                            <label class="check radio">
                                <input type="radio" name="reg_type" data-value="com" onchange="__checkout.change_reg_type(this)"> Công ty
                                <s></s>
                            </label>
                        </div>
                        <div class="reg-type-company hidden">
                            <div class="flex flex-large">
                                <div class="width-70 flex-margin">
                                    <div class="input-label">
                                        <span class="">Tên công ty (Nếu đăng ký cho tổ chức)</span>
                                        <input value="{{ get_user_data($user_id, 'company') }}" name="user[company]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                    </div>
                                </div>
                                <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                    <div class="input-label">
                                        <span class="">Mã số thuế</span>
                                        <input value="{{ get_user_data($user_id, 'company_tax') }}" name="user[company_tax]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-large">
                            <div class="width-20 flex-margin" style="margin-bottom: 20px;">
                                <div class="input-label">
                                    <span class="">Giới tính</span>
                                    <select name="user[gender]" class="width-100 form-field" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                        <option value="">Chọn</option>
                                        <option value="1" {{ (get_user_data($user_id, 'gender') == 1 ? 'selected' : '') }}>Ông</option>
                                        <option value="2" {{ (get_user_data($user_id, 'gender') == 2 ? 'selected' : '') }}>Bà</option>
                                    </select>
                                </div>
                            </div>
                            <div class="width-40 flex-margin" style="margin-bottom: 20px;">
                                <div class="input-label">
                                    <span class="">Họ và tên</span>
                                    <input value="{{ get_user_data($user_id, 'name') }}" name="user[name]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                            <div class="width-40 flex-margin" style="margin-bottom: 20px;">
                                <div class="form-date-wrap" data-format="day/month/year">
                                    <div class="form-date-picker form-date-picker-bottom hidden"></div>
                                    <div class="input-icon">
                                        <i class="fa fa-calendar"></i>
                                        <input class="input width-100 form-field" placeholder="Ngày sinh" type="text" name="user[birthday]" value="{{ get_user_data($user_id, 'birthday') }}" readonly=""/>
                                    </div>
                                    <code>{
                                        "allow": {
                                        "hours": [  ],
                                        "minutes": "0-59",
                                        "requiredHour": false,
                                        "days": "",
                                        "months": "",
                                        "weekDay": ["mon","tue","wed","thu","fri","sat","sun"],
                                        "min": {
                                        "y": 1950,
                                        "m": "01",
                                        "d": "01"
                                        },
                                        "max":{
                                        "y": {{ (date("Y") - 13) }},
                                        "m": "{{ date("m") }}",
                                        "d": "{{ date("d") }}"}
                                        },
                                        "value": {
                                        "day": "01",
                                        "month": "01",
                                        "year": "2000"
                                        }
                                        }
                                    </code>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-large">
                            <div class="width-40 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Email</span>
                                    <input value="{{ get_user_data($user_id, 'email') }}" name="user[email]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                            <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Số điện thoại</span>
                                    <input value="{{ get_user_data($user_id, 'phone') }}" name="user[phone]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                            <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Số CMND/Căn cước</span>
                                    <input value="{{ get_user_data($user_id, 'card_id') }}" name="user[card_id]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-large">
                            <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Tỉnh thành</span>
                                    <select name="user[province]" class="width-100 form-field" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                        <option value="">Chọn</option>
                                        @foreach( \models\InetProvince::get_provinces() as $item)
                                            <option value="{{ $item->name }}" {{ (get_user_data($user_id, 'province') == $item->name ? 'selected' : '') }}>
                                                {{ $item->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="width-70 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Địa chỉ</span>
                                    <input value="{{ get_user_data($user_id, 'address') }}" name="user[address]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="checkout-create-account">
                        <div class="hidden users-add-msg" style="margin-bottom: 10px">

                        </div>
                        <div id="checkout-create-account-body" class="hidden">
                            <input type="hidden" name="user[role]" value="2">
                            <div>
                                <div class="input-label">
                                    <span class="">Tạo mật khẩu người dùng</span>
                                    <input type="password" name="user[password]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                            <div class="center">
                                <button class="btn-primary" type="button" onclick="__checkout.create_user(this)">
                                    Tạo tài khoản
                                </button>
                            </div>
                        </div>
                    </div>
                    <script src="/assets/select2/select2.full.min.js"></script>
                    <script src="/assets/select2/form-select2.min.js"></script>
                    <script src="/assets/select2/select-user-ajax/scripts.js"></script>
                @else
                    <div id="checkout-user-info" class="{{ (permission('accountant') ? 'hidden' : '') }}">
                        <div id="reg-type" style="margin-bottom: 20px">
                            <label class="check radio">
                                <input type="radio" name="reg_type" data-value="per" onchange="__checkout.change_reg_type(this)" {{ empty( user('company') ) ? 'checked' : '' }}> Cá nhân
                                <s></s>
                            </label>
                            <label class="check radio">
                                <input type="radio" name="reg_type" data-value="com" onchange="__checkout.change_reg_type(this)" {{ empty( user('company') ) ? '' : 'checked' }}> Công ty
                                <s></s>
                            </label>
                        </div>
                        <div class="reg-type-company {{ empty( user('company') ) ? 'hidden' : '' }}">
                            <div class="flex flex-large">
                                <div class="width-70 flex-margin">
                                    <div class="input-label">
                                        <span class="">Tên công ty (Nếu đăng ký cho tổ chức)</span>
                                        <input value="{{ user('company') }}" name="user[company]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                    </div>
                                </div>
                                <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                    <div class="input-label">
                                        <span class="">Mã số thuế</span>
                                        <input value="{{ user('company_tax') }}" name="user[company_tax]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-large">
                            <div class="width-20 flex-margin" style="margin-bottom: 20px;">
                                <div class="input-label">
                                    <span class="">Giới tính</span>
                                    <select name="user[gender]" class="width-100 form-field" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                        <option value="1" {{ (user('gender') == 1 ? 'selected' : '') }}>Ông</option>
                                        <option value="2" {{ (user('gender') == 2 ? 'selected' : '') }}>Bà</option>
                                    </select>
                                </div>
                            </div>
                            <div class="width-40 flex-margin" style="margin-bottom: 20px;">
                                <div class="input-label">
                                    <span class="">Họ và tên</span>
                                    <input value="{{ user('name') }}" name="user[name]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                            <div class="width-40 flex-margin" style="margin-bottom: 20px;">
                                <div class="form-date-wrap" data-format="day/month/year">
                                    <div class="form-date-picker form-date-picker-bottom hidden"></div>
                                    <div class="input-icon">
                                        <i class="fa fa-calendar"></i>
                                        <input class="input width-100 form-field" placeholder="Ngày sinh" type="text" name="user[birthday]" value="{{ user('birthday') }}" readonly=""/>
                                    </div>
                                    <code>{
                                        "allow": {
                                        "hours": [  ],
                                        "minutes": "0-59",
                                        "requiredHour": false,
                                        "days": "",
                                        "months": "",
                                        "weekDay": ["mon","tue","wed","thu","fri","sat","sun"],
                                        "min": {
                                        "y": 1950,
                                        "m": "01",
                                        "d": "01"
                                        },
                                        "max":{
                                        "y": {{ (date("Y") - 13) }},
                                        "m": "{{ date("m") }}",
                                        "d": "{{ date("d") }}"}
                                        },
                                        "value": {
                                        "day": "01",
                                        "month": "01",
                                        "year": "2000"
                                        }
                                        }
                                    </code>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-large">
                            <div class="width-40 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Email</span>
                                    <input value="{{ user('email') }}" name="user[email]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                            <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Số điện thoại</span>
                                    <input value="{{ user('phone') }}" name="user[phone]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                            <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Số CMND/Căn cước</span>
                                    <input value="{{ user('card_id') }}" name="user[card_id]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-large">
                            <div class="width-30 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Tỉnh thành</span>
                                    <select name="user[province]" class="width-100 form-field" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                        <option value="">Chọn</option>
                                        @foreach( \models\InetProvince::get_provinces() as $item)
                                            <option value="{{ $item->name }}" {{ (user('province') == $item->name ? 'selected' : '') }}>
                                                {{ $item->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="width-70 flex-margin " style="margin-bottom: 20px">
                                <div class="input-label">
                                    <span class="">Địa chỉ</span>
                                    <input value="{{ user('address') }}" name="user[address]" class="width-100 input" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
        @endif
    </section>
    <section class="width-30 flex-margin">
        <section class="section">
            <div class="section-heading">
                <i class="bx bx-note"></i>
                Thông tin đơn hàng
            </div>
            <div class="section-body" style="padding-bottom: 30px">
                @if( !empty($services) )
                    <div class="checkout-amount-item" data-name="prevat">
                        Tổng tiền (Chưa VAT): <b></b>
                    </div>
                    <div class="checkout-amount-item" data-name="vat">
                        Thuế VAT: <b></b>
                    </div>
                    <div class="checkout-amount-item" data-name="aftervat">
                        <div>Tổng tiền cần thanh toán:</div>
                        <div>
                            <b></b>
                        </div>
                    </div>
                    <div style="color: #2c83a6">
                        @if( permission('accountant') )
                            <div>Giá iNet: <b id="agency-price"></b></div>
                        @else
                            <div class="center" id="order-submit-loading">
                                <i class="fa fa-spinner fa-spin" style="font-size: 20px"></i>
                            </div>
                        @endif
                        <input name="agency_price" value="" type="hidden">
                    </div>
                    <div class="form-msg hidden alert-danger" style="margin: 20px 0">

                    </div>
                    <div class="center" style="margin: 20px 0" id="order-submit">
                        <button class="btn-gradient" type="button" onclick="__checkout.save_order(this)">
                            Tiến hành thanh toán
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    </section>
</form>
<style>
    .checkout-item-price > s{
        font-size: 12px;
        color: gray;
    }
    .checkout-item-price > span{
        color: var(--primary-color);
        font-size: 16px;
        font-weight: bold;
        margin-left: 5px;
    }
    .checkout-amount-item {
        padding-bottom: 15px;
    }
    .checkout-amount-item[data-name="aftervat"]{
        color: #cc5e0e;
    }
    .checkout-amount-item[data-name="aftervat"] div{
        padding-top: 5px;
    }
    .checkout-amount-item[data-name="aftervat"] b{
        font-size: 20px;
    }
</style>
<script type="text/javascript">
    const __checkout = {
        /*
        * Cập nhật giá
        * */
        update_price: () => {
            var amount_before_vat = 0, amount_after_vat = 0;
            $('.checkout-item').each(function () {
               var price = $(this).find('.checkout-item-year option:selected').attr('data-price');
               var price_origin = $(this).find('.checkout-item-year option:selected').attr('data-orgprice');
               var price_vat = $(this).find('.checkout-item-year option:selected').attr('data-pricevat');
               $(this).find('.checkout-item-price').html(`
                    `+(price_origin > price ? '<s>'+numberFormat(price_origin)+' ₫</s><br>' : '')+`
                    <span>${numberFormat(price)} ₫</span>
               `);
                amount_before_vat += parseInt(price);
                amount_after_vat += parseInt(price_vat);
            });
            $('.checkout-amount-item[data-name="prevat"] b').html( `${numberFormat(amount_before_vat)} ₫` );
            $('.checkout-amount-item[data-name="aftervat"] b').html( `${numberFormat(amount_after_vat)} ₫` ).attr('data-amount', amount_after_vat);
            $('.checkout-amount-item[data-name="vat"] b').html( `${numberFormat(amount_after_vat - amount_before_vat)} ₫` );
            __checkout.update_agency_price();
        },

        /*
        * Lấy giá đại lý
        * */
        update_agency_price: () => {
            var data = [];
            $('.checkout-item').each(function () {
                var type_map = {
                    domain: 'register',
                    domain_renew: 'renew'
                };
                data.push({
                    type: type_map[$(this).attr('data-type')],
                    value: $(this).attr('data-value'),
                    year: $(this).find('.checkout-item-year').val()
                });
            });
            $('#agency-price').html(`<i class="fa fa-spin fa-spinner"></i>`);
            $('#order-submit').hide();
            $('#order-submit-loading').show();
            $.ajax({
                url: '/api/get-agency-price',
                data: {data: data},
                type: 'POST',
                dataType: 'JSON',
                success: (res) => {
                    var sell_price = $('.checkout-amount-item[data-name="aftervat"] b').attr('data-amount');
                    $('#agency-price').html( numberFormat(res.amount)+'₫ (Lãi '+numberFormat(sell_price - res.amount)+')' );
                    $('input[name="agency_price"]').val(res.amount);
                    $('#order-submit').show();
                    $('#order-submit-loading').hide();
                },
                error: () => {
                    setTimeout(function () {
                        __checkout.update_agency_price();
                    }, 1000);
                }
            });
        },

        /*
        * Xóa khỏi giỏ hàng
        * */
        remove: (self, type, value) => {
            $('#loading').show();
            $.ajax({
                url: '/api/services/add-to-cart',
                data: {value: value, 'type': type},
                type: 'GET',
                dataType: 'JSON',
                success: (res) => {
                    $('#loading').hide();
                    __checkout.refresh_cart();
                },
                error: () => {
                    setTimeout(function () {
                        __checkout.remove(self, type, value);
                    }, 1000);
                }
            });
        },

        /*
        * Làm mới lại danh sách dịch vụ
        * */
        refresh_cart: () => {
            $('#loading').show();
            $.ajax({
                url: '',
                type: 'GET',
                dataType: 'HTML',
                success: (res) => {
                    $('#services-list').html( $(res).find('#services-list').html() );
                    $('#loading').hide();
                    __checkout.update_price();
                },
                error: () => {
                    setTimeout(function () {
                        __checkout.refresh_cart();
                    }, 1000);
                }
            });
        },

        /*
        * Lưu đơn hàng
        * */
        save_order: (self) => {
            $('#loading').show();
            var form_el = $(self).parents('form');
            var data = form_el.serializeArray();
            $(form_el).find('.form-msg').slideUp();
            $.ajax({
                url: '{{ $checkout_api }}',
                data: data,
                type: 'POST',
                dataType: 'JSON',
                success: (res) => {
                    if( res.error.length > 0 ){
                        // Có lỗi
                        $(form_el).find('.form-msg').html(res.error).slideDown();
                    }else{
                        // Thành công
                        location.href = '/admin/Order?id='+res.order_id;
                    }
                    $('#loading').hide();
                },
                error: () => {
                    setTimeout(function () {
                        __checkout.save_order(self);
                    }, 1000);
                }
            });
        },

        /*
        * Ấn nút tạo tài khoản
        * */
        add_user: () => {
            $('#checkout-user-info').show();
            $('select[name="user[id]"]').empty().trigger('change');
            $('#checkout-user-info input, #checkout-user-info select').each(function () {
                var value = '';
                $('#checkout-create-account-body').slideDown();
                if( $(this).is('input') ){
                    $(this).val(value);
                }
                if( $(this).is('select') ){
                    $(this).children('[value="'+value+'"]').prop('selected', true);
                }
            });
            inputLabelInit();
        },
        /*
        * Tạo tài khoản
        * */
        create_user: (self) => {
            $('#loading').show();
            var form_el = $(self).parents('form');
            var data = form_el.serializeArray();
            $(form_el).find('.form-msg').slideUp();
            console.log(data);
            $.ajax({
                url: '/admin/UsersList',
                data: data,
                type: 'POST',
                dataType: 'HTML',
                success: (res) => {
                    if( $(res).find('.users-add-msg .alert-success').length == 0 ){
                        // Có lỗi
                        $(form_el).find('.users-add-msg')
                            .html( $(res).find('.users-add-msg').text() )
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .slideDown();
                    }else{
                        // Thành công
                        $(form_el).find('.users-add-msg')
                            .html( $(res).find('.users-add-msg').text() )
                            .addClass('alert-success')
                            .removeClass('alert-danger')
                            .slideDown();
                        $('#checkout-create-account-body').hide();
                    }
                    $('#loading').hide();
                },
                error: () => {
                    setTimeout(function () {
                        __checkout.create_user(self);
                    }, 1000);
                }
            });
        },
        /*
        * Thay đổi loại tài khoản: Cá nhân/Công ty
        * */
        change_reg_type: (self) => {
            var value = $('#reg-type input:checked').attr('data-value');
            $('.reg-type-company').hide();
            if(value == 'com'){
                $('.reg-type-company').show();
            }
        }
    };
    $(document).ready(function () {
        inputLabelInit();
        __checkout.update_price();
    });
</script>