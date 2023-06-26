/**
 * File: common.js
 * Author: Raqibul Hasan Moon
 * Created: 2023-06-15
 * Description: This file contains commonly used JavaScripts code for the project.
 */

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

function resetForm(selector) {
    $(selector).trigger("reset");
}


