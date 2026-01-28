// Smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
            target.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }
    });
});

// Active navigation highlighting
const currentPage = window.location.pathname.split("/").pop() || "index.html";
document.querySelectorAll(".nav-item").forEach((item) => {
    if (item.getAttribute("href") === currentPage) {
        item.classList.add("active");
    } else {
        item.classList.remove("active");
    }
});

// Copy code blocks
document.querySelectorAll(".code-block").forEach((block) => {
    const button = document.createElement("button");
    button.className = "copy-btn";
    button.innerHTML = '<i class="fas fa-copy"></i>';
    button.onclick = () => {
        const code = block.querySelector("code").textContent;
        navigator.clipboard.writeText(code).then(() => {
            button.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-copy"></i>';
            }, 2000);
        });
    };
    block.style.position = "relative";
    block.appendChild(button);
});

// Add animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
        }
    });
}, observerOptions);

document.querySelectorAll(".doc-section").forEach((section) => {
    section.style.opacity = "0";
    section.style.transform = "translateY(20px)";
    section.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(section);
});
