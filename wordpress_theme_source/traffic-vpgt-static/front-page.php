<?php
/**
 * Front page template.
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <header class="site-header d-flex align-items-center justify-content-between">
    <a class="brand d-inline-flex align-items-center" href="#top" aria-label="Trang chủ">
      <span class="brand-mark">VP</span>
      <span>
        <strong>Tra cứu VPGT</strong>
        <small>Cổng thông tin vi phạm giao thông</small>
      </span>
    </a>
    <nav class="nav d-flex align-items-center" aria-label="Điều hướng chính">
      <a href="#lookup">Tra cứu</a>
      <a href="#offenses">Mức phạt</a>
      <a href="#payment">Thanh toán</a>
      <a href="#support">Hỗ trợ</a>
    </nav>
  </header>

  <main id="top">
    <section class="lookup-hero d-flex align-items-center" id="lookup">
      <div class="hero-backdrop" aria-hidden="true"></div>
      <div class="container hero-content">
        <h1>Tra cứu vi phạm giao thông</h1>
        <p class="hero-copy">Nhập biển số xe, số GPLX, số CCCD hoặc số biên bản để xem thông tin vi phạm, bằng chứng và hướng dẫn thanh toán.</p>
        <form class="search-panel card" id="lookupForm">
          <div class="card-body">
            <label for="lookupInput">Thông tin tra cứu</label>
            <div class="form-row align-items-center">
              <div class="col-lg-10 col-md-9 mb-2 mb-md-0">
                <input id="lookupInput" class="form-control form-control-lg" type="search" placeholder="VD: 30C-104.17 hoặc BB2026-0006-0101" autocomplete="off">
              </div>
              <div class="col-lg-2 col-md-3">
                <button class="btn btn-primary btn-lg btn-block" type="submit">Tra cứu</button>
              </div>
            </div>
            <div class="quick-samples d-flex flex-wrap" id="quickSamples" aria-label="Từ khóa mẫu"></div>
          </div>
        </form>
      </div>
    </section>

    <section class="section container d-none" id="summarySection">
      <div class="summary-grid row" id="summaryGrid"></div>
    </section>

    <section class="section container d-none" id="recordsSection">
      <div class="section-heading d-flex align-items-end justify-content-between">
        <div>
          <p class="eyebrow">Kết quả</p>
          <h2>Biên bản vi phạm</h2>
        </div>
        <div class="filter-group btn-group flex-wrap" aria-label="Bộ lọc trạng thái">
          <button class="btn btn-sm filter is-active" data-filter="all" type="button">Tất cả</button>
          <button class="btn btn-sm filter" data-filter="unpaid" type="button">Chưa nộp</button>
          <button class="btn btn-sm filter" data-filter="paid" type="button">Đã nộp</button>
          <button class="btn btn-sm filter" data-filter="overdue" type="button">Quá hạn</button>
        </div>
      </div>
      <div id="resultNotice" class="result-notice alert alert-info"></div>
      <div class="records" id="recordList"></div>
    </section>

    <section class="section container" id="offenses">
      <div class="row align-items-start">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <p class="eyebrow">Danh mục</p>
          <h2>Một số lỗi vi phạm và mức phạt</h2>
        </div>
        <div class="col-lg-8">
          <div class="table-card card">
            <div class="table-responsive">
              <table class="table table-bordered table-hover mb-0">
                <thead>
                  <tr>
                    <th>Mã lỗi</th>
                    <th>Lỗi vi phạm</th>
                    <th>Mức phạt</th>
                  </tr>
                </thead>
                <tbody id="offenseTable"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section payment-band" id="payment">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <p class="eyebrow">Thanh toán</p>
            <h2>Hướng dẫn chuyển khoản</h2>
            <p>Người vi phạm chuyển khoản theo đúng số tiền trên biên bản. Nội dung chuyển khoản chính là số biên bản.</p>
          </div>
          <div class="col-lg-6">
            <div class="bank-box card card-body">
              <p><b>Thanh toán:</b> Chuyển khoản ngân hàng</p>
              <p><b>Ngân hàng:</b> ViettinBank</p>
              <p><b>Mã ngân hàng:</b> 10422511655978</p>
              <p><b>Nội dung chuyển khoản:</b> VPGT-&lt;SỐ BIÊN BẢN&gt;</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section container" id="support">
      <div class="row">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <div class="support-card card card-body h-100">
            <p class="eyebrow">Liên hệ</p>
            <h2>Gửi phản hồi</h2>
            <form id="supportForm" class="support-form">
              <input class="form-control" type="text" placeholder="Họ tên" required>
              <input class="form-control" type="tel" placeholder="Số điện thoại" required>
              <input class="form-control" type="text" placeholder="Số biên bản hoặc biển số">
              <textarea class="form-control" rows="4" placeholder="Nội dung cần hỗ trợ" required></textarea>
              <button class="btn btn-primary" type="submit">Gửi yêu cầu</button>
              <p class="form-message" id="supportMessage"></p>
            </form>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="faq-card card card-body h-100">
            <p class="eyebrow">FAQ</p>
            <h2>Câu hỏi thường gặp</h2>
            <details open>
              <summary>Có thể tra cứu bằng thông tin nào?</summary>
              <p>Biển số xe, số GPLX, số CCCD hoặc số biên bản.</p>
            </details>
            <details>
              <summary>Bằng chứng vi phạm hiển thị thế nào?</summary>
              <p>Trang hỗ trợ cả ảnh và video dạng link. Có thể mở rộng hoặc tải về khi cần.</p>
            </details>
            <details>
              <summary>Nội dung chuyển khoản ghi gì?</summary>
              <p>Ghi đúng theo mẫu VPGT-SỐ BIÊN BẢN, ví dụ VPGT-BB2026-0006-0101.</p>
            </details>
          </div>
        </div>
      </div>
    </section>
  </main>

  <dialog id="detailDialog" class="detail-dialog">
    <div class="dialog-body">
      <button class="icon-button" id="closeDialog" type="button" aria-label="Đóng">×</button>
      <div id="dialogContent"></div>
    </div>
  </dialog>

<?php wp_footer(); ?>
</body>
</html>
