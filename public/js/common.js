/**
 * File: common.js
 * Author: Raqibul Hasan Moon
 * Created: 2023-06-15
 * Description: This file contains commonly used JavaScripts code for the project.
 */

const otpWrap = '/otp-wrap';
const verifyWrap = '/verify-wrap';
const callDynamically = '/calldapi';

const audioManager = {
    audio: [],

    playAudio: function (audioUrl) {
        // Stop any existing audio before playing a new one
        this.stopAudio();

        const audioElement = new Audio(audioUrl);
        this.audio.push(audioElement);

        audioElement.onended = () => {
            // Remove the audio element from the array when playback is complete
            const index = this.audio.indexOf(audioElement);
            if (index !== -1) {
                this.audio.splice(index, 1);
            }
        };

        audioElement.play();
    },

    stopAudio: function () {
        // Stop all existing audio elements
        for (const audioElement of this.audio) {
            audioElement.pause();
            audioElement.onended = null;
        }
        this.audio = [];
    }
};

// Function to check login status using Axios and redirect to send-otp if not logged in
function checkLoginStatus() {
    return new Promise((resolve, reject) => {
        axios.get('/check-login') // Replace with the URL of your Laravel endpoint for login check
            .then(response => {
                // Resolve the promise with a boolean indicating login status
                resolve(response.data.is_logged);
            })
            .catch(error => {
                // An error occurred during login check, reject the promise with the error message
                reject(error);
            });
    });
}

function showVerificationAlert() {
    playErrorAudio('/uploads/prompts/verify-account-to-access-feature.mp3'); // Play the error audio

    Swal.fire({
        icon: 'warning',
        title: 'Verification Required',
        text: 'You need to verify your account first to access this feature.',
        showCancelButton: true,
        confirmButtonText: 'Go to Verification',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect the user to the login page
            goTo('send-otp'); // Replace with your login page URL
        }
    });
}

function makeHttpRequest(url, dataForGetPost, method = "GET", headers = null) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: method,
            url: url,
            data: dataForGetPost,
            cache: false,
            async: false,
            headers: headers,
            success: function (response) {
                resolve(response);
            },
            error: function (xhr, status, error) {
                reject(error);
            }
        });
    });
}

function toggleSound() {
    playerControl();
}

function playerControl() {
    const audioElement = document.getElementById('playMedia');
    const toggleButton = document.getElementById('toggleAudio');

    if (audioElement.paused) {
        audioElement.play();
        toggleButton.classList.remove('play');
        toggleButton.classList.add('pause');
    } else {
        audioElement.pause();
        toggleButton.classList.remove('pause');
        toggleButton.classList.add('play');
    }
}

function uid() {
    return 'id_' + Date.now() + String(Math.random()).substr(2);
}

function uniqueID() {
    return Math.floor(Math.random() * Date.now());
}

function goTo(url = '/') {
    location.replace(url);
    return false;
}

function printThis(value) {
    document.write(value);
}

function getQryStr(target) {
    let queryStringKeyValue = (target === 'parent') ? window.parent.location.search.replace('?', '').split('&') : window.location.search.replace('?', '').split('&');

    let qsJsonObject = {};
    if (queryStringKeyValue !== '') {
        queryStringKeyValue.forEach((queryString) => {
            let [key, val] = queryString.split('=');
            qsJsonObject[key] = val;
        });
    }
    return qsJsonObject;
}

function parseURLParams(url) {
    let queryStart = url.indexOf("?") + 1;
    let queryEnd = url.indexOf("#") + 1 || url.length + 1;
    let query = url.slice(queryStart, queryEnd - 1);
    let pairs = query.replace(/\+/g, " ").split("&");
    let params = {};

    if (query === url || query === "") {
        return;
    }

    pairs.forEach((pair) => {
        let [n, v] = pair.split("=", 2);
        n = decodeURIComponent(n);
        v = decodeURIComponent(v);

        if (!params.hasOwnProperty(n)) {
            params[n] = [];
        }
        params[n].push((v.length === 2) ? v : null);
    });

    return params;
}

function isEmpty(value) {
    return (value == null || value.length === 0 || value === '');
}


