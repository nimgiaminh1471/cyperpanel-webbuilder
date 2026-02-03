<?php
    $data = \models\InetDomainSuffix::get_suffix();
?>
<section class="bg" style="margin-bottom: 20px; padding: 30px 0; text-align: center">
    <form class="input-check-domain" action="/admin/RegisterDomain" method="GET">
        <input type="text" name="domain" class="input" placeholder="Nhập tên miền"><input type="submit" class="btn-primary" value="Kiểm tra">
    </form>
</section>
<main class="flex flex-large">
    @foreach(['vn' => 'Bảng giá tên miền Việt Nam', 'global' => 'Bảng giá tên miền quốc tế'] as $type => $name)
        <section class="width-50 flex-margin">
            <section class="section">
                <div class="section-heading">
                    {{ $name }}
                </div>
                <div class="section-body">
                    <table class="table table-border table-hover width-100">
                        <thead>
                        <tr>
                            <th style="width: 120px">
                                Đuôi
                            </th>
                            <th>
                                Chi phí năm đầu
                            </th>
                            <th>
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
    @media (min-width: 767px) {
        .input-check-domain > .input{
            width: 320px;
            box-shadow: none !important;
        }
    }
</style>
