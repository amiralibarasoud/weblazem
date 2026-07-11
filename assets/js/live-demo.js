(function () {
    'use strict';

    var root = document.querySelector('[data-ld-root]');
    if (!root || typeof weblazemLiveDemo === 'undefined') {
        return;
    }

    var cfg = weblazemLiveDemo;
    var itemsById = {};
    (cfg.items || []).forEach(function (item) {
        itemsById[String(item.id)] = item;
    });

    var filterBtns = root.querySelectorAll('[data-ld-filter]');
    var listItems = root.querySelectorAll('[data-ld-item]');
    var viewer = root.querySelector('[data-ld-viewer]');
    var placeholder = root.querySelector('[data-ld-placeholder]');
    var frameWrap = root.querySelector('[data-ld-frame-wrap]');
    var frame = root.querySelector('[data-ld-frame]');
    var iframe = root.querySelector('[data-ld-iframe]');
    var videoSlot = root.querySelector('[data-ld-video-slot]');
    var noteEl = root.querySelector('[data-ld-note]');
    var openBtn = root.querySelector('[data-ld-open]');
    var modeBtns = root.querySelectorAll('[data-ld-mode]');
    var deviceBtns = root.querySelectorAll('[data-ld-device]');

    var state = {
        id: null,
        device: 'desktop',
        mode: 'live',
        loadTimer: null
    };

    function setLoading(on) {
        if (!frame) return;
        frame.classList.toggle('is-loading', !!on);
    }

    function clearVideo() {
        if (videoSlot) {
            videoSlot.innerHTML = '';
            videoSlot.hidden = true;
        }
    }

    function showLive(item) {
        clearVideo();
        if (iframe) {
            iframe.hidden = false;
            setLoading(true);
            if (state.loadTimer) {
                clearTimeout(state.loadTimer);
            }
            iframe.onload = function () {
                setLoading(false);
            };
            iframe.src = item.url || 'about:blank';
            state.loadTimer = setTimeout(function () {
                setLoading(false);
            }, 8000);
        }
        if (noteEl) {
            noteEl.hidden = !item.url;
        }
    }

    function showVideo(item) {
        if (iframe) {
            iframe.hidden = true;
            iframe.removeAttribute('src');
        }
        setLoading(false);
        if (!videoSlot) return;
        videoSlot.hidden = false;
        videoSlot.innerHTML = '';

        if (item.ytEmbed) {
            var yt = document.createElement('iframe');
            yt.src = item.ytEmbed;
            yt.title = item.title || 'ویدیو دمو';
            yt.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            yt.allowFullscreen = true;
            videoSlot.appendChild(yt);
        } else if (item.isMp4 && item.video) {
            var vid = document.createElement('video');
            vid.src = item.video;
            vid.controls = true;
            vid.playsInline = true;
            videoSlot.appendChild(vid);
        } else if (item.video) {
            var link = document.createElement('a');
            link.href = item.video;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.textContent = 'مشاهده ویدیو';
            link.className = 'ld-viewer__open';
            link.style.position = 'absolute';
            link.style.inset = 'auto';
            link.style.top = '50%';
            link.style.left = '50%';
            link.style.transform = 'translate(-50%, -50%)';
            videoSlot.appendChild(link);
        }
        if (noteEl) noteEl.hidden = true;
    }

    function updateModeButtons(item) {
        var hasLive = !!(item && item.url);
        var hasVideo = !!(item && item.video);
        modeBtns.forEach(function (btn) {
            var mode = btn.getAttribute('data-ld-mode');
            if (mode === 'live') {
                btn.hidden = !hasLive;
            } else if (mode === 'video') {
                btn.hidden = !hasVideo;
            }
            btn.classList.toggle('is-active', mode === state.mode);
        });
    }

    function applyDevice() {
        if (!frame) return;
        frame.classList.remove('ld-frame--desktop', 'ld-frame--tablet', 'ld-frame--mobile');
        frame.classList.add('ld-frame--' + state.device);
        deviceBtns.forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-ld-device') === state.device);
        });
    }

    function selectItem(id) {
        var item = itemsById[String(id)];
        if (!item) return;

        state.id = String(id);
        listItems.forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-ld-id') === state.id);
        });

        if (placeholder) placeholder.hidden = true;
        if (frameWrap) frameWrap.hidden = false;

        var preferVideo = cfg.showVideoFirst && item.video;
        if (preferVideo) {
            state.mode = 'video';
        } else if (item.url) {
            state.mode = 'live';
        } else if (item.video) {
            state.mode = 'video';
        } else {
            state.mode = 'live';
        }

        updateModeButtons(item);
        applyDevice();

        if (openBtn) {
            if (item.url) {
                openBtn.hidden = false;
                openBtn.href = item.url;
            } else {
                openBtn.hidden = true;
                openBtn.removeAttribute('href');
            }
        }

        if (state.mode === 'video' && item.video) {
            showVideo(item);
        } else {
            showLive(item);
        }
    }

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var filter = btn.getAttribute('data-ld-filter') || 'all';
            filterBtns.forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
            });
            listItems.forEach(function (itemBtn) {
                var cats = (itemBtn.getAttribute('data-ld-cats') || '').split(',').filter(Boolean);
                var show = filter === 'all' || cats.indexOf(filter) !== -1;
                itemBtn.hidden = !show;
            });
        });
    });

    listItems.forEach(function (btn) {
        btn.addEventListener('click', function () {
            selectItem(btn.getAttribute('data-ld-id'));
        });
    });

    deviceBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.device = btn.getAttribute('data-ld-device') || 'desktop';
            applyDevice();
        });
    });

    modeBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!state.id) return;
            var item = itemsById[state.id];
            if (!item) return;
            state.mode = btn.getAttribute('data-ld-mode') || 'live';
            updateModeButtons(item);
            if (state.mode === 'video') {
                showVideo(item);
            } else {
                showLive(item);
            }
        });
    });

    if (listItems.length === 1) {
        selectItem(listItems[0].getAttribute('data-ld-id'));
    }
})();
