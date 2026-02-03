@php
    $inet_data = \classes\InetAPI::connect('domain/search', [
        'pageSize' => 10,
        'page' => ($_GET['page'] ?? 1) - 1
    ]);
    if( !empty($inet_data->content) ){
        foreach($inet_data->content as $item){
            \models\InetDomain::sync_domain($item);
        }
    }else if( permission('admin') ){
        dd($inet_data);
    }
        $items = \models\InetDomain::where('id', '>', '0');
        if( !permission('accountant') ){
            $items = $items->where('user_id', user('id') );
        }
        if( !empty($_GET['keyword']) ){
            $items = $items->where('name', 'LIKE', '%'.$_GET['keyword'].'%' );
        }
        $_GET['status'] = $_GET['status'] ?? 'active';
        if( !empty($_GET['status']) ){
            if( $_GET['status'] == 'expire' ){
                $items = $items->where('expired_date', '<', date('Y-m-d', strtotime('+7 days')) )
                    ->where('expired_date', '>=', date('Y-m-d') );
            }else{
                $items = $items->where('status', $_GET['status'] );
            }
        }
        $items = $items->orderBy('inet_id', 'DESC')->paginate(10);

        echo Assets::show('/assets/dropdown/dropdown.js', '/assets/dropdown/dropdown.css')
