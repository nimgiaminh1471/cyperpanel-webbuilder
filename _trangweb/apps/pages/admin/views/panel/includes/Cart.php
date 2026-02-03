<section class="section">
    <div class="section-heading">
        <i class="bx bx-cart-alt"></i>
        GIỎ HÀNG
    </div>
    <div class="section-body" id="cart-body">
        <?php if( $_SESSION['carts']['domain'] ?? [] ): ?>
            <table class="table table-border width-100" id="cart-domain">
                <tbody>
                <?php foreach($_SESSION['carts']['domain'] as $item): ?>
                    <tr class="cart-domain-item" data-domain="<?php echo $item; ?>">
                        <td>
                            <b>
                                <?php echo $item; ?>
                            </b>
                        </td>
                        <td style="width: 60px; text-align: center">
                            <button class="btn btn-danger btn-sm" onclick="register_domain_scripts.add_to_cart('<?php echo $item; ?>')">
                                <i class="bx bx-x"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="center pd-20">
                <a class="btn btn-gradient" href="<?php echo user('id') ? '/admin/Checkout' : 'javascript: $(\'.modal-register-box\').show()' ?>">
                    Tiến hành đăng ký
                    <i class="bx bx-chevron-right"></i>
                </a>
            </div>
        <?php else: ?>
            Chưa có tên miền nào trong giỏ hàng, hãy nhập tên miền bạn muốn đăng ký!
        <?php endif; ?>
    </div>
</section>