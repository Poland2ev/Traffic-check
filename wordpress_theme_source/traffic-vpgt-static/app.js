const money = new Intl.NumberFormat("vi-VN", {
  style: "currency",
  currency: "VND",
  maximumFractionDigits: 0
});

const dateTime = new Intl.DateTimeFormat("vi-VN", {
  day: "2-digit",
  month: "2-digit",
  year: "numeric",
  hour: "2-digit",
  minute: "2-digit"
});

const dateOnly = new Intl.DateTimeFormat("vi-VN", {
  day: "2-digit",
  month: "2-digit",
  year: "numeric"
});

const state = {
  query: "",
  filter: "all",
  records: RECORDS
};

const offenseMap = new Map(OFFENSES.map(item => [item.code, item]));
const lookupForm = document.querySelector("#lookupForm");
const lookupInput = document.querySelector("#lookupInput");
const quickSamples = document.querySelector("#quickSamples");
const recordList = document.querySelector("#recordList");
const resultNotice = document.querySelector("#resultNotice");
const summaryGrid = document.querySelector("#summaryGrid");
const summarySection = document.querySelector("#summarySection");
const recordsSection = document.querySelector("#recordsSection");
const offenseTable = document.querySelector("#offenseTable");
const detailDialog = document.querySelector("#detailDialog");
const dialogContent = document.querySelector("#dialogContent");

function recordTotal(record) {
  return record.items.reduce((sum, code) => sum + (offenseMap.get(code)?.fine || 0), 0);
}

function isOverdue(record) {
  const today = new Date("2026-07-29T00:00:00");
  return record.status !== "paid" && new Date(record.dueDate + "T23:59:59") < today;
}

function statusLabel(record) {
  if (record.status === "paid") return { text: "Đã nộp", className: "badge-success" };
  if (isOverdue(record)) return { text: "Quá hạn", className: "badge-danger" };
  return { text: "Chưa nộp", className: "badge-warning" };
}

function normalize(value) {
  return (value || "").toString().toLowerCase().trim();
}

function matchesQuery(record, query) {
  if (!query) return false;
  return normalize(record.plateNo) === normalize(query);
}

function matchesFilter(record, filter) {
  if (filter === "all") return true;
  if (filter === "paid") return record.status === "paid";
  if (filter === "unpaid") return record.status !== "paid";
  if (filter === "overdue") return isOverdue(record);
  return true;
}

function filteredRecords() {
  return state.records.filter(record => matchesQuery(record, state.query) && matchesFilter(record, state.filter));
}

function hasExactPlateQuery() {
  return RECORDS.some(record => normalize(record.plateNo) === normalize(state.query));
}

function renderSummary() {
  const source = filteredRecords();
  const total = source.length;
  const unpaid = source.filter(record => record.status !== "paid").length;
  const overdue = source.filter(isOverdue).length;
  const amount = source.filter(record => record.status !== "paid").reduce((sum, record) => sum + recordTotal(record), 0);

  summaryGrid.innerHTML = [
    ["Tổng biên bản", total],
    ["Chưa nộp phạt", unpaid],
    ["Quá hạn", overdue],
    ["Tổng tiền chưa nộp", money.format(amount)]
  ].map(([label, value]) => `
    <div class="col-lg-3 col-md-6 mb-3">
      <article class="summary-card card card-body h-100">
        <span>${label}</span>
        <strong>${value}</strong>
      </article>
    </div>
  `).join("");
}

function renderSamples() {
  ["30C-104.17", "29F-113.17", "51N-133.31", "30L-191.17"].forEach(sample => {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-outline-light";
    button.textContent = sample;
    button.addEventListener("click", () => {
      lookupInput.value = sample;
      state.query = sample;
      renderRecords();
      renderSummary();
      document.querySelector("#lookup").scrollIntoView({ behavior: "smooth", block: "start" });
    });
    quickSamples.appendChild(button);
  });
}

function renderOffenseTable() {
  offenseTable.innerHTML = OFFENSES.map(item => `
    <tr>
      <td>${item.code}</td>
      <td>${item.name}</td>
      <td>${money.format(item.fine)}</td>
    </tr>
  `).join("");
}

function renderRecords() {
  const canShowRecords = hasExactPlateQuery();
  summarySection.classList.toggle("d-none", !canShowRecords);
  recordsSection.classList.toggle("d-none", !canShowRecords);

  if (!canShowRecords) {
    recordList.innerHTML = "";
    if (state.query) {
      resultNotice.textContent = "Không tìm thấy biển số xe phù hợp. Vui lòng nhập đúng biển số xe để xem biên bản.";
      recordsSection.classList.remove("d-none");
    }
    return;
  }

  const rows = filteredRecords();
  resultNotice.textContent = `Tìm thấy ${rows.length} biên bản theo biển số "${state.query}".`;

  if (rows.length === 0) {
    recordList.innerHTML = `
      <article class="record-card card card-body">
        <div>
          <h3>Không tìm thấy dữ liệu</h3>
          <p class="muted">Hãy thử nhập biển số xe khác.</p>
        </div>
      </article>
    `;
    return;
  }

  recordList.innerHTML = rows.map(record => {
    const status = statusLabel(record);
    return `
      <article class="record-card card card-body">
        <div>
          <h3>${record.ticketNo}</h3>
          <div class="meta-line">
            <span>${record.ownerName}</span>
            <span>${record.plateNo}</span>
            <span>${dateTime.format(new Date(record.time))}</span>
          </div>
        </div>
        <div>
          <span class="badge ${status.className}">${status.text}</span>
          <p class="amount">${money.format(recordTotal(record))}</p>
          <p class="muted">Hạn nộp: ${dateOnly.format(new Date(record.dueDate))}</p>
        </div>
        <div class="record-actions">
          <button class="btn btn-primary" type="button" data-detail="${record.ticketNo}">Chi tiết</button>
          <button class="btn btn-outline-primary secondary" type="button" data-print="${record.ticketNo}">In hướng dẫn</button>
        </div>
      </article>
    `;
  }).join("");
}