@endphp
@if( permission('accountant') )
    <div class="center" id="org-fund">
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
                <b>
                    ${numberFormat(res.inet_fund) } ₫
                </b>
                `);
                }
            });
        }, 1000);
    </script>
@endif
<div class="pd-20 center">
    <a href="/admin/RegisterDomain" class="btn btn-gradient">
        Đăng ký tên miền mới
    </a>
</div>
<section class="section" style="box-shadow: none;">
    <div class="heading flex flex-middle flex-medium">
        <div class="width-40 pd-5">
            <i class="bx bx-globe"></i>
            TÊN MIỀN
        </div>
        <div class="width-20 right pd-5">
            <select class="select search-filter width-100" name="status" onchange="__domain_manager.refresh(null)">
                <option value="">Tất cả trạng thái</option>
                <option value="expire">
                    Hết hạn trong tuần
                    @php
                        $citems = \models\InetDomain::where('id', '>', '0');
                        if( !permission('accountant') ){
                            $citems = $citems->where('user_id', user('id') );
                        }
                        $citems = $citems->where('expired_date', '<', date('Y-m-d', strtotime('+7 days')) )
                            ->where('expired_date', '>=', date('Y-m-d') );
                        $citems = $citems->count();
                    @endphp
                    ({{ $citems }})
                </option>
                @foreach(DOMAIN_STATUS as $key => $item)
                    <option value="{{ $key }}" {{ ($key == $_GET['status'] ? 'selected' : '') }}>
                        {{ $item['label'] }}
                        @php
                            $citems = \models\InetDomain::where('id', '>', '0');
                            if( !permission('accountant') ){
                                $citems = $citems->where('user_id', user('id') );
                            }
                            $citems = $citems->where('status', $key);
                            $citems = $citems->count();
                        @endphp
                        ({{ $citems }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="width-40 right pd-5">
            <input name="keyword" class="input search-filter" placeholder="Tìm kiếm tên miền..." style="width: 100%;" onkeyup="__domain_manager.refresh(null)">
        </div>
    </div>
</section>
<section id="item-list">
    <div class="table-responsive">
        <table class="width-100 table table-border" style="min-width: 600px">
            <thead>
            <tr>
                <th style="width: 300px">
                    Tên miền
                </th>
                <th style="width: 200px">
                    Tên chủ thể
                </th>
                <th style="width: 140px">
                    Trạng thái
                </th>
                <th style="text-align: center">
                    Thao tác
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr class="domain-item" data-domain="{{ $item->name }}" data-id="{{ $item->inet_id }}">
                    <td>
                        <div style="font-weight: bold">
                            <div>
                                {{ $item->name }}
                            </div>
                        </div>
                        <div style="font-size: 14px; color: gray; padding-top: 5px">
                            Ngày đăng ký: {{ date('d/m/Y', strtotime($item->register_date) ) }}
                        </div>
                        <div style="font-size: 14px; color: gray; padding-top: 5px">
                            @php
                                $exp_day = round( ( strtotime($item->expired_date) - time() ) / (60 * 60 * 24) );
                            @endphp
                            Ngày hết hạn: {{ date('d/m/Y', strtotime($item->expired_date) ) }}
                            (Còn <b style="{{ ($exp_day > 60 ? '' : 'color: red') }}">{{ $exp_day }}</b> ngày)
                        </div>
                        @php
                            $item->record = json_decode($item->record);
                            $item->contacts = json_decode($item->contacts);
                            $item->dns = json_decode($item->dns);
                        @endphp
                        <textarea class="hidden domain-item-data">@json($item)</textarea>
                    </td>
                    <td>
                        <div>
                            {{ $item->registrant }}
                        </div>
                        <div>
                            @if( !empty($item->user_id) )
                                {!! user('name_color', $item->user_id) !!}
                                <br>
                                {!! user('phone', $item->user_id) !!}
                            @endif
                        </div>
                    </td>
                    <td>
                        <div>
                            <span class="label-{{ DOMAIN_STATUS[$item->status]['color'] }}" style="font-size: 13px">
                                {{ DOMAIN_STATUS[$item->status]['label'] }}
                            </span>
                        </div>
                        <div style="margin-top: 5px; font-size: 13px">
                            @if( strpos($item->name, '.vn') === false )
                                @if($item->verify_status == 'not-verify')
                                    <span style="color: red;">
                                        <i class="fa fa-times"></i>
                                        Chưa xác thực
                                    </span>
                                @else
                                    <span style="color: #18be18;">
                                        <i class="fa fa-check"></i>
                                        Đã xác thực
                                    </span>
                                @endif
                            @else
                                @if($item->contract == 'approved')
                                    <span style="color: #18be18;">
                                        <i class="fa fa-check"></i>
                                        Bản khai đã duyệt
                                    </span>
                                @else
                                    <span style="color: red;">
                                        <i class="fa fa-info-circle"></i>
                                        Chưa có bản khai
                                    </span>
                                @endif
                            @endif
                        </div>
                    </td>
                    <td style="text-align: center">
                        <div class="btn-group">
                            @if( isset($_SESSION['carts']['domain_renew'][$item->name]) )
                                <a class="btn btn-default mt-btn btn-sm" onclick="__domain_manager.renew.show_popup('{{ $item->name }}')" style="color: tomato">
                                    Bỏ chọn
                                </a>
                            @else
                                <a class="btn btn-default mt-btn btn-sm" onclick="__domain_manager.renew.show_popup('{{ $item->name }}')">
                                    Gia hạn
                                </a>
                            @endif
                            <a class="btn btn-default mt-btn btn-sm" onclick="__domain_manager.dns.init('{{ $item->name }}')">
                                DNS
                            </a>
                            <a class="btn btn-default mt-btn btn-sm" onclick="__domain_manager.record.init('{{ $item->name }}')">
                                Bản ghi
                            </a>
                            <a class="btn btn-default mt-btn btn-sm" onclick="__domain_manager.whois('{{ $item->name }}')">
                                Whois
                            </a>
                            <div class="dropdown">
                                <a class="btn btn-default mt-btn dropdown-btn btn-sm" onclick="dropdown_toggle(this)">
                                    <i class="fa fa-ellipsis-h"></i>
                                </a>
                                <div class="dropdown-content">
                                    {{--@if( strpos($item->name, '.vn') === false )
                                        <a onclick="">
                                            Ẩn thông tin
                                        </a>
                                    @endif--}}
                                    <a href="http://bankhai.exdomain.net/docusign?token={{ $item->contact_token }}" target="_blank">
                                        Bản khai tên miền
                                    </a>
                                    @if( strpos($item->name, '.vn') !== false && $item->contract != 'approved' )
                                        <a onclick="__domain_manager.upload_id_card.init('{{ $item->name }}')">
                                            Upload CMND
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 10px 0">
        Tổng số tên miền: <b>{{ $items->total() }}</b>
    </div>
    <div class="center pd-20">
        {!! $items->links() !!}
    </div>
</section>
@if( permission('accountant') )
    <div class="center pd-20">
        <button class="btn-primary" onclick="__domain_manager.sync(1)">
            <i class="fa fa-refresh"></i>
            Đồng bộ tên miền từ inet
        </button>
        <div class="alert-info hidden" id="domain-sync-msg"></div>
    </div>
@endif

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
<div class="modal hidden modal-allow-close modal-allow-scroll" id="dns-modal">
    <div class="modal-body" style="max-width: 450px">
        <div class="modal-content">
            <div class="heading modal-heading">
                <span>
                    DNS: <b class="modal-title-value"></b>
                </span>
                <i class="modal-close link fa"></i>
            </div>
            <div>
                <form class="bg pd-20 modal-body-content">
                    <div id="dns-list">

                    </div>
                    <div class="pd-10 right">
                        <button type="button" class="btn-info btn-sm" onclick="__domain_manager.dns.add_row(this)">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="pd-20 center">
                        <button type="button" class="btn-primary" onclick="__domain_manager.dns.save(this)">
                            Cập nhật
                        </button>
                    </div>
                    <div class="hidden form-msg alert-danger"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal hidden modal-allow-close modal-allow-scroll" id="record-modal">
    <div class="modal-body" style="max-width: 850px">
        <div class="modal-content">
            <div class="heading modal-heading">
                <span>
                    Bản ghi: <b class="modal-title-value"></b>
                </span>
                <i class="modal-close link fa"></i>
            </div>
            <div>
                <form class="bg pd-20 modal-body-content">
                    <div class="pd-10 right">
                        <button type="button" class="btn-info btn-sm" onclick="__domain_manager.record.add_row(this)">
                            <i class="fa fa-plus"></i> Thêm bản ghi
                        </button>
                    </div>
                    <div class="table-responsive">
                        <div style="min-width: 550px">
                            <div id="record-list">

                            </div>
                            <div id="record-list-append">

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal hidden modal-allow-close modal-allow-scroll" id="upload-id-card-modal">
    <div class="modal-body" style="max-width: 450px">
        <div class="modal-content">
            <div class="heading modal-heading">
                <span>
                    XÁC MINH CMND/CC: <b class="modal-title-value"></b>
                </span>
                <i class="modal-close link fa"></i>
            </div>
            <div>
                <form class="bg pd-20 modal-body-content">
                    <div id="upload-id-card-modal-body">
                    </div>
                    <div class="menu bd-bottom"></div>
                    <div class="flex flex-middle menu bd-bottom">
                        <div class="width-30">
                            Mặt trước:
                        </div>
                        <div class="width-70">
                            <input type="file" name="front_end" accept="image/*">
                        </div>
                    </div>
                    <div class="flex flex-middle menu bd-bottom">
                        <div class="width-30">
                            Mặt sau:
                        </div>
                        <div class="width-70">
                            <input type="file" name="back_end" accept="image/*">
                        </div>
                    </div>
                    <div class="pd-20 center">
                        <button type="button" class="btn-primary" onclick="__domain_manager.upload_id_card.save()">
                            Xác minh
                        </button>
                    </div>
                    <div class="hidden form-msg alert-danger"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="cart-fixed" style="{{ ( empty($_SESSION['carts']['domain_renew']) ? '' : 'display: block') }}">
    <section class="section">
        <div class="section-heading" onclick="__domain_manager.toggle_cart()">
            <i class="bx bx-cart-alt"></i>
            GIA HẠN TÊN MIỀN
        </div>
        <div class="section-body" id="cart-body">
            <?php if( $_SESSION['carts']['domain_renew'] ?? [] ): ?>
            <table class="table table-border width-100" id="cart-domain">
                <tbody>
                <?php foreach($_SESSION['carts']['domain_renew'] as $item): ?>
                <tr class="cart-domain-item" data-domain="<?php echo $item; ?>">
                    <td>
                        <b>
                            <?php echo $item; ?>
                        </b>
                    </td>
                    <td style="width: 60px; text-align: center">
                        <button class="btn btn-danger btn-sm" onclick="register_domain_scripts.add_to_cart('<?php echo $item; ?>', 'domain_renew')">
                            <i class="bx bx-x"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="center pd-20">
                <a class="btn btn-gradient" href="/admin/Checkout?renew">
                    Tiến hành gia hạn
                    <i class="bx bx-chevron-right"></i>
                </a>
            </div>
            <?php else: ?>
            Hãy chọn tên miền cần gia hạn!
            <?php endif; ?>
        </div>
    </section>
</section>
<style>
    .mt-btn.btn {
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 12%);
    }
    .btn-group>.btn:first-child {
        margin-left: 0;
    }
    .btn-group>.btn-group:first-child:not(:last-child)>.btn:last-child, .btn-group>.btn-group:first-child:not(:last-child)>.dropdown-toggle, .btn-group>.btn:first-child:not(:last-child):not(.dropdown-toggle) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .cart-fixed {
        position: fixed;
        bottom: 0;
        right: 0;
        z-index: 99999;
        width: 100%;
        max-width: 450px;
        display: none;
    }
    .cart-fixed > .section {
        margin-bottom: 0 !important;
    }
    .cart-fixed .section-heading{
        background: var(--primary-color) !important;
        color: white !important;
        position: relative;
        cursor: pointer;
    }
    .cart-fixed .section-heading:after{
        content: '\f107';
        font: normal normal normal 14px/1 FontAwesome;
        right: 10px;
        top: 50%;
        position: absolute;
        transform: translate(-50%, -50%);
    }
</style>
<script type="text/javascript">
    const __domain_manager = {
        toggle_cart: () => {
            $('.cart-fixed .section-body').slideToggle();
        },
        sync: (page) => {
            $('#domain-sync-msg').prev().hide();
            if( $('#domain-sync-msg').is(':hidden') ){
                $('#domain-sync-msg').html('<i class="fa fa-spinner fa-spin"></i> Đang đồng bộ, vui lòng chờ').show();
            }
            $.ajax({
                url: '/api/services/sync-domain',
                data: {page: page},
                type: 'POST',
                dataType: 'JSON',
                success: (res) => {
                    if( res.error.length > 0 ){
                        // Có lỗi
                    }else{
                        // Thành công
                        if(page >= res.total_page){
                            alert('Đã hoàn tất đồng bộ');
                            location.reload();
                        }else{
                            page++;
                            $('#domain-sync-msg').html(`<i class="fa fa-spinner fa-spin"></i> Đang đồng bộ: ${page}/${res.total_page}`).show();
                        }
                    }
                    __domain_manager.refresh(function () {

                    });
                    setTimeout(function () {
                        __domain_manager.sync(page);
                    }, 200);
                },
                error: () => {
                    setTimeout(function () {
                        __domain_manager.sync(page);
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
                        __domain_manager.whois(domain);
                    }, 1000);
                }
            });
        },

        /*
        * DNS
        * */
        dns: {
            init: (domain) => {
                $('#loading').show();
                var data = JSON.parse( $('.domain-item[data-domain="'+domain+'"]').find('.domain-item-data').val() );
                $.ajax({
                    url: '/api/services/sync-dns',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {id: data.inet_id},
                    success: (res) => {
                        $('#loading').hide();
                        __domain_manager.dns.show_popup(domain, res);
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.dns.init(domain);
                        }, 1000);
                    }
                });
            },
            show_popup: (domain, dns) => {
                var data = JSON.parse( $('.domain-item[data-domain="'+domain+'"]').find('.domain-item-data').val() );
                var dns_list = '';
                $('#dns-modal').find('.modal-title-value').html(`<b>${data.name}</b>`);
                $.each(dns, function (i, item) {
                    dns_list += `
                        <div class="pd-10 flex flex-middle dns-ns-item">
                        <div class="width-90">
                            <input class="input width-100" placeholder="NS${i + 1}" name="ns[]" value="${item}">
                        </div>
                        <div class="width-10">
                            <button type="button" class="btn-danger btn-sm" onclick="__domain_manager.dns.remove_row(this)" style="margin-left: 5px">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    `;
                });

                dns_list += `<input type="hidden" name="inet_id" value="${data.inet_id}">`;
                $('#dns-list').html(dns_list);
                $('#dns-modal').find('.form-msg').hide();
                $('#dns-modal').show();
            },
            remove_row: (self) => {
                $(self).parents('.dns-ns-item').remove();
            },
            add_row: (self) => {
                $('#dns-list').append(`
                    <div class="pd-10 flex flex-middle dns-ns-item">
                        <div class="width-90">
                            <input class="input width-100" placeholder="" name="ns[]" value="">
                        </div>
                        <div class="width-10">
                            <button type="button" class="btn-danger btn-sm" onclick="__domain_manager.dns.remove_row(this)" style="margin-left: 5px">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                `);
            },
            save: (self) => {
                var form_el = $(self).parents('form');
                var data = form_el.serializeArray();
                form_el.find('.form-msg').slideUp();
                $('#loading').show();
                $.ajax({
                    url: '/api/services/update-dns',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    success: (res) => {
                        $('#loading').hide();
                        if( res.error.length == 0 ){
                            $('#dns-modal').hide();
                            alert('Cập nhật thành công');
                        }else{
                            form_el.find('.form-msg').html(res.error).slideDown();
                        }
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.dns.save(self);
                        }, 1000);
                    }
                });
            }
        },

        /*
       * Bản ghi
       * */
        rtype_list: {
            A: 'A (IP Address)',
            CNAME: 'CNAME (Alias)',
            MX: 'MX (Mail Exchange)',
            REDIRECT: 'URL Redirect',
            DOMAIN_REDIRECT: 'Domain Redirect',
            FRAME: 'URL Frame',
            TXT: 'TXT(Text)',
            AAAA: 'AAAA (IPV6 Host)',
            SRV: 'SRV Record'
        },
        record: {
            init: (domain) => {
                $('#loading').show();
                var data = JSON.parse( $('.domain-item[data-domain="'+domain+'"]').find('.domain-item-data').val() );
                $.ajax({
                    url: '/api/services/sync-record',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {id: data.inet_id},
                    success: (res) => {
                        $('#loading').hide();
                        __domain_manager.record.show_popup(domain, res);
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.record.init(domain);
                        }, 1000);
                    }
                });
            },
            show_popup: (domain, record) => {
                var data = JSON.parse( $('.domain-item[data-domain="'+domain+'"]').find('.domain-item-data').val() );
                var record_list = '';
                $('#record-modal').find('.modal-title-value').html(`<b>${data.name}</b>`);
                record_list += `
                        <div class="pd-10 flex flex-middle menu bd-bottom">
                            <div class="width-20 pd-5">
                                <b>
                                    Tên bản ghi
                                </b>
                            </div>
                            <div class="width-20 pd-5">
                                <b>
                                    Loại bản ghi
                                </b>
                            </div>
                            <div class="width-30 pd-5">
                                <b>
                                    Giá trị
                                </b>
                            </div>
                            <div class="width-15 pd-5">
                                <b>
                                    Ưu tiên
                                </b>
                            </div>
                            <div class="width-15 pd-5 right">
                                <b>
                                    Thao tác
                                </b>
                            </div>
                        </div>
                    `;
                $.each(record, function (i, item) {
                    var rtype = '';
                    $.each(__domain_manager.rtype_list, function (type, label) {
                        rtype += `
                            <option value="${type}" ${(item.type == type ? 'selected' : '')}>${label}</option>
                        `;
                    });
                    record_list += `
                        <div class="pd-10 flex flex-middle bd-bottom menu">
                            <div class="width-20 pd-5">
                                <input class="input width-100" placeholder="" name="name" value="${item.name}">
                            </div>
                            <div class="width-20 pd-5">
                                <select class="select width-100" name="type" onchange="__domain_manager.record.change_type(this)">
                                    ${rtype}
                                </select>
                            </div>
                            <div class="width-30 pd-5">
                                <input class="input width-100" placeholder="" name="data" value="${item.data}">
                                <div class="record-item-msg" style="color: red"></div>
                            </div>
                            <div class="width-15 pd-5">
                                <input class="input width-100 ${item.type == 'MX' ? '' : 'hidden'}" placeholder="" name="priority" value="${(typeof item.priority == 'undefined' ? '' : item.priority)}">
                            </div>
                            <div class="width-15 pd-5 right">
                                <button type="button" class="btn-primary btn-sm" data-rid="${item.id}" onclick="__domain_manager.record.save_row(this)" style="margin-left: 5px">
                                    <i class="fa fa-save"></i>
                                </button>
                                <button type="button" class="btn-danger btn-sm" data-rid="${item.id}" onclick="__domain_manager.record.remove_row(this)" style="margin-left: 5px">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                record_list += `<input type="hidden" name="inet_id" value="${data.inet_id}">`;
                $('#record-list').html(record_list);
                $('#record-modal').find('.form-msg').hide();
                $('#record-modal').show();
            },
            remove_row: (self) => {
                var record_id = $(self).attr('data-rid');
                if( typeof record_id == 'undefined'){
                    return $(self).parents('.flex').remove();
                }
                var domain_id = $(self).parents('form').find('input[name="inet_id"]').val();
                var data = JSON.parse( $('.domain-item[data-id="'+domain_id+'"]').find('.domain-item-data').val() );
                $('#loading').show();
                $.ajax({
                    url: '/api/services/record',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {action: 'del', inet_id: domain_id, record_id: record_id},
                    success: (res) => {
                        __domain_manager.refresh(function () {
                            $('#loading').hide();
                            __domain_manager.record.init(data.name);
                        });
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.record.remove_row(self, record_id);
                        }, 1000);
                    }
                });
            },
            change_type: (self) => {
                var parent_el = $(self).parents('.flex');
                var pt_el = parent_el.find('input[name="priority"]');
                var type = $(self).val();
                if( type == 'MX' ){
                    pt_el.show();
                }else{
                    pt_el.hide();
                }
            },
            add_row: (self) => {
                var record_list = '';
                var rtype = '';
                $.each(__domain_manager.rtype_list, function (type, label) {
                    rtype += `
                            <option value="${type}">${label}</option>
                        `;
                });
                record_list += `
                        <div class="pd-10 flex flex-middle bd-bottom menu">
                            <div class="width-20 pd-5">
                                <input class="input width-100" placeholder="" name="name" value="">
                            </div>
                            <div class="width-20 pd-5">
                                <select class="select width-100" name="type" onchange="__domain_manager.record.change_type(this)">
                                    ${rtype}
                                </select>
                            </div>
                            <div class="width-30 pd-5">
                                <input class="input width-100" placeholder="" name="data" value="">
                                <div class="record-item-msg" style="color: red"></div>
                            </div>
                            <div class="width-15 pd-5">
                                <input class="input width-100 hidden" placeholder="" name="priority" value="">
                            </div>
                            <div class="width-15 pd-5 right">
                                <button type="button" class="btn-info btn-sm" onclick="__domain_manager.record.save_row(this)" style="margin-left: 5px">
                                    <i class="fa fa-check"></i>
                                </button>
                                <button type="button" class="btn-danger btn-sm" onclick="__domain_manager.record.remove_row(this)" style="margin-left: 5px">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                $('#record-list-append').append(record_list);
            },
            save_row: (self) => {
                var record_id = $(self).attr('data-rid');
                if( typeof record_id == 'undefined'){
                    var record_id = '';
                }

                var domain_id = $(self).parents('form').find('input[name="inet_id"]').val();
                var data = JSON.parse( $('.domain-item[data-id="'+domain_id+'"]').find('.domain-item-data').val() );
                var form_data = $(self).parents('.flex').find('input, select').serializeArray();
                form_data.push({
                    name: 'action',
                    value: 'update'
                });
                form_data.push({
                    name: 'inet_id',
                    value: domain_id
                });
                form_data.push({
                    name: 'record_id',
                    value: record_id
                });
                $('#loading').show();
                $.ajax({
                    url: '/api/services/record',
                    type: 'POST',
                    dataType: 'JSON',
                    data: form_data,
                    success: (res) => {
                        if( typeof res.data.message != 'undefined'){
                            alert(res.data.message);
                        }else{
                            if( record_id.length == 0){
                                $(self).parents('.flex').remove();
                            }
                            __domain_manager.record.init(data.name);
                        }
                        $('#loading').hide();
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.record.remove_row(self, record_id);
                        }, 1000);
                    }
                });
            }
        },

        /*
        * Gia hạn
        * */
        renew: {
            show_popup: (domain) => {
                var data = JSON.parse($('.domain-item[data-domain="' + domain + '"]').find('.domain-item-data').val());
                register_domain_scripts.add_to_cart(domain, 'domain_renew');
            }
        },

        /*
        * Tải lại danh sách
        * */
        refresh_settimeout: null,
        refresh: (callback) => {
            $('#item-list').css({opacity: 0.3});
            clearTimeout(__domain_manager.refresh_settimeout);
            __domain_manager.refresh_settimeout = setTimeout(function () {
                var data = $('.search-filter').serializeArray();
                $.ajax({
                    url: '',
                    type: 'GET',
                    dataType: 'HTML',
                    data: data,
                    success: (res) => {
                        $('#item-list').html( $(res).find('#item-list').html() ).css({opacity: ''});;
                        if( callback ){
                            callback();
                        }
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.refresh(callback);
                        }, 1000);
                    }
                });
            }, 300);
        },
        /*
        * Upload CMND
        * */
        upload_id_card: {
            init: (domain) => {
                $('#loading').show();
                var data = JSON.parse( $('.domain-item[data-domain="'+domain+'"]').find('.domain-item-data').val() );
                $.ajax({
                    url: '/api/services/sync-dns',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {id: data.inet_id},
                    success: (res) => {
                        $('#loading').hide();
                        __domain_manager.upload_id_card.show_popup(domain, res);
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.upload_id_card.init(domain);
                        }, 1000);
                    }
                });
            },
            show_popup: (domain) => {
                var data = JSON.parse($('.domain-item[data-domain="' + domain + '"]').find('.domain-item-data').val());
                $('#upload-id-card-modal').find('.modal-title-value').html(`<b>${data.name}</b>`);
                var html = `<input type="hidden" name="domain_id" value="${data.id}">`;
                html += `<div class="flex flex-middle">
                    <div class="width-40 menu">Chủ thể</div>
                    <div class="width-60 menu">${data.registrant}</div>
                </div>`;
                $('#upload-id-card-modal-body').html(html);
                $('#upload-id-card-modal').show();
            },
            save: () => {
                $('#loading').show();
                var form_el = $('#upload-id-card-modal form');
                form_el.find('.form-msg').slideUp();
                var data = new FormData(form_el[0]);
                $.ajax({
                    url: '/api/upload-id-card',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: (res) => {
                        $('#loading').hide();
                        if( res.error.length == 0 ){
                            $('#upload-id-card-modal').hide();
                            form_el.find('input').val('');
                            alert('Cập nhật thành công, vui lòng đợi duyệt');
                        }else{
                            form_el.find('.form-msg').html(res.error).slideDown();
                        }
                    },
                    error: () => {
                        setTimeout(function () {
                            __domain_manager.upload_id_card.save();
                        }, 1000);
                    }
                });
            }
        },

    };
    const register_domain_scripts = {
        /*
        * Thêm vào giỏ hàng
        * */
        add_to_cart: (domain, type) => {
            $('#loading').show();
            $.ajax({
                url: '/api/services/add-to-cart',
                data: {value: domain, 'type': type},
                type: 'GET',
                dataType: 'JSON',
                success: (res) => {
                    $('#loading').hide();
                    __domain_manager.refresh();
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
                    if( $('#cart-body .cart-domain-item').length == 0 ){
                        $('.cart-fixed').slideUp();
                    }else{
                        $('.cart-fixed, .cart-fixed .section-body').slideDown();
                    }
                    $('#loading').hide();
                },
                error: () => {
                    setTimeout(function () {
                        register_domain_scripts.refresh_cart();
                    }, 1000);
                }
            });
        },
    };
</script>