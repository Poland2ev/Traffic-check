<header class="public-nav">
    <a class="public-brand" href="<?php echo base_url ?>">
        <span class="public-brand-mark">VP</span>
        <span>
            <strong>Tra cứu VPGT</strong>
            <small>Cổng thông tin vi phạm giao thông</small>
        </span>
    </a>
    <a class="login-link" href="<?php echo base_url.'admin' ?>">Đăng nhập</a>
</header>

<main>
    <section class="dynamic-lookup-hero" id="main-header">
        <div class="dynamic-lookup-inner">
            <h1>Tra cứu vi phạm giao thông</h1>
            <p>Nhập biển số xe, số GPLX, số CCCD hoặc số biên bản để xem thông tin vi phạm, bằng chứng và hướng dẫn thanh toán.</p>
            <form action="<?php echo base_url ?>" method="get" class="dynamic-search-panel">
                <input type="hidden" name="p" value="lookup">
                <label for="publicLookup">Thông tin tra cứu</label>
                <div class="dynamic-search-row">
                    <input type="text" id="publicLookup" name="q" placeholder="VD: 30C-104.17 hoặc BB2026-0006-0101" required>
                    <button type="submit"><i class="fa fa-search"></i> Tra cứu</button>
                </div>
                <div class="dynamic-samples">
                    <a href="<?php echo base_url ?>?p=lookup&q=30C-104.17"><span></span><strong>30C-104.17</strong></a>
                    <a href="<?php echo base_url ?>?p=lookup&q=GPLX-720010"><span></span><strong>GPLX-720010</strong></a>
                    <a href="<?php echo base_url ?>?p=lookup&q=050260000022"><span></span><strong>050260000022</strong></a>
                    <a href="<?php echo base_url ?>?p=lookup&q=BB2026-0006-0101"><span></span><strong>BB2026-0006-0101</strong></a>
                </div>
            </form>
        </div>
    </section>
</main>
