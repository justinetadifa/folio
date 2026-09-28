"use strict";
const menu = document.querySelector("[data-menu]");
const toggle = document.querySelector("[data-menu-toggle]");
const backdrop = document.querySelector("[data-backdrop]");
const compactNavigation = window.matchMedia("(max-width: 900px)");
function syncNavigation() {
    if (menu)
        menu.inert =
            compactNavigation.matches && !menu.classList.contains("is-open");
}
function closeMenu() {
    menu?.classList.remove("is-open");
    backdrop?.setAttribute("hidden", "");
    toggle?.setAttribute("aria-expanded", "false");
    syncNavigation();
}
syncNavigation();
compactNavigation.addEventListener("change", closeMenu);
toggle?.addEventListener("click", () => {
    const open = menu.classList.toggle("is-open");
    backdrop.hidden = !open;
    toggle.setAttribute("aria-expanded", String(open));
    syncNavigation();
});
backdrop?.addEventListener("click", closeMenu);
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeMenu();
});
const dialog = document.querySelector("#confirmation-dialog");
let pendingForm = null;
let trigger = null;
document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (event) => {
        if (form.dataset.confirmed === "true") return;
        if (!dialog?.showModal) {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
            return;
        }
        event.preventDefault();
        pendingForm = form;
        trigger = document.activeElement;
        dialog.querySelector("[data-confirm-message]").textContent =
            form.dataset.confirm;
        dialog.querySelector("[data-confirm-button]").textContent =
            form.dataset.confirmLabel || "Confirm";
        dialog.showModal();
    });
});
document
    .querySelector("[data-cancel-confirm]")
    ?.addEventListener("click", () => dialog.close());
document
    .querySelector("[data-confirm-button]")
    ?.addEventListener("click", () => {
        const form = pendingForm;
        dialog.close();
        if (form) {
            form.dataset.confirmed = "true";
            form.requestSubmit();
        }
    });
dialog?.addEventListener("close", () => {
    trigger?.focus();
    pendingForm = null;
});
