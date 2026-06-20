const navToggle = document.querySelector(".nav-toggle");
const siteNav = document.querySelector("#site-nav");

navToggle?.addEventListener("click", () => {
  const isOpen = siteNav.classList.toggle("is-open");
  navToggle.setAttribute("aria-expanded", String(isOpen));
});

siteNav?.addEventListener("click", (event) => {
  if (event.target.matches("a")) {
    siteNav.classList.remove("is-open");
    navToggle?.setAttribute("aria-expanded", "false");
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
