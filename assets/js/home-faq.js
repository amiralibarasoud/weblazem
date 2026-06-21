function initFaqAccordion() {
    document.querySelectorAll('[data-faq-accordion]').forEach((accordion) => {
        const items = accordion.querySelectorAll('.faq-item');

        items.forEach((item) => {
            const trigger = item.querySelector('.faq-item__trigger');
            const panel = item.querySelector('.faq-item__panel');

            if (!trigger || !panel) {
                return;
            }

            trigger.addEventListener('click', () => {
                const isOpen = trigger.getAttribute('aria-expanded') === 'true';

                items.forEach((otherItem) => {
                    const otherTrigger = otherItem.querySelector('.faq-item__trigger');
                    const otherPanel = otherItem.querySelector('.faq-item__panel');

                    if (!otherTrigger || !otherPanel) {
                        return;
                    }

                    otherTrigger.setAttribute('aria-expanded', 'false');
                    otherItem.classList.remove('is-open');
                    otherPanel.hidden = true;
                });

                if (!isOpen) {
                    trigger.setAttribute('aria-expanded', 'true');
                    item.classList.add('is-open');
                    panel.hidden = false;
                }
            });
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFaqAccordion);
} else {
    initFaqAccordion();
}
