document.addEventListener('DOMContentLoaded', function () {
     var swiper = new Swiper(".swiper", {
         loop: true,
         effect: "coverflow",
         slidesPerView: "auto",
         centeredSlides: true,
         grabCursor: true,
         coverflowEffect: {
             rotate: 0,
             stretch: 0,
             depth: 100,
             modifier: 2,
             slideShadows: true,
         },
         pagination: {
             el: ".swiper-pagination",
             clickable: true,
         },
         autoplay: {
             delay: 3000,
             disableOnInteraction: false,
         },
     });
 });
 
