document.addEventListener("DOMContentLoaded", function() {
    const tocToggle = document.querySelector(".manan-toc-toggle");
    const tocList = document.querySelector(".manan-toc-list");

    if (tocToggle && tocList) {
        tocToggle.addEventListener("click", () => {
            const isExpanded = tocToggle.getAttribute("aria-expanded") === "true";
            tocList.classList.toggle("collapsed");
            tocToggle.textContent = isExpanded ? "Show" : "Hide";
            tocToggle.setAttribute("aria-expanded", (!isExpanded).toString());
        });
    }
});
