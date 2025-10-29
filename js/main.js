
        let current = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const total = slides.length;

        function showSlide(n) {
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));
            slides[n].classList.add('active');
            dots[n].classList.add('active');
            current = n;
        }

        function changeSlide(direction) {
            current = (current + direction + total) % total;
            showSlide(current);
        }

        function goToSlide(n) {
            showSlide(n);
        }

        setInterval(() => changeSlide(1), 5000);
 