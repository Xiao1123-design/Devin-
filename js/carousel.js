let currentSlide = 0;
const slideWidth = 300; // Width of each slide + margin
const slidesToShow = 3; // Number of slides visible at once

function moveCarousel(direction) {
    const carousel = document.getElementById('productCarousel');
    const slides = carousel.children;
    const maxSlide = slides.length - slidesToShow;
    
    currentSlide = Math.max(0, Math.min(currentSlide + direction, maxSlide));
    
    carousel.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
}

// Auto-scroll
setInterval(() => moveCarousel(1), 5000);
