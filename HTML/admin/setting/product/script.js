const productModal = document.getElementById("productModal");
const openProductModal = document.getElementById("openProductModal");
const closeProductModal = document.getElementById("closeProductModal");
const cancelProductModal = document.getElementById("cancelProductModal");
const productForm = document.getElementById("productForm");
const searchProduct = document.getElementById("searchProduct");
const resetProductFilter = document.getElementById("resetProductFilter");
const refreshProductTable = document.getElementById("refreshProductTable");
const productRows = Array.from(document.querySelectorAll("#productTableBody tr"));

function toggleProductModal(show) {
  productModal.hidden = !show;
}

function filterProducts(keyword) {
  const normalized = keyword.trim().toLowerCase();
  productRows.forEach((row) => {
    const searchText = row.getAttribute("data-search") || "";
    row.hidden = Boolean(normalized) && !searchText.includes(normalized);
  });
}

openProductModal.addEventListener("click", () => toggleProductModal(true));
closeProductModal.addEventListener("click", () => toggleProductModal(false));
cancelProductModal.addEventListener("click", () => toggleProductModal(false));

searchProduct.addEventListener("input", () => {
  filterProducts(searchProduct.value);
});

resetProductFilter.addEventListener("click", () => {
  searchProduct.value = "";
  filterProducts("");
});

refreshProductTable.addEventListener("click", () => {
  window.alert("Tabel produk direfresh pada versi HTML statis.");
});

document.querySelectorAll("[data-edit-product]").forEach((button) => {
  button.addEventListener("click", () => {
    toggleProductModal(true);
  });
});

document.querySelectorAll("[data-delete-product]").forEach((button) => {
  button.addEventListener("click", () => {
    window.alert("Aksi hapus perlu dihubungkan ke backend saat implementasi.");
  });
});

productForm.addEventListener("submit", (event) => {
  event.preventDefault();
  toggleProductModal(false);
  window.alert("Form produk versi HTML sudah siap disambungkan ke endpoint API.");
});
