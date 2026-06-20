const navToggle = document.querySelector(".nav-toggle");
const siteNav = document.querySelector("#site-nav");
const navBackdrop = document.querySelector(".nav-backdrop");

const setNavOpen = (isOpen) => {
  if (!siteNav) {
    return;
  }

  const labels = window.dbrBusiness?.messages || {};

  siteNav.classList.toggle("is-open", isOpen);
  navToggle?.classList.toggle("is-open", isOpen);
  navToggle?.setAttribute("aria-expanded", String(isOpen));
  navToggle?.setAttribute("aria-label", isOpen ? (labels.closeMenu || "Close menu") : (labels.openMenu || "Open menu"));
  navBackdrop?.classList.toggle("is-open", isOpen);
  document.documentElement.classList.toggle("nav-open", isOpen);

  if (navBackdrop) {
    navBackdrop.hidden = !isOpen;
  }
};

navToggle?.addEventListener("click", () => {
  setNavOpen(!siteNav?.classList.contains("is-open"));
});

navBackdrop?.addEventListener("click", () => {
  setNavOpen(false);
});

siteNav?.addEventListener("click", (event) => {
  if (event.target instanceof Element && event.target.closest("a")) {
    setNavOpen(false);
  }
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && siteNav?.classList.contains("is-open")) {
    setNavOpen(false);
    navToggle?.focus();
  }
});

const form = document.querySelector(".quote-form");
const note = document.querySelector(".form-note");

form?.addEventListener("submit", (event) => {
  event.preventDefault();

  note?.classList.remove("is-error");

  if (!form.checkValidity()) {
    form.reportValidity();
    if (note) {
      note.textContent = window.dbrBusiness?.messages?.invalid || "Please add name, phone, job location and service before sending.";
      note.classList.add("is-error");
    }
    return;
  }

  const data = new FormData(form);
  const labels = window.dbrBusiness?.messages || {};
  const message = [
    labels.intro || "Hello, I need a CAT 226B bobcat rental quote in the UAE.",
    `${labels.name || "Name"}: ${data.get("name") || ""}`,
    `${labels.phone || "Phone"}: ${data.get("phone") || ""}`,
    `${labels.wa || "WhatsApp"}: ${data.get("whatsapp") || ""}`,
    `${labels.loc || "Location"}: ${data.get("location") || ""}`,
    `${labels.service || "Service"}: ${data.get("service") || ""}`,
    `${labels.date || "Date"}: ${data.get("date") || ""}`,
    `${labels.operator || "Operator"}: ${data.get("operator") || ""}`,
    `${labels.attach || "Attachment"}: ${data.get("attachment") || ""}`,
    `${labels.message || "Message"}: ${data.get("message") || ""}`
  ].join("\n");

  const whatsapp = (window.dbrBusiness?.whatsapp || "+971547388695").replace(/[^0-9]/g, "");
  const whatsappUrl = `https://wa.me/${whatsapp}?text=${encodeURIComponent(message)}`;
  if (note) {
    note.textContent = labels.opening || "Opening WhatsApp with your quote details.";
  }

  const opened = window.open(whatsappUrl, "_blank", "noopener,noreferrer");
  if (!opened) {
    window.location.href = whatsappUrl;
  }
});
