/**
 * File: common.js
 * Author: Raqibul Hasan Moon
 * Created: 2023-06-15
 * Description: This file contains commonly used JavaScripts code for the project.
 */

const otpWrap = '/otp-wrap';
const verifyWrap = '/verify-wrap';

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

function goTo(url) {
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
