document.addEventListener("DOMContentLoaded", function () {
    const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener("click", function (event) {
            event.preventDefault();
            const dropdownMenu = this.nextElementSibling; // Get the next sibling (dropdown menu)
            dropdownMenu.classList.toggle("show"); // Toggle the dropdown visibility

            // Toggle the arrow rotation
            const arrow = this.querySelector(".arrow");
            arrow.classList.toggle("rotate");
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        dropdownToggles.forEach(toggle => {
            const dropdownMenu = toggle.nextElementSibling; // Get the next sibling (dropdown menu)
            if (!toggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove("show");
                const arrow = toggle.querySelector(".arrow");
                arrow.classList.remove("rotate");
            }
        });
    });
});