function evidenceMarkup(record) {
  if (record.evidenceType === "video") {
    return `<video class="evidence-media rounded border" src="${record.evidenceUrl}" controls></video>`;
  }
  return `<a href="${record.evidenceUrl}" target="_blank"><img class="evidence-media img-fluid rounded border" src="${record.evidenceUrl}" alt="Bằng chứng vi phạm"></a>`;
}

function detailMarkup(record) {
  const status = statusLabel(record);
  const rows = record.items.map(code => {
    const item = offenseMap.get(code);
    return `<tr><td>${item.code}</td><td>${item.name}</td><td>${money.format(item.fine)}</td></tr>`;
  }).join("");

  return `
    <div class="row">
      <div class="col-lg-7 mb-4 mb-lg-0">
        <p class="eyebrow">Chi tiết biên bản</p>
        <h2>${record.ticketNo}</h2>
        <dl class="detail-list">
          <dt>Người vi phạm</dt><dd>${record.ownerName}</dd>
          <dt>Biển số xe</dt><dd>${record.plateNo}</dd>
          <dt>Số GPLX</dt><dd>${record.licenseNo}</dd>
          <dt>Số CCCD</dt><dd>${record.citizenId}</dd>
          <dt>Phương tiện</dt><dd>${record.vehicle}</dd>
          <dt>Thời gian</dt><dd>${dateTime.format(new Date(record.time))}</dd>
          <dt>Địa điểm</dt><dd>${record.location}</dd>
          <dt>Cán bộ xử lý</dt><dd>${record.officer} (${record.officerId})</dd>
          <dt>Trạng thái</dt><dd><span class="badge ${status.className}">${status.text}</span></dd>
          <dt>Hạn nộp</dt><dd>${dateOnly.format(new Date(record.dueDate))}</dd>
        </dl>
      </div>
      <div class="col-lg-5">
        <h3>Bằng chứng</h3>
        ${evidenceMarkup(record)}
        <p class="mt-2"><a class="btn btn-outline-primary btn-sm" href="${record.evidenceUrl}" target="_blank">Mở bằng chứng</a></p>
      </div>
    </div>
    <hr>
    <div class="table-card card table-responsive">
      <table class="table table-bordered table-hover mb-0">
        <thead><tr><th>Mã lỗi</th><th>Lỗi vi phạm</th><th>Mức phạt</th></tr></thead>
        <tbody>${rows}</tbody>
        <tfoot><tr><th colspan="2">Tổng cộng</th><th>${money.format(recordTotal(record))}</th></tr></tfoot>
      </table>
    </div>
    <div class="payment-note alert alert-info mt-3">
      <p><b>Thanh toán:</b> Chuyển khoản ngân hàng</p>
      <p><b>Ngân hàng:</b> ViettinBank</p>
      <p><b>Mã ngân hàng:</b> 10422511655978</p>
      <p><b>Nội dung chuyển khoản:</b> VPGT-${record.ticketNo}</p>
    </div>
  `;
}

function openDetail(ticketNo) {
  const record = RECORDS.find(item => item.ticketNo === ticketNo);
  if (!record) return;
  dialogContent.innerHTML = detailMarkup(record);
  detailDialog.showModal();
}

function printRecord(ticketNo) {
  const record = RECORDS.find(item => item.ticketNo === ticketNo);
  if (!record) return;
  const adminlteHref = document.querySelector("#traffic-vpgt-adminlte-css")?.href || "";
  const themeHref = document.querySelector("#traffic-vpgt-style-css")?.href || "";
  const win = window.open("", "_blank", "width=1000,height=800");
  win.document.write(`
    <html lang="vi">
      <head>
        <meta charset="utf-8">
        <title>Hướng dẫn thanh toán ${record.ticketNo}</title>
        ${adminlteHref ? `<link rel="stylesheet" href="${adminlteHref}">` : ""}
        ${themeHref ? `<link rel="stylesheet" href="${themeHref}">` : ""}
      </head>
      <body>
        <main class="container section">${detailMarkup(record)}</main>
      </body>
    </html>
  `);
  win.document.close();
  setTimeout(() => win.print(), 400);
}

lookupForm.addEventListener("submit", event => {
  event.preventDefault();
  state.query = lookupInput.value.trim();
  renderRecords();
  renderSummary();
});

document.querySelectorAll(".filter").forEach(button => {
  button.addEventListener("click", () => {
    document.querySelectorAll(".filter").forEach(item => item.classList.remove("is-active"));
    button.classList.add("is-active");
    state.filter = button.dataset.filter;
    renderRecords();
    renderSummary();
  });
});

recordList.addEventListener("click", event => {
  const detailButton = event.target.closest("[data-detail]");
  const printButton = event.target.closest("[data-print]");
  if (detailButton) openDetail(detailButton.dataset.detail);
  if (printButton) printRecord(printButton.dataset.print);
});

document.querySelector("#closeDialog").addEventListener("click", () => detailDialog.close());
detailDialog.addEventListener("click", event => {
  if (event.target === detailDialog) detailDialog.close();
});

document.querySelector("#supportForm").addEventListener("submit", event => {
  event.preventDefault();
  document.querySelector("#supportMessage").textContent = "Yêu cầu hỗ trợ đã được ghi nhận trong bản demo.";
  event.currentTarget.reset();
});

renderSummary();
renderSamples();
renderOffenseTable();
renderRecords();
