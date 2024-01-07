/**
 * File: home.js
 * Author: Raqibul Hasan Moon
 * Description: This file contains ui/ux related JavaScripts codes for the project.
 */

// Menu Start
function openNav() {
    document.getElementById("mobileMenu").style.width = "100%";
}

function closeNav() {
    document.getElementById("mobileMenu").style.width = "0%";
}

// Menu End

// Tabs
let tabsContainer = document.querySelector("#tabs");

if (tabsContainer) {
    let tabTogglers = tabsContainer.querySelectorAll("#tabs a");

    tabTogglers.forEach(function (toggler) {
        toggler.addEventListener("click", function (e) {
            e.preventDefault();

            let tabName = this.getAttribute("href");

            let tabContents = document.querySelector("#tab-contents");

            for (let i = 0; i < tabContents.children.length; i++) {

                tabTogglers[i].parentElement.classList.remove("border-b-[2px]", "border-[color:var(--brand-color-blue)]", "text-[color:var(--brand-color-blue)]");
                tabContents.children[i].classList.remove("hidden");
                tabTogglers[i].parentElement.classList.add("text-[color:var(--text-black)]", "border-b-[2px]", "border-gray-300");
                if ("#" + tabContents.children[i].id === tabName) {
                    continue;
                }
                tabContents.children[i].classList.add("hidden");

            }
            e.target.parentElement.classList.remove("text-[color:var(--text-black)]", "border-b-[2px]", "border-gray-300");
            e.target.parentElement.classList.add("text-[color:var(--brand-color-blue)]", "border-b-[2px]", "border-[color:var(--brand-color-blue)]");
        });
    });

} else {
    // console.log("Tabs container not found");
}

// Tabs End
