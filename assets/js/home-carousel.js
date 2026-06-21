/**
 * Standard infinite carousel — slide-by-slide, CSS transition, no external libs.
 */
class WeblazemCarousel {
    constructor(root) {
        this.root = root;
        this.viewport = root.querySelector('[data-carousel-viewport]');
        this.track = root.querySelector('[data-carousel-track]');
        this.prevBtn = root.querySelector('[data-carousel-prev]');
        this.nextBtn = root.querySelector('[data-carousel-next]');

        if (!this.viewport || !this.track) {
            return;
        }

        this.autoplayMs = parseInt(root.dataset.autoplay, 10) || 4000;
        this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.realSlides = Array.from(this.track.children);
        this.realCount = this.realSlides.length;
        this.index = 0;
        this.isTransitioning = false;
        this.isPaused = false;
        this.autoplayId = null;
        this.touchStartX = 0;
        this.touchDeltaX = 0;

        if (this.realCount === 0) {
            return;
        }

        if (this.realCount > 1) {
            this.buildClones();
            this.index = this.realCount;
        }

        this.bindEvents();
        this.goTo(this.index, false);
        this.startAutoplay();
    }

    buildClones() {
        this.realSlides.forEach((slide) => {
            const cloneEnd = slide.cloneNode(true);
            cloneEnd.classList.add('is-clone');
            cloneEnd.setAttribute('aria-hidden', 'true');
            this.track.appendChild(cloneEnd);
        });

        this.realSlides.slice().reverse().forEach((slide) => {
            const cloneStart = slide.cloneNode(true);
            cloneStart.classList.add('is-clone');
            cloneStart.setAttribute('aria-hidden', 'true');
            this.track.insertBefore(cloneStart, this.track.firstChild);
        });
    }

    getGap() {
        const styles = window.getComputedStyle(this.track);
        return parseFloat(styles.columnGap || styles.gap || '0') || 0;
    }

    getStep() {
        const slide = this.track.children[this.index] || this.track.children[0];
        if (!slide) {
            return 0;
        }
        return slide.getBoundingClientRect().width + this.getGap();
    }

    goTo(index, animate = true) {
        const step = this.getStep();
        const x = -(index * step);

        if (animate) {
            this.track.classList.remove('is-instant');
        } else {
            this.track.classList.add('is-instant');
        }

        this.track.style.transform = `translate3d(${x}px, 0, 0)`;
        this.index = index;

        if (!animate) {
            void this.track.offsetHeight;
            this.track.classList.remove('is-instant');
        }
    }

    normalizeIndex() {
        if (this.realCount <= 1) {
            return;
        }

        if (this.index >= this.realCount * 2) {
            this.index = this.realCount;
            this.goTo(this.index, false);
        } else if (this.index < this.realCount) {
            this.index = (this.realCount * 2) - 1;
            this.goTo(this.index, false);
        }
    }

    goNext() {
        if (this.isTransitioning || this.realCount <= 1) {
            return;
        }

        this.isTransitioning = true;
        this.goTo(this.index + 1, true);
    }

    goPrev() {
        if (this.isTransitioning || this.realCount <= 1) {
            return;
        }

        this.isTransitioning = true;
        this.goTo(this.index - 1, true);
    }

    onTransitionEnd(event) {
        if (event.target !== this.track || event.propertyName !== 'transform') {
            return;
        }

        this.normalizeIndex();
        this.isTransitioning = false;
    }

    startAutoplay() {
        this.stopAutoplay();

        if (this.prefersReducedMotion || this.realCount <= 1) {
            return;
        }

        this.autoplayId = window.setInterval(() => {
            if (!this.isPaused && !this.isTransitioning) {
                this.goNext();
            }
        }, this.autoplayMs);
    }

    stopAutoplay() {
        if (this.autoplayId) {
            window.clearInterval(this.autoplayId);
            this.autoplayId = null;
        }
    }

    restartAutoplay() {
        this.stopAutoplay();
        this.startAutoplay();
    }

    bindEvents() {
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                this.goNext();
                this.restartAutoplay();
            });
        }

        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                this.goPrev();
                this.restartAutoplay();
            });
        }

        this.track.addEventListener('transitionend', (event) => this.onTransitionEnd(event));

        this.root.addEventListener('mouseenter', () => {
            this.isPaused = true;
        });

        this.root.addEventListener('mouseleave', () => {
            this.isPaused = false;
        });

        this.viewport.addEventListener('touchstart', (event) => {
            this.touchStartX = event.touches[0].clientX;
            this.touchDeltaX = 0;
        }, { passive: true });

        this.viewport.addEventListener('touchmove', (event) => {
            this.touchDeltaX = event.touches[0].clientX - this.touchStartX;
        }, { passive: true });

        this.viewport.addEventListener('touchend', () => {
            if (Math.abs(this.touchDeltaX) < 40) {
                return;
            }

            if (this.touchDeltaX < 0) {
                this.goNext();
            } else {
                this.goPrev();
            }

            this.restartAutoplay();
        });

        window.addEventListener('resize', () => {
            window.clearTimeout(this.resizeTimer);
            this.resizeTimer = window.setTimeout(() => {
                this.goTo(this.index, false);
            }, 150);
        });
    }
}

function initWeblazemCarousels() {
    document.querySelectorAll('[data-weblazem-carousel]').forEach((element) => {
        new WeblazemCarousel(element);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWeblazemCarousels);
} else {
    initWeblazemCarousels();
}
