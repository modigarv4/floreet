document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector("form");
  if (!form) return;

  const button = form.querySelector("button[type='submit']");
  if (!button) return;

  const text = button.querySelector(".btn-text");
  const loader = button.querySelector(".dots-container");

  form.addEventListener("submit", () => {
    if (button.disabled) return;
    if (text && loader) {
      text.style.display = "none";
      loader.style.display = "flex";
    }
    button.disabled = true;
  });
});
