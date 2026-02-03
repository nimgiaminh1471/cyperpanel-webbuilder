@php
    $items = \models\Order::where('id', '>', 0);
    if( !permission('accountant') ){
        $items = $items->where('user_id', user('id') );
    }
    $items = $items->orderBy('id', 'DESC')->paginate(10);
    echo Assets::show("/assets/tab-panel/style.css", "/assets/tab-panel/script.js");
@endphp
<section class="section" style="box-shadow: none;">
    <div class="heading">
        <i class="bx bx-note"></i>
        ĐƠN HÀNG
    </div>
</section>
<section class="table-responsive">
    <table class="width-100 table table-border" style="min-width: 700px">
        <thead>
        <tr>
            <th style="text-align: center; width: 50px">
                STT
            </th>
            <th>
                Danh sách dịch vụ
            </th>
            <th style="text-align: right; width: 140px">
                Tổng tiền
            </th>
            <th style="text-align: center; width: 170px">
                Trạng thái
            </th>
            <th style="text-align: center; width: 150px">
                Thời gian tạo
            </th>
            @if( permission('accountant') )
                <th style="text-align: center; width: 120px">
                    Khách hàng
                </th>
            @endif
        </tr>
        </thead>
        <tbody>
            @php $id = 0; @endphp
            @foreach($items as $item)
                @php $id++; @endphp
                <tr class="order-item" data-id="{{ $item->id }}">
                    <td style="text-align: center;">
                        {{ $id }}
                        <template class="order-item-data">@json($item)</template>
                        <template class="order-item-active-body">
                            <div class="flex menu bd-bottom" style="color: tomato; margin-top: 10px">
                                <div style="width: 120px">ID đơn hàng:</div>
                                <div><b>{{ $item->id }}</b></div>
                            </div>
                            <div class="flex menu bd-bottom" style="color: tomato">
                                <div style="width: 120px">Giá trị đơn hàng:</div>
                                <div><b>{{ number_format($item->amount) }} ₫</b></div>
                            </div>
                            <div class="flex menu bd-bottom" style="color: #2c83a6">
                                <div style="width: 120px">Giá iNet:</div>
                                <div><b>{{ number_format($item->agency_amount) }} ₫</b> (Lãi {{ number_format($item->amount - $item->agency_amount) }})</div>
                            </div>
                            <div class="flex menu bd-bottom">
                                <div style="width: 120px">Khách hàng:</div>
                                <div>{!! user('name_color', $item->user_id) !!}</div>
                            </div>
                            <div class="center pd-20">
                                <button class="btn-primary" onclick="__order.active_order_submit('{{ $item->id }}')">
                                    Xác nhận
                                </button>
                            </div>
                        </template>
                    </td>
                    <td>
                        <div class="order-item-services">
                            <table class="table table-border width-100 table-custom-color">
                                <tbody>
                                @php $sid = 0; @endphp
                                @foreach( json_decode($item->services) as $s_item)
                                    @php $sid++; @endphp
                                    <tr>
                                        <td style="text-align: center; width: 50px">
                                            {{ $sid }}
                                        </td>
                                        <td>
                                            <div class="primary-color">
                                                {{ $s_item->label }} ({{ $s_item->price->label }})
                                            </div>
                                            <b>
                                                {{ $s_item->value }}
                                            </b>
                                        </td>
                                        <td style="text-align: right; color: #cc5e0e; width: 120px">
                                    <span>
                                        {{ number_format($s_item->price->price_vat) }} ₫
                                    </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ number_format($item->amount) }} ₫
                    </td>
                    <td style="text-align: center;">
                        <span class="label-{{ ORDER_STATUS[ $item->status ]['color'] }}">
                            {{ ORDER_STATUS[ $item->status ]['label'] }}
                        </span>
                        @if( permission('accountant') )
                            @if($item->status == 0)
                                <div style="padding-top: 5px">
                                    <button onclick="__order.active_order({{ $item->id }})" class="btn-primary btn-sm">
                                        <i class="fa fa-check"></i>
                                        Kích hoạt
                                    </button>
                                    <button onclick="__order.cancel_order({{ $item->id }})" class="btn-danger btn-sm">
                                        <i class="fa fa-times"></i>
                                        Hủy
                                    </button>
                                </div>
                            @endif
                        @else
                            @if( $item->status == 0 )
                                <div style="padding-top: 5px">
                                    <button onclick="__order.payment({{ $item->id }})" class="btn-primary btn-sm">
                                        Thanh toán ngay
                                    </button>
                                </div>
                            @endif
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ $item->created_at }}
                    </td>
                    @if( permission('accountant') )
                        <td style="text-align: center;">
                            {!! user('name_color', $item->user_id) !!}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
<div class="center">
    {!! $items->links() !!}
