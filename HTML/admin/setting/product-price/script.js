const priceModal = document.getElementById("priceModal");
const openPriceModal = document.getElementById("openPriceModal");
const closePriceModal = document.getElementById("closePriceModal");
const cancelPriceModal = document.getElementById("cancelPriceModal");
const priceForm = document.getElementById("priceForm");
const applyPriceFilter = document.getElementById("applyPriceFilter");
const resetPriceFilter = document.getElementById("resetPriceFilter");
const priceCategoryFilter = document.getElementById("priceCategoryFilter");
const modalProduct = document.getElementById("modalProduct");
const modalCategory = document.getElementById("modalCategory");
const priceRows = Array.from(document.querySelectorAll("#priceTableBody tr"));

const categoryOptionsByProduct = {
  riset: [{ value: "retail", label: "Retail Store" }],
  fgd: [{ value: "komunitas", label: "Komunitas Online" }],
  audit: [{ value: "audit", label: "Audit Outlet" }],
};

function togglePriceModal(show) {
  priceModal.hidden = !show;
}

function filterPriceRows(category) {
  priceRows.forEach((row) => {
    row.hidden = Boolean(category) && row.dataset.category !== category;
  });
}

function renderCategoryOptions(productId) {
  modalCategory.innerHTML = '<option value="">Pilih kategori</option>';
  const options = categoryOptionsByProduct[productId] || [];
  options.forEach((option) => {
    const element = document.createElement("option");
    element.value = option.value;
    element.textContent = option.label;
    modalCategory.appendChild(element);
  });
}

openPriceModal.addEventListener("click", () => togglePriceModal(true));
closePriceModal.addEventListener("click", () => togglePriceModal(false));
cancelPriceModal.addEventListener("click", () => togglePriceModal(false));

applyPriceFilter.addEventListener("click", () => {
  filterPriceRows(priceCategoryFilter.value);
});

resetPriceFilter.addEventListener("click", () => {
  priceCategoryFilter.value = "";
  filterPriceRows("");
});

modalProduct.addEventListener("change", () => {
  renderCategoryOptions(modalProduct.value);
});

document.querySelectorAll("[data-edit-price]").forEach((button) => {
  button.addEventListener("click", () => togglePriceModal(true));
});

document.querySelectorAll("[data-delete-price]").forEach((button) => {
  button.addEventListener("click", () => {
    window.alert("Aksi hapus harga perlu dihubungkan ke endpoint backend.");
  });
});

priceForm.addEventListener("submit", (event) => {
  event.preventDefault();

  const detailsField = document.getElementById("priceDetails");
  if (detailsField.value.trim()) {
    try {
      JSON.parse(detailsField.value);
    } catch (error) {
      window.alert("Format JSON pada detail harga belum valid.");
      return;
    }
  }

  togglePriceModal(false);
  window.alert("Form harga versi HTML sudah siap disambungkan ke backend.");
});
