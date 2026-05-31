(function () {
    function setupPageTransitions() {
        var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        if (reduceMotion) {
            return;
        }

        var overlay = document.createElement("div");
        overlay.className = "page-transition-overlay";
        document.body.appendChild(overlay);

        requestAnimationFrame(function () {
            overlay.classList.add("is-hidden");
        });

        window.setTimeout(function () {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 320);

        var internalLinks = document.querySelectorAll('a[href]');
        internalLinks.forEach(function (link) {
            link.addEventListener("click", function (event) {
                var href = link.getAttribute("href");
                if (!href || href.startsWith("#") || href.startsWith("mailto:") || href.startsWith("tel:")) {
                    return;
                }

                if (link.target === "_blank" || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                    return;
                }

                if (/^https?:\/\//i.test(href) && !href.includes(window.location.host)) {
                    return;
                }

                event.preventDefault();
                var exitOverlay = document.createElement("div");
                exitOverlay.className = "page-transition-overlay";
                exitOverlay.classList.add("is-hidden");
                document.body.appendChild(exitOverlay);

                requestAnimationFrame(function () {
                    exitOverlay.classList.remove("is-hidden");
                    exitOverlay.classList.add("is-active");
                });

                window.setTimeout(function () {
                    window.location.href = href;
                }, 220);
            });
        });
    }

    function setActiveNavLink() {
        var currentPage = window.location.pathname.split("/").pop() || "home.html";
        var navLinks = document.querySelectorAll("nav a");

        navLinks.forEach(function (link) {
            var href = link.getAttribute("href");
            var isActive = href === currentPage;
            link.classList.toggle("active", isActive);
            if (isActive) {
                link.setAttribute("aria-current", "page");
            } else {
                link.removeAttribute("aria-current");
            }
        });
    }

    function setupMobileMenu() {
        var toggle = document.querySelector(".nav-toggle");
        var nav = document.querySelector("header nav");

        if (!toggle || !nav) {
            return;
        }

        function closeMenu() {
            document.body.classList.remove("nav-open");
            toggle.setAttribute("aria-expanded", "false");
            toggle.textContent = "Menu";
        }

        toggle.addEventListener("click", function () {
            var isOpen = document.body.classList.toggle("nav-open");
            toggle.setAttribute("aria-expanded", String(isOpen));
            toggle.textContent = isOpen ? "Close" : "Menu";
        });

        nav.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", closeMenu);
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth > 900) {
                closeMenu();
            }
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                closeMenu();
            }
        });
    }

    function setupRevealAnimation() {
        var revealTargets = document.querySelectorAll(".hero, .card, .timeline-item, .team-card, .slider");
        if (!revealTargets.length) {
            return;
        }

        revealTargets.forEach(function (element, i) {
            element.classList.add("js-reveal");
            // stagger reveal slightly
            try { element.style.transitionDelay = (i * 80) + 'ms'; } catch (e) { }
        });

        if (!("IntersectionObserver" in window)) {
            revealTargets.forEach(function (element) {
                element.classList.add("is-visible");
            });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries, obs) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add("is-visible");
                    obs.unobserve(entry.target);
                });
            },
            {
                root: null,
                threshold: 0.12,
                rootMargin: "0px 0px -30px 0px"
            }
        );

        revealTargets.forEach(function (element) {
            observer.observe(element);
        });
    }

    function setupContactForm() {
        var form = document.querySelector(".form-wrap");
        if (!form) {
            return;
        }

        var nameField = form.querySelector("#name");
        var emailField = form.querySelector("#email");
        var messageField = form.querySelector("#message");
        var nextUrlField = form.querySelector("#next-url");

        if (!nameField || !emailField || !messageField) {
            return;
        }

        if (nextUrlField) {
            var current = window.location.href.split("?")[0].split("#")[0];
            nextUrlField.value = current + "?sent=1";
        }

        var status = document.createElement("p");
        status.className = "form-status";
        status.setAttribute("aria-live", "polite");
        form.appendChild(status);

        var draftKey = "hack-contact-draft";

        function showStatus(text, type) {
            status.textContent = text;
            status.classList.remove("success", "error");
            if (type) {
                status.classList.add(type);
            }
        }

        function saveDraft() {
            var draft = {
                name: nameField.value,
                email: emailField.value,
                message: messageField.value
            };
            localStorage.setItem(draftKey, JSON.stringify(draft));
        }

        var params = new URLSearchParams(window.location.search);
        if (params.get("sent") === "1") {
            showStatus("Thanks, your message was sent successfully.", "success");
            if (window.history.replaceState) {
                params.delete("sent");
                var query = params.toString();
                var cleanUrl = window.location.pathname + (query ? "?" + query : "") + window.location.hash;
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }

        var savedDraftRaw = localStorage.getItem(draftKey);
        if (savedDraftRaw) {
            try {
                var savedDraft = JSON.parse(savedDraftRaw);
                if (savedDraft && typeof savedDraft === "object") {
                    nameField.value = savedDraft.name || "";
                    emailField.value = savedDraft.email || "";
                    messageField.value = savedDraft.message || "";
                    showStatus("Draft restored from your last visit.", "");
                }
            } catch (error) {
                localStorage.removeItem(draftKey);
            }
        }

        [nameField, emailField, messageField].forEach(function (field) {
            field.addEventListener("input", saveDraft);
        });

        form.addEventListener("submit", function (event) {
            if (nameField.value.trim().length < 2) {
                event.preventDefault();
                showStatus("Please enter your full name.", "error");
                nameField.focus();
                return;
            }

            if (!emailField.checkValidity()) {
                event.preventDefault();
                showStatus("Please enter a valid email address.", "error");
                emailField.focus();
                return;
            }

            if (messageField.value.trim().length < 10) {
                event.preventDefault();
                showStatus("Message should be at least 10 characters.", "error");
                messageField.focus();
                return;
            }

            localStorage.removeItem(draftKey);
            showStatus("Sending your message...", "");
        });
    }

    function setupMembershipForm() {
        var openButton = document.getElementById("membership-open");
        var dialog = document.getElementById("membership-dialog");
        var closeButton = document.getElementById("membership-close");
        var form = document.getElementById("membership-form");
        var status = document.getElementById("membership-status");
        var formStatus = document.getElementById("membership-form-status");

        if (!openButton || !dialog || !form || !status) {
            return;
        }

        function showPageStatus(text, type) {
            status.textContent = text;
            status.classList.remove("success", "error");
            if (type) {
                status.classList.add(type);
            }
        }

        function openDialog() {
            if (typeof dialog.showModal === "function") {
                dialog.showModal();
                return;
            }

            dialog.setAttribute("open", "");
        }

        function closeDialog() {
            if (typeof dialog.close === "function") {
                dialog.close();
                return;
            }

            dialog.removeAttribute("open");
        }

        openButton.addEventListener("click", openDialog);

        if (closeButton) {
            closeButton.addEventListener("click", closeDialog);
        }

        dialog.addEventListener("click", function (event) {
            var rect = dialog.getBoundingClientRect();
            var clickedOutside = event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
            if (clickedOutside) {
                closeDialog();
            }
        });

        var params = new URLSearchParams(window.location.search);
        if (params.get("membership") === "success") {
            showPageStatus("Membership request received. We will review it soon.", "success");
            if (window.history.replaceState) {
                params.delete("membership");
                var query = params.toString();
                var cleanUrl = window.location.pathname + (query ? "?" + query : "") + window.location.hash;
                window.history.replaceState({}, document.title, cleanUrl);
            }
        } else if (params.get("membership") === "error") {
            showPageStatus("We could not save your request. Please try again.", "error");
            if (window.history.replaceState) {
                params.delete("membership");
                var errorQuery = params.toString();
                var errorUrl = window.location.pathname + (errorQuery ? "?" + errorQuery : "") + window.location.hash;
                window.history.replaceState({}, document.title, errorUrl);
            }
        }

        form.addEventListener("submit", function () {
            if (formStatus) {
                formStatus.textContent = "Sending your membership request...";
                formStatus.classList.remove("success", "error");
            }
        });
    }

    function setupSliders() {
        var sliders = document.querySelectorAll('.slider');
        if (!sliders || !sliders.length) return;

        sliders.forEach(function (slider) {
            var slidesWrap = slider.querySelector('.slides');
            var slides = slider.querySelectorAll('.slide');
            var prev = slider.querySelector('.slider-prev');
            var next = slider.querySelector('.slider-next');
            var dots = slider.querySelector('.slider-dots');
            var index = 0;
            var total = slides.length || 0;
            if (!total) return;

            // Ensure the slides wrapper and each slide have explicit widths
            // so percentage transforms behave consistently across pages.
            try {
                slidesWrap.style.width = (total * 100) + '%';
                slides.forEach(function (s) { s.style.width = (100 / total) + '%'; });
            } catch (e) {
                // ignore styling errors
            }

            function go(i) {
                index = (i + total) % total;
                var shiftPercent = -index * (100 / total);
                slidesWrap.style.transform = 'translateX(' + shiftPercent + '%)';
                updateDots();
            }

            function updateDots() {
                if (!dots) return;
                dots.innerHTML = '';
                for (var j = 0; j < total; j++) {
                    var b = document.createElement('button');
                    if (j === index) b.className = 'is-active';
                    (function (n) { b.addEventListener('click', function () { go(n); reset(); }); })(j);
                    dots.appendChild(b);
                }
            }

            if (prev) prev.addEventListener('click', function () { go(index - 1); reset(); });
            if (next) next.addEventListener('click', function () { go(index + 1); reset(); });

            var timer = null;
            function start() { stop(); timer = setInterval(function () { go(index + 1); }, 4000); }
            function stop() { if (timer) { clearInterval(timer); timer = null; } }
            function reset() { stop(); start(); }

            slider.addEventListener('mouseenter', stop);
            slider.addEventListener('mouseleave', start);

            updateDots();
            go(0);
            start();
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        setupPageTransitions();
        setActiveNavLink();
        setupMobileMenu();
        setupRevealAnimation();
        setupContactForm();
        setupMembershipForm();
        setupSliders();
    });
})();
