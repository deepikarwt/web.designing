const nav = document.querySelector("#primary-nav");
const menuToggle = document.querySelector(".menu-toggle");

menuToggle.addEventListener("click", () => {
  const isOpen = menuToggle.getAttribute("aria-expanded") === "true";
  menuToggle.setAttribute("aria-expanded", String(!isOpen));
  nav.classList.toggle("open", !isOpen);
});

nav.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    nav.classList.remove("open");
    menuToggle.setAttribute("aria-expanded", "false");
  });
});

const slides = [...document.querySelectorAll(".slide")];
const dots = document.querySelector(".dots");
let currentSlide = 0;
let carouselTimer;
let paused = false;

slides.forEach((slide, index) => {
  const button = document.createElement("button");
  button.setAttribute("aria-label", `Show slide ${index + 1}`);
  button.addEventListener("click", () => showSlide(index));
  dots.append(button);
});

function showSlide(index) {
  slides[currentSlide].classList.remove("active");
  slides[currentSlide].setAttribute("aria-hidden", "true");

  currentSlide = (index + slides.length) % slides.length;

  slides[currentSlide].classList.add("active");
  slides[currentSlide].setAttribute("aria-hidden", "false");

  [...dots.children].forEach((button, buttonIndex) => {
    button.setAttribute("aria-current", String(buttonIndex === currentSlide));
  });
}

function startCarousel() {
  clearInterval(carouselTimer);

  if (!paused) {
    carouselTimer = setInterval(() => {
      showSlide(currentSlide + 1);
    }, 6000);
  }
}

document.querySelector(".next").addEventListener("click", () => {
  showSlide(currentSlide + 1);
  startCarousel();
});

document.querySelector(".prev").addEventListener("click", () => {
  showSlide(currentSlide - 1);
  startCarousel();
});

document.querySelector(".pause").addEventListener("click", (event) => {
  paused = !paused;
  event.currentTarget.textContent = paused ? "Play" : "Pause";
  event.currentTarget.setAttribute(
    "aria-label",
    paused ? "Play carousel" : "Pause carousel"
  );
  startCarousel();
});

showSlide(0);
startCarousel();

const bookingForm = document.querySelector("#booking-form");

bookingForm.addEventListener("submit", (event) => {
  event.preventDefault();

  if (!bookingForm.reportValidity()) {
    return;
  }

  const customerName = bookingForm.name.value.trim();

  document.querySelector("#form-status").textContent =
    `Thanks, ${customerName}! Your table request is ready. ` +
    "This demo has not sent it to the bakery.";

  bookingForm.reset();
});

document.querySelector("#share").addEventListener("click", async () => {
  try {
    if (navigator.share) {
      await navigator.share({
        title: document.title,
        url: location.href
      });
    } else {
      await navigator.clipboard.writeText(location.href);
    }
  } catch (error) {
    // Sharing was cancelled or the clipboard is unavailable.
  }
});