
const toggleButton = document.getElementById('toggleMenu');
const offCanvas = document.getElementById('offCanvas');
const closeButton = document.getElementById('closeMenu');
toggleButton.addEventListener('click', () => {
  offCanvas.classList.remove('hidden');
});
closeButton.addEventListener('click', () => {
  offCanvas.classList.add('hidden');
});
window.addEventListener('click', (e) => {
  if (e.target === offCanvas) {
    offCanvas.classList.add('hidden');
  }
});
$('.review-slider').slick({
  dots: true,
  infinite: true,
  speed: 300,
  slidesToShow: 2,
  slidesToScroll: 1,
  arrows: false,
  autoplay: true,
  autoplaySpeed: 2000,
  responsive: [
      {
          breakpoint: 768,
          settings: {
              slidesToShow: 1,
          }
      },
  ]
});