</div>
<div class="modal hidden modal-allow-close modal-allow-scroll" id="modal-order-payment">
    <div class="modal-body" style="max-width: 500px">
        <div class="modal-content">
            <div class="heading modal-heading">
                <span>
                    Thanh toán đơn hàng
                </span>
                <i class="modal-close link fa"></i>
            </div>
            <div>
                <div class="bg pd-20" id="modal-order-payment-body">
                    <div class="modal-order-payment-services" style="margin: 5px; margin-bottom: 30px"></div>
                    <div class="alert-info" style="margin: 5px; margin-bottom: 10px">
                        Bạn muốn thanh toán qua ngân hàng/ví điện tử nào?
                    </div>
                    <div class="flex flex-middle payment-method flex-center">
                        @foreach(Storage::setting("banks", []) as $bank => $item)
                            <div class="width-25 pd-5">
                                <div class="payment-method-item pd-5 center">
                                    <label class="check radio">
                                        <input type="radio" name="recharge[payment_method]" value="{{ $bank }}">
                                        <s></s>
                                    </label>
                                    <img style="max-height: 50px" src="{{ $item["image"] }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-middle payment-method flex-center">
                        @foreach(Storage::setting("online_wallet", []) as $bank => $item)
                            <div class="width-25 pd-5">
                                <div class="payment-method-item pd-5 center">
                                    <label class="check radio">
                                        <input type="radio" name="recharge[payment_method]" value="{{ $bank }}">
                                        <s></s>
                                    </label>
                                    <img style="max-height: 50px" src="{{ $item["image"] }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @foreach(Storage::setting("banks", []) as $bank => $item)
                        <section class="payment-detail hidden" data-id="{{ $bank }}">
                            <div class="pd-10">
                            </div>
                            <div class="panel panel-info">
                                <div class="heading"><b>Chuyển khoản qua ngân hàng</b></div>
                                <div class="panel-body">
                                    {!! call_user_func(function($item){
                                        $out='';
                                        $out.='
                                             <div class="flex pd-5" style="color: tomato">
                                               <div style="width: 120px">Tổng số tiền:</div>
                                               <div><b class="payment-total-amount"></b></div>
                                             </div>
                                             <div class="flex pd-5" style="color: tomato">
                                               <div style="width: 120px">Nội dung CK:</div>
                                               <div><b class="payment-note"></b></div>
                                             </div>
                                        ';
                                        $infoLabel=[
                                            "name"   =>"Ngân hàng",
                                            "user"   =>"Chủ TK",
                                            "number" =>"Số TK",
                                            "office" =>"Chi nhánh"
                                        ];
                                        foreach($item as $key=>$info){
                                            if( isset($infoLabel[$key]) ){
                                                $out.='
                                                    <div class="flex pd-5">
                                                        <div style="width: 120px">'.$infoLabel[$key].':</div> <div><b>'.$info.'</b></div>
                                                    </div>
                                                ';
                                            }
                                        }
                                        return $out;
                                    }, $item) !!}
                                </div>
                            </div>
                        </section>
                    @endforeach
                    @foreach(Storage::setting("online_wallet", []) as $bank => $item)
                        <section class="payment-detail hidden" data-id="{{ $bank }}">
                            <div class="pd-10">
                            </div>
                            <div class="panel panel-info">
                                <div class="heading"><b>Chuyển tiền qua ví điện tử</b></div>
                                <div class="panel-body">
                                    {!! call_user_func(function($item){
                                        $out='';
                                        $out.='
                                             <div class="flex pd-5" style="color: tomato">
                                               <div style="width: 120px">Tổng số tiền:</div>
                                               <div><b class="payment-total-amount"></b></div>
                                             </div>
                                             <div class="flex pd-5" style="color: tomato">
                                               <div style="width: 120px">Nội dung CK:</div>
                                               <div><b class="payment-note"></b></div>
                                             </div>
                                        ';
                                        $infoLabel=[
                                            "name"   =>"Ví điện tử",
                                            "phone" =>"Số ĐT",
                                        ];
                                        foreach($item as $key=>$info){
                                            if( isset($infoLabel[$key]) ){
                                                $out.='
                                                    <div class="flex pd-5">
                                                        <div style="width: 120px">'.$infoLabel[$key].':</div> <div><b>'.$info.'</b></div>
                                                    </div>
                                                ';
                                            }
                                        }
                                        return $out;
                                    }, $item) !!}
                                </div>
                            </div>
                        </section>
                    @endforeach
                    <div class="hidden alert-danger modal-order-active-msg"></div>
                    <div id="modal-order-payment-by-wallet" class="text-center pd-10 hidden">
                        <div style="padding-bottom: 10px">
                            Số dư tài khoản:
                            <b id="user-wallet-amount" data-amount="{{ user('money') }}">{{ number_format( user('money') ) }}</b>
                        </div>
                        <button class="btn-gradient" type="button">
                            <i class="fa fa-money"></i>
                            Thanh toán bằng số dư
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal hidden modal-allow-close modal-allow-scroll" id="modal-order-active">
    <div class="modal-body" style="max-width: 450px">
        <div class="modal-content">
            <div class="heading modal-heading">
                <span>
                    Kích hoạt dịch vụ
                </span>
                <i class="modal-close link fa"></i>
            </div>
            <div>
                <div class="bg pd-20" id="modal-order-active-body">

                </div>
                <div class="center" id="org-fund" style="padding-bottom: 20px">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
                <script type="text/javascript">
                    setTimeout(function () {
                        $.ajax({
                            url: '/api/get-fund-amount',
                            type: 'GET',
                            dataType: 'JSON',
                            success: (res) => {
                                $('#org-fund').html(`Số dư đại lý iNet:
                                    <b class="primary-color">
                                        ${numberFormat(res.inet_fund) } ₫
                                    </b>
                                    <a class="btn btn-gradient btn-sm" href="https://dms.inet.vn/rms/account/organization/add-fund" target="_blank">Nạp</a>
                                `);
                            }
                        });
                    }, 1000);
                </script>
            </div>
        </div>
    </div>
