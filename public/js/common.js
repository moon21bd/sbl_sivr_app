const otpWrap = '/otp-wrap';
const verifyWrap = '/verify-wrap';
const callDynamically = '/calldapi';

const audioManager = {
    audio: [],

    playAudio: function (audioUrl) {

        this.stopAudio();

        const audioElement = new Audio(audioUrl);
        this.audio.push(audioElement);

        audioElement.onended = () => {
            const index = this.audio.indexOf(audioElement);
            if (index !== -1) {
                this.audio.splice(index, 1);
            }
        };

        audioElement.play();
    },

    stopAudio: function () {

        for (const audioElement of this.audio) {
            audioElement.pause();
            audioElement.onended = null;
        }
        this.audio = [];
    }
};

function checkLoginStatus() {
    return new Promise((resolve, reject) => {
        axios.get('/check-login')
            .then(response => {
                resolve(response.data.is_logged);
            })
            .catch(error => {
                reject(error);
            });
    });
}

function showVerificationAlert() {
    commonCloseNav();
    const getLocaleFromLS = getSavedLocale();
    playErrorAudio(`/uploads/prompts/common/verify-your-account-${getLocaleFromLS}.m4a`); // Play the error audio

    let newTitle = verificationTitleBn;
    let newText = verificationTextBn;
    if (getLocaleFromLS === 'en') {
        newTitle = verificationTitleEn;
        newText = verificationTextEn;
    }

    Swal.fire({
        html: `<img class="" src="./img/icon/checkmark.svg" /> <h2 class="swal2-title"> ${newTitle} </h2>
             <p>${newText}</p>`,
        showCancelButton: true,
        confirmButtonText: defaultVerificationText,
        cancelButtonText: defaultVerificationCancelText,
        allowOutsideClick: false,
        customClass: {
            container: 'default-verification-swal-bg'
        },
        willClose: () => {
            stopAllAudioPlayback();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            goTo('send-otp');
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

function tUj(action, data) {
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '/tuj', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('succeeded!');
        } else {
            console.error('error:', xhr.status);
        }
    };

    const requestData = JSON.stringify({action: action, data: JSON.stringify(data)});

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
    let locale = getSavedLocale();

    if (phoneVal === '') {
        clearErrorMessage(errorDisplay);
        buttonDisable(submitButton, true);
    } else if (!validatePhoneNumber(phoneVal)) {
        buttonDisable(submitButton, true);
        displayErrorMessage((locale === 'en') ? "Mobile number is invalid." : "মোবাইল নাম্বার সঠিক নয়।", errorDisplay);
        phoneInput.focus();
    } else if (phoneVal.length !== 11) {
        buttonDisable(submitButton, true);
        displayErrorMessage((locale === 'en') ? "Mobile number must be 10 digits." : "মোবাইল নাম্বার কমপক্ষে ১০ ডিজিট হতে হবে ।", errorDisplay);
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
            return data;
        }
    }

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

    setTimeout(() => {
        if (audioUrl) {
            const audioElem = document.querySelector('audio');
            audioElem.src = audioUrl;
            audioElem.play();
        } else {
            console.error('Audio URL not found in the response.');
        }
    }, 100);
}

function getSavedLocale() {

    const savedLocale = getCookie('locale');
    if (savedLocale) {
        return savedLocale;
    }

    return localStorage.getItem('locale');
}

function setActiveState(locale) {
    $('.radioBtn a').removeClass('active');
    $('.radioBtn a[data-locale="' + locale + '"]').addClass('active');
    setSavedLocale(locale);
}

function setSavedLocale(locale) {
    setCookie('locale', locale, 30);
    localStorage.setItem('locale', locale);
}

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
        allowOutsideClick: false,
        willClose: () => {
            stopAllAudioPlayback();
        }
    });
}

function stopAllAudioPlayback() {
    console.log('stopAllAudioPlayback Called.');

    const allAudioElements = document.querySelectorAll('audio');
    allAudioElements.forEach(audio => {
        audio.pause();
    });

    const audioToggleButtons = document.querySelectorAll('.playAudioBtn, .audioBtn');

    audioToggleButtons.forEach(button => {
        if (button.classList.contains('pause')) {
            button.classList.remove('pause');
        }
    });
}

function commonOpenNav() {
    let elem = document.getElementById("mobileMenu");
    if (elem) {
        elem.style.display = 'flex';
    }
}

function commonCloseNav() {
    let elem = document.getElementById("mobileMenu");
    if (elem) {
        elem.style.display = 'none';
    }

}

