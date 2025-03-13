
    document.addEventListener("DOMContentLoaded", function () {
        const slides = document.querySelectorAll(".slide");
        let index = 0;

        function showSlide(i) {
            slides.forEach((slide, j) => {
                slide.classList.remove("active");
                if (j === i) slide.classList.add("active");
            });
        }

        function nextSlide() {
            index = (index + 1) % slides.length;
            showSlide(index);
        }

        setInterval(nextSlide, 3000);
    });