</div>
<style>
    .table-custom-color td{
        border: 1px solid #dfdfdf !important;
    }
    .payment-method{
        align-items: stretch !important;
    }
    .payment-method-item{
        border: 1px solid #EAEAEA;
        height: 100%;
        cursor: pointer;
        position: relative;
    }
    .payment-method-item>label{
        display: none
    }
    .payment-method-item-actived,
    .payment-method-item:hover{
        border: 1px solid blue;
        background: #D2E5F2
    }
    @media (max-width: 767px){
        .payment-method>div{
            width: 50% !important
        }
    }
</style>
<script type="text/javascript">
    const __order = {
        /*
        * Chi tiết thanh toán
        * */
        payment: (order_id) => {
            var domain = (location.host.match(/([^.]+)\.\w{2,3}(?:\.\w{2})?$/) || [])[1];
            var order_el = $('.order-item[data-id="'+order_id+'"]');
            var data = JSON.parse( order_el.find('.order-item-data').html() );
            var table_services = order_el.find('.order-item-services').html();
            $('#modal-order-payment-body').find('.payment-total-amount').text( numberFormat(data.amount) +' ₫');
            var wallet = $('#user-wallet-amount').attr('data-amount');
            $('#modal-order-payment-by-wallet').hide();
            if( parseInt(wallet) >= data.amount ){
                $('#modal-order-payment-by-wallet').show();
                $('#modal-order-payment-by-wallet button').attr('onclick', `__order.active_order_submit(${data.id})`);
            }
            var services = JSON.parse(data.services);
            var domain = services[0].value.split('.')[0];
            $('#modal-order-payment-body').find('.payment-note').text( 'dk ten mien '+domain );
            $('.modal-order-payment-services').html(table_services);
            $('#modal-order-payment').show();
        },

        /*
        * Hủy đơn hàng
        * */
        cancel_order: (order_id) => {
            if( !confirm('Xác nhận hủy đơn hàng này') ){
                return;
            }
            $('#loading').show();
            $.ajax({
                url: '/api/services/cancel-order',
                data: {id: order_id},
                type: 'POST',
                dataType: 'JSON',
                success: (res) => {
                    $('#loading').hide();
                    location.reload();
                },
                error: () => {
                    setTimeout(function () {
                        __order.cancel_order(order_id);
                    }, 1000);
                }
            });
        },

        /*
        * Xác nhận kích hoạt đơn hàng
        * */
        active_order: (order_id) => {
            var order_el = $('.order-item[data-id="'+order_id+'"]');
            var data = JSON.parse( order_el.find('.order-item-data').html() );
            if( data.status > 0 ){
                return;
            }
            var table_services = order_el.find('.order-item-services').html();
            var order_active_body = order_el.find('.order-item-active-body').html();
            $('#modal-order-active').show();
            $('#modal-order-active-body').html(`${table_services}${order_active_body}<div class="hidden alert-danger modal-order-active-msg"></div>`);
        },

        /*
        * Ấn nút kích hoạt đơn hàng
        * */
        active_order_submit: (order_id) => {
            $('#loading').show();
            $('.modal-order-active-msg').slideUp();
            $.ajax({
                url: '/api/services/active-order',
                data: {id: order_id},
                type: 'POST',
                dataType: 'JSON',
                success: (res) => {
                    $('#loading').hide();
                    if( res.error.length > 0 ){
                        // Có lỗi
                        $('.modal-order-active-msg').html(res.error).slideDown();
                    }else{
                        // Thành công
                        alert('Đơn hàng của bạn đã được kích hoạt');
                        location.href = res.move_to;
                    }
                },
                error: () => {
                    setTimeout(function () {
                        __order.active_order_submit(order_id);
                    }, 1000);
                }
            });
        },
    };
    @if( isset( $_GET['id'] ) )
        $(document).ready(function () {
            @if( permission('accountant') )
                __order.active_order({{ $_GET['id'] }});
            @else
                __order.payment({{ $_GET['id'] }});
            @endif
        });
    @endif
    $(".payment-method-item").on("click", function(){
        var parentEl = $(this).parents(".payment-method");
        $(".payment-method-item-actived").removeClass("payment-method-item-actived");
        $(this).addClass("payment-method-item-actived");
        $(".payment-method-item").find("input").prop("checked", false);
        $(this).find("input").prop("checked", true).change();
        var show = $(this).find("input").val();
        $(".payment-detail").hide();
        $(".payment-detail[data-id='"+show+"']").show();
    });
</script>
