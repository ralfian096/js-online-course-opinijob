const registerForm = document.getElementById("registerForm");
const submitRegister = document.getElementById("submitRegister");
const verifyPhoneButton = document.getElementById("verifyPhoneButton");
const phoneStatus = document.getElementById("phoneStatus");
const otpModal = document.getElementById("otpModal");
const closeOtpModal = document.getElementById("closeOtpModal");
const verifyOtpButton = document.getElementById("verifyOtpButton");
const resendOtpButton = document.getElementById("resendOtpButton");
const otpPhoneLabel = document.getElementById("otpPhoneLabel");
const otpTimer = document.getElementById("otpTimer");
const formAlert = document.querySelector(".form-alert");

const emailInput = document.getElementById("email");
const phoneInput = document.getElementById("phoneNational");
const passwordInput = document.getElementById("password");
const passwordConfirmationInput = document.getElementById("passwordConfirmation");
const otpInputs = Array.from(document.querySelectorAll(".otp-inputs input"));

let phoneVerified = false;
let otpCountdown = 180;
let otpIntervalId = null;

function setError(key, message) {
    const target = document.querySelector(`[data-error-for="${key}"]`);
    if (target) {
        target.textContent = message || "";
    }
}

function sanitizeDigits(value) {
    return value.replace(/\D/g, "");
}

function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
}

function getPasswordStrength(value) {
    return {
        minLength: value.length >= 8,
        upperLower: /[a-z]/.test(value) && /[A-Z]/.test(value),
        special: /[^A-Za-z0-9]/.test(value),
    };
}

function renderPasswordChecklist() {
    const passwordRules = getPasswordStrength(passwordInput.value);
    document.querySelectorAll(".password-checklist li").forEach((item) => {
        const rule = item.getAttribute("data-rule");
        item.classList.toggle("is-valid", Boolean(passwordRules[rule]));
    });
}

function validateForm() {
    const emailOk = isValidEmail(emailInput.value);
    const phoneDigits = sanitizeDigits(phoneInput.value);
    const phoneOk = phoneDigits.startsWith("8") && phoneDigits.length >= 9 && phoneDigits.length <= 13;
    const passwordRules = getPasswordStrength(passwordInput.value);
    const passwordOk = Object.values(passwordRules).every(Boolean);
    const confirmationOk = passwordInput.value && passwordInput.value === passwordConfirmationInput.value;

    setError("email", emailInput.value && !emailOk ? "Format email tidak valid." : "");
    setError("phone", phoneInput.value && !phoneOk ? "Nomor telepon harus diawali 8 dan berisi 9-13 digit." : "");
    setError(
        "passwordConfirmation",
        passwordConfirmationInput.value && !confirmationOk ? "Konfirmasi password tidak sama." : "",
    );

    submitRegister.disabled = !(emailOk && phoneOk && passwordOk && confirmationOk && phoneVerified);
}

function updatePhoneStatus() {
    phoneStatus.textContent = phoneVerified ? "Terverifikasi" : "Belum terverifikasi";
    phoneStatus.classList.toggle("is-verified", phoneVerified);
}

function formatOtpTimer() {
    const minutes = String(Math.floor(otpCountdown / 60)).padStart(2, "0");
    const seconds = String(otpCountdown % 60).padStart(2, "0");
    otpTimer.textContent = `Kirim ulang dalam ${minutes}:${seconds}`;
}

function stopOtpTimer() {
    if (otpIntervalId) {
        clearInterval(otpIntervalId);
        otpIntervalId = null;
    }
}

function startOtpTimer() {
    stopOtpTimer();
    otpCountdown = 180;
    formatOtpTimer();
    otpIntervalId = window.setInterval(() => {
        otpCountdown -= 1;
        if (otpCountdown <= 0) {
            stopOtpTimer();
            otpTimer.textContent = "Anda dapat mengirim ulang OTP sekarang.";
            return;
        }
        formatOtpTimer();
    }, 1000);
}

function openOtpModal() {
    const fullPhone = `+62${sanitizeDigits(phoneInput.value)}`;
    otpPhoneLabel.textContent = fullPhone;
    otpModal.hidden = false;
    otpInputs.forEach((input) => {
        input.value = "";
    });
    verifyOtpButton.disabled = true;
    startOtpTimer();
}

function closeModal() {
    otpModal.hidden = true;
}

document.querySelectorAll("[data-toggle-password]").forEach((button) => {
    button.addEventListener("click", () => {
        const selector = button.getAttribute("data-toggle-password");
        const input = selector ? document.querySelector(selector) : null;
        if (!(input instanceof HTMLInputElement)) {
            return;
        }
        input.type = input.type === "password" ? "text" : "password";
        button.textContent = input.type === "password" ? "Tampilkan" : "Sembunyikan";
    });
});

phoneInput.addEventListener("input", () => {
    phoneInput.value = sanitizeDigits(phoneInput.value);
    phoneVerified = false;
    updatePhoneStatus();
    validateForm();
});

[emailInput, passwordInput, passwordConfirmationInput].forEach((input) => {
    input.addEventListener("input", () => {
        renderPasswordChecklist();
        validateForm();
    });
});

verifyPhoneButton.addEventListener("click", () => {
    validateForm();
    if (sanitizeDigits(phoneInput.value).length < 9) {
        return;
    }
    openOtpModal();
});

closeOtpModal.addEventListener("click", closeModal);

resendOtpButton.addEventListener("click", () => {
    startOtpTimer();
});

otpInputs.forEach((input, index) => {
    input.addEventListener("input", () => {
        input.value = sanitizeDigits(input.value).slice(0, 1);
        if (input.value && otpInputs[index + 1]) {
            otpInputs[index + 1].focus();
        }
        verifyOtpButton.disabled = otpInputs.some((item) => item.value.length !== 1);
    });
});

verifyOtpButton.addEventListener("click", () => {
    phoneVerified = true;
    updatePhoneStatus();
    validateForm();
    closeModal();
});

registerForm.addEventListener("submit", (event) => {
    validateForm();
    if (submitRegister.disabled) {
        event.preventDefault();
        return;
    }
    event.preventDefault();
    if (formAlert instanceof HTMLElement) {
        formAlert.hidden = false;
        formAlert.textContent = "Konversi HTML berhasil. Sambungkan form ini ke endpoint backend saat implementasi.";
    }
});

renderPasswordChecklist();
updatePhoneStatus();
validateForm();
