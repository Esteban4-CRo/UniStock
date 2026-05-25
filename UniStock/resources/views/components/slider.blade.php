<!-- resources/views/components/slider.blade.php -->
<div class="slider-wrapper w-100" style="background:transparent; margin: 2rem 0;">
    <div class="swiper mySwiper" style="width:100%; padding-top: 20px; padding-bottom: 50px;">
        <div class="swiper-wrapper">
            <div class="swiper-slide" style="width: 300px; height: 300px; background-position: center; background-size: cover; border-radius: 15px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                <img src="{{ asset('images/1.gif') }}" class="d-block w-100 h-100 object-fit-cover" alt="Slide 1">
            </div>
            <div class="swiper-slide" style="width: 300px; height: 300px; background-position: center; background-size: cover; border-radius: 15px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                <img src="{{ asset('images/2.gif') }}" class="d-block w-100 h-100 object-fit-cover" alt="Slide 2">
            </div>
            <div class="swiper-slide" style="width: 300px; height: 300px; background-position: center; background-size: cover; border-radius: 15px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                <img src="{{ asset('images/3.gif') }}" class="d-block w-100 h-100 object-fit-cover" alt="Slide 3">
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper('.mySwiper', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        loop: true,
        coverflowEffect: {
            rotate: 0,
            stretch: 50,
            depth: 150,
            modifier: 1.5,
            slideShadows: true,
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
</script>
