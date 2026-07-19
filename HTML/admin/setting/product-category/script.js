const categoryModal = document.getElementById("categoryModal");
const openCategoryModal = document.getElementById("openCategoryModal");
const closeCategoryModal = document.getElementById("closeCategoryModal");
const cancelCategoryModal = document.getElementById("cancelCategoryModal");
const categoryForm = document.getElementById("categoryForm");
const categoryName = document.getElementById("categoryName");
const categoryCode = document.getElementById("categoryCode");
const categorySearch = document.getElementById("categorySearch");
const categorySearchForm = document.getElementById("categorySearchForm");
const resetCategorySearch = document.getElementById("resetCategorySearch");
const categoryRows = Array.from(document.querySelectorAll("#categoryTableBody tr"));

function slugify(value) {
  return value
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
}

function toggleCategoryModal(show) {
  categoryModal.hidden = !show;
}

function filterCategoryRows(keyword) {
  const normalized = keyword.trim().toLowerCase();
  categoryRows.forEach((row) => {
    const haystack = row.getAttribute("data-search") || "";
    row.hidden = Boolean(normalized) && !haystack.includes(normalized);
  });
}

openCategoryModal.addEventListener("click", () => toggleCategoryModal(true));
closeCategoryModal.addEventListener("click", () => toggleCategoryModal(false));
cancelCategoryModal.addEventListener("click", () => toggleCategoryModal(false));

categoryName.addEventListener("input", () => {
  if (!categoryCode.dataset.manual) {
    categoryCode.value = slugify(categoryName.value);
  }
});

categoryCode.addEventListener("input", () => {
  categoryCode.dataset.manual = "true";
});

categorySearchForm.addEventListener("submit", (event) => {
  event.preventDefault();
  filterCategoryRows(categorySearch.value);
});

resetCategorySearch.addEventListener("click", () => {
  categorySearch.value = "";
  filterCategoryRows("");
});

document.querySelectorAll("[data-edit-category]").forEach((button) => {
  button.addEventListener("click", () => toggleCategoryModal(true));
});

document.querySelectorAll("[data-delete-category]").forEach((button) => {
  button.addEventListener("click", () => {
    window.alert("Aksi hapus kategori perlu dihubungkan ke API saat implementasi.");
  });
});

categoryForm.addEventListener("submit", (event) => {
  event.preventDefault();
  toggleCategoryModal(false);
  window.alert("Form kategori versi HTML sudah siap untuk dihubungkan ke backend.");
});
