{!!
ST("builder_inet_", "iNet API", [
	["type"=>"text", "name"=>"api_token", "title"=>'iNet API token (<a href="https://dms.inet.vn/rms/account/organization/setting/api" target="_blank">Click để lấy</a>)', "note"=>"", "value"=>"", "attr"=>''],
])
!!}
{!!
ST("builder_inet_", "Thiết lập tên miền", [
    ["type"=>"textarea", "name"=>"domain_check_default", "title"=>"Đuôi miền mặc định khi kiểm tra", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
    ["type"=>"textarea", "name"=>"domain_check_more", "title"=>"Cho phép kiểm tra các đuôi miền khác", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
	["type"=>"text", "name"=>"ns_1", "title"=>'NS1 mặc định', "note"=>"", "value"=>"", "attr"=>''],
	["type"=>"text", "name"=>"ns_2", "title"=>'NS2 mặc định', "note"=>"", "value"=>"", "attr"=>''],
])
!!}

{!!
ST("builder_inet_", "Chiết khấu tên miền",
    call_user_func(function (){
        $out = [];
        foreach(\models\Role::all() as $item){
            $out[] = ["type"=>"number", "name"=>"discount_".$item->id, "title"=>"% chiết khấu cho <b style='color: ".$item->color."'>".$item->label."</b>", "note"=>"", "min"=>0, "max"=>100, "value"=>0,"attr"=>''];

        }
        return $out;
    })
)
!!}

@php
    $domain_suffix = \models\InetDomainSuffix::get_suffix();
    //$getRoles = \models\Role::all();
    //dd($getRoles);
@endphp
<section class="section">
    <div class="heading">
        Thiết lập giá tên miền
    </div>
    <div class="section-body">
        <div class="alert-info">
            Để trống giá sẽ lấy giá gốc từ iNet
        </div>
        <div class="flex flex-middle menu bd-bottom">
            <div class="width-10 pd-5">
                <b>
                    Đuôi miền
                </b>
            </div>
            <div class="pd-5" style="width: 22.5%">
                <div class="">
                    <b>
                        Giá đăng ký gốc
                    </b>
                </div>
            </div>
            <div class="pd-5" style="width: 22.5%">
                <div class="">
                    <b>
                        Giá bán (Chưa VAT)
                    </b>
                </div>
            </div>
            <div class="pd-5" style="width: 22.5%">
                <div class="">
                    <b>
                        Giá bán (Có VAT)
                    </b>
                </div>
            </div>
            <div class="pd-5" style="width: 22.5%">
                <div class="">
                    <b>
                        Giá gia hạn
                    </b>
                </div>
            </div>
        </div>
        @foreach( $domain_suffix as $item )
            <div class="flex flex-medium flex-middle menu bd-bottom">
            <div class="width-10 pd-5">
                <b>
                    .{{ $item->suffix }}
                </b>
            </div>
                <div class="pd-5" style="width: 22.5%">
                    <div style="color: green;">
                        <b>{{ number_format($item->reg_price) }}</b>
                    </div>
                </div>
            <div class="pd-5" style="width: 22.5%">
                <div class="">
                    <input value="{{ Storage::setting("builder_inet_reg_price_{$item->suffix}") }}" name="storage[setting][builder_inet_reg_price_{{ $item->suffix }}]" class="width-100 input" placeholder="Giá bán (Chưa VAT)" type="text" onchange="inputCurrency(this)" onkeyup="inputCurrency(this)">
                </div>
                <div style="padding-top: 10px; color: green; font-size: small">
                    <b>{{ number_format($item->reg_price) }}</b>
                </div>
            </div>
            <div class="pd-5" style="width: 22.5%">
                <div class="">
                    <input value="{{ Storage::setting("builder_inet_reg_price_{$item->suffix}_vat") }}" name="storage[setting][builder_inet_reg_price_{{ $item->suffix }}_vat]" class="width-100 input" placeholder="Giá bán (Có VAT)" type="text" onchange="inputCurrency(this)" onkeyup="inputCurrency(this)">
                </div>
                <div style="padding-top: 10px; color: green; font-size: small">
                    <b>{{ number_format($item->reg_price_vat) }}</b>
                </div>
            </div>
                <div class="pd-5" style="width: 22.5%">
                    <div class="">
                        <input value="{{ Storage::setting("builder_inet_renew_price_{$item->suffix}") }}" name="storage[setting][builder_inet_renew_price_{{ $item->suffix }}]" class="width-100 input" placeholder="Giá gia hạn" type="text" onchange="inputCurrency(this)" onkeyup="inputCurrency(this)">
                    </div>
                    <div style="padding-top: 10px; color: green; font-size: small">
                        <b>{{ number_format($item->renew_price) }}</b>
                    </div>
                </div>
        </div>
        @endforeach
    </div>
</section>