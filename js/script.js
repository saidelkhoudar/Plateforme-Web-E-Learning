
const images = [
  "images/slideshow/Image 1.png",
  "images/slideshow/Image 2.png",
  "images/slideshow/Image 3.png"
];

let current = 0;
let slideshowStarted = false;

function startSlideshow() {
  if (slideshowStarted) return;
  slideshowStarted = true;

  setInterval(() => {
    const img = document.getElementById("slideshow-image");
    img.style.opacity = 0;

    setTimeout(() => {
      current = (current + 1) % images.length;
      img.src = images[current];
      img.style.opacity = 1;
    }, 300);
  }, 1500);
}

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        document.getElementById("slideshow-section").classList.add("visible");
        startSlideshow();
      }
    });
  },
  { threshold: 0.3 }
);

observer.observe(document.getElementById("slideshow-section"));

