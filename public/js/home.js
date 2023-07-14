/**
 * File: home.js
 * Author: Raqibul Hasan Moon
 * Created: 2023-06-25
 * Description: This file contains home file related JavaScripts codes for the project.
 */

document.addEventListener('DOMContentLoaded', function () {

    const btnCardActivate = document.getElementById('btnCardActivate');

    btnCardActivate.addEventListener('click', function () {
        tUj('card-activate', {
            'purpose': 'cardActivate',
            'page': 'home',
            'button': 'btnCardActivate',
            'user_phone_no': getData('pn'),
            'user_account_no': getData('acn')
        });

        const audioElement = document.querySelector('audio');
        audioElement.addEventListener('ended', redirectToPage);

        // Check if the audio is not playing on page load
        if (audioElement.paused || audioElement.ended) {
            redirectToPage();
        }

        function redirectToPage() {
            // Redirect to page-b.
            console.log('ended', audioElement);
            goTo('send-otp');
        }
    });


    // Check for the saved locale in cookie or localStorage
    const savedLocale = getSavedLocale();

    // Set the initial active state based on the saved locale
    setActiveState(savedLocale);

    // Handle click event on language buttons
    $('.radioBtn a').on('click', function () {
        const locale = $(this).data('locale');

        // Update the active state based on the clicked locale
        setActiveState(locale);

        axios.post('/change-locale', {locale: locale})
            .then(response => {
                console.log(response.data);
                // Redirect to the received URL
                window.location.href = response.data.redirect;
                return false;
            })
            .catch(error => {
                console.error(error);
                // Handle any errors that occur during the request
            });
    });

    function getSavedLocale() {
        // Check if the locale is saved in cookie
        const savedLocale = getCookie('locale');

        if (savedLocale) {
            return savedLocale;
        }

        // Check if the locale is saved in localStorage
        return localStorage.getItem('locale');
    }

    function setActiveState(locale) {
        // Remove active class from all buttons
        $('.radioBtn a').removeClass('active');

        // Add active class to the button with the matching locale
        $('.radioBtn a[data-locale="' + locale + '"]').addClass('active');

        // Save the active locale in cookie or localStorage
        setSavedLocale(locale);
    }

    function setSavedLocale(locale) {
        // Save the locale in cookie
        setCookie('locale', locale, 30); // Set the cookie expiration to 30 days

        // Save the locale in localStorage
        localStorage.setItem('locale', locale);
    }

    // Helper function to get cookie value by name
    function getCookie(name) {
        const cookieArr = document.cookie.split(';');
        for (let i = 0; i < cookieArr.length; i++) {
            const cookiePair = cookieArr[i].split('=');
            if (name === cookiePair[0].trim()) {
                return decodeURIComponent(cookiePair[1]);
            }
        }
        return null;
    }

    // Helper function to set cookie
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + expires.toUTCString() + ';path=/';
    }
});