// user journey track
function tUj(action, data) {
    // Get the CSRF token from the page's meta tag
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    // Create an XMLHttpRequest object
    let xhr = new XMLHttpRequest();

    // Set the request URL and method
    xhr.open('POST', '/tuj', true);

    // Set the request headers, including the CSRF token
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    // Handle the response
    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('succeeded!');
        } else {
            console.error('error:', xhr.status);
        }
    };

    // Convert the data to JSON string
    const requestData = JSON.stringify({action: action, data: JSON.stringify(data)});

    // Send the request
    xhr.send(requestData);
}

function showHideDiv(divName, isShowOrHide = 'show') {
    let div = $('#' + divName + '_error_div');
    if (isShowOrHide === 'show') {
        div.show();
    } else if (isShowOrHide === 'hide') {
        div.hide();
    }
}

function showEmptyErrorMsg(divName, message = '', isShowOrHide = 'show') {
    let div = $('#' + divName + '_error');
    if (isShowOrHide === 'show') {
        div.text(message);
    } else if (isShowOrHide === 'empty') {
        div.empty();
    }
}

function showHideButton(btnElem, isShowHide = 'show') {
    if (isShowHide === 'show') {
        $(btnElem).attr("disabled", false);
    } else if (isShowHide === 'hide') {
        $(btnElem).attr("disabled", true);
    }
}

function buttonDisable(submitButton, decision = false) {
    submitButton.disabled = decision;
}

function validatePhoneNumber(phoneNumber) {
    const regex = /^01[3456789][0-9]{8}$/;
    return regex.test(phoneNumber);
}

function displayErrorMessage(message, errorDisplay) {
    // console.error(message);
    errorDisplay.textContent = message;
    errorDisplay.classList.remove('hidden');
}

function clearErrorMessage(errorDisplay) {
    errorDisplay.textContent = '';
    errorDisplay.classList.add('hidden');
}

function handlePhoneNumberInput(phoneInput, errorDisplay, submitButton) {
    const phoneVal = phoneInput.value;

    if (phoneVal === '') {
        clearErrorMessage(errorDisplay);
        buttonDisable(submitButton, true);
    } else if (!validatePhoneNumber(phoneVal)) {
        buttonDisable(submitButton, true);
        displayErrorMessage('Mobile number is invalid.', errorDisplay);
        phoneInput.focus();
    } else if (phoneVal.length !== 11) {
        buttonDisable(submitButton, true);
        displayErrorMessage('Phone number must be 10 digits.', errorDisplay);
        phoneInput.focus();
    } else {
        clearErrorMessage(errorDisplay);
        buttonDisable(submitButton, false);
    }
}

// Show the loader
function showLoader() {
    const loader = document.querySelector('.loader');
    loader.classList.remove('hidden');
}

// Hide the loader
function hideLoader() {
    const loader = document.querySelector('.loader');
    loader.classList.add('hidden');
}

function redirectTo(url) {
    window.location.replace(url);
    return false;
}

function storeData(key, value) {
    if (typeof key !== 'string' || key.trim() === '') {
        console.error('Invalid key. Key must be a non-empty string.');
    }

    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (error) {
        console.error('Error storing data:', error);
        return false;
    }
}

function getData(key) {
    let data = localStorage.getItem(key);
    if (data) {
        try {
            return JSON.parse(data);
        } catch (error) {
            // Parsing failed, return the original string value
            return data;
        }
    }
    // No data found for the given key
    return null;
}


function toggleAudio() {
    const audioElement = document.getElementById('playMedia');
    const toggleButton = document.getElementById('toggleAudio');

    if (audioElement.paused) {
        playAudio(audioElement);
        toggleButton.classList.remove('play');
        toggleButton.classList.add('pause');
    } else {
        pauseAudio(audioElement);
        toggleButton.classList.remove('pause');
        toggleButton.classList.add('play');
    }
}

function playAudio(audioElement) {
    if (audioElement.paused || audioElement.ended) {
        audioElement.play().catch(function (error) {
            console.log('Failed to play audio:', error);
        });
    }
}

function pauseAudio(audioElement) {
    if (!audioElement.paused && !audioElement.ended) {
        audioElement.pause();
    }
}

function playErrorAudio(audioUrl) {
    // console.log('playErrorAudio called', audioUrl)

    setTimeout(() => {
        if (audioUrl) {
            const audioElem = document.querySelector('audio');
            audioElem.src = audioUrl;
            audioElem.play();
        } else {
            console.error('Audio URL not found in the response.');
        }
    }, 100); // Adjust the delay time as needed
}

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

function showToast(title, message, icon, timer = 3000) {
    Swal.fire({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        icon: icon,
        title: title,
        text: message,
    });
}
