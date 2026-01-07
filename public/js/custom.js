document.addEventListener("DOMContentLoaded", function () {
    const dropdownToggle = document.getElementById("userDropdownToggle");
    const dropdownMenu = document.getElementById("userDropdownMenu");

    dropdownToggle.addEventListener("click", function (event) {
        event.preventDefault();
        dropdownMenu.classList.toggle("show");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });
});
