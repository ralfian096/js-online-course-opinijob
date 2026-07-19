document.querySelectorAll("[data-toggle-password]").forEach((button) => {
  button.addEventListener("click", () => {
    const selector = button.getAttribute("data-toggle-password");
    const input = selector ? document.querySelector(selector) : null;

    if (!(input instanceof HTMLInputElement)) {
      return;
    }

    const nextType = input.type === "password" ? "text" : "password";
    input.type = nextType;
    button.textContent = nextType === "password" ? "Tampilkan" : "Sembunyikan";
  });
});

const loginForm = document.getElementById("loginForm");
const loginAlert = document.querySelector(".auth-alert");

if (loginForm instanceof HTMLFormElement && loginAlert instanceof HTMLElement) {
  loginForm.addEventListener("submit", (event) => {
    if (!loginForm.checkValidity()) {
      return;
    }

    event.preventDefault();
    loginAlert.hidden = false;
  });
}
