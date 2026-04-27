(function () {
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

    function setupRevealAnimation() {
        var revealTargets = document.querySelectorAll(".hero, .card, .timeline-item");
        if (!revealTargets.length) {
            return;
        }

        revealTargets.forEach(function (element) {
            element.classList.add("js-reveal");
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

        if (!nameField || !emailField || !messageField) {
            return;
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
            event.preventDefault();

            if (nameField.value.trim().length < 2) {
                showStatus("Please enter your full name.", "error");
                nameField.focus();
                return;
            }

            if (!emailField.checkValidity()) {
                showStatus("Please enter a valid email address.", "error");
                emailField.focus();
                return;
            }

            if (messageField.value.trim().length < 10) {
                showStatus("Message should be at least 10 characters.", "error");
                messageField.focus();
                return;
            }

            localStorage.removeItem(draftKey);
            showStatus("Thanks, your message is ready to send. We will contact you soon.", "success");
            form.reset();
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        setActiveNavLink();
        setupRevealAnimation();
        setupContactForm();
    });
})();
