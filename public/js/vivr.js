let count = 0;
let pageURL = window.location.href;
let parentPageURL = window.parent.location.href;

function uid() {
    return 'id_' + Date.now() + String(Math.random()).substr(2);
}

function uniqueID() {
    return Math.floor(Math.random() * Date.now())
}

function getQueryString(target) {
    let queryStringKeyValue = '';
    if (target == 'parent') {
        queryStringKeyValue = window.parent.location.search.replace('?', '').split('&');
    } else {
        queryStringKeyValue = window.location.search.replace('?', '').split('&');
    }

    let qsJsonObject = {};
    if (queryStringKeyValue != '') {
        for (let i = 0; i < queryStringKeyValue.length; i++) {
            qsJsonObject[queryStringKeyValue[i].split('=')[0]] = queryStringKeyValue[i].split('=')[1];
        }
    }
    return qsJsonObject;
}



let msisdn = '';
let query_param = getQueryString('main');
//console.log('query_param::', atob(query_param['msisdn']));
if (query_param['msisdn'] != undefined) {
    msisdn = atob(query_param['msisdn']);
    localStorage.setItem('msisdn', msisdn);
} else {
    localStorage.setItem('msisdn', msisdn);
}


window.onload = function () {
    let win = document.getElementById('pageLoader').contentWindow;
};



document.addEventListener('DOMContentLoaded', function() {
    const playAudioMedia = document.getElementById('play_audio_media');
    if (playAudioMedia) {
        playAudioMedia.addEventListener('playing', function() {
            triggerPlaying('Playing');
        });
    }
});
function triggerPlaying(state) {
    if (state === 'Playing') {
        if ($('#loud').attr('data-text') != 'Stop') {
            //$('#state').text('Play');
            $('#loud').attr('data-text', 'Play');
            $('#loud').removeClass('mute');
        }

    }
}

function changeState() {
    //$('#state').text('Play');
    //$('.loud').attr('data-text', 'Play');
    $('.loud').attr('data-text', 'Play');
    $('.loud').addClass('mute');
}

let playerState = 0;
function playerControl() {
    if (playerState != 0) {
        console.log('text', $('.loud').attr('data-text'));
        if ($('.loud').attr('data-text') == 'Stop') {
            document.getElementById('play_audio_media').pause();
            playerState = 0;
            $('#loud').attr('data-text', 'Play');
            $('#loud').removeClass('mute');
        } else {
            document.getElementById('play_audio_media').play();
            $('#loud').attr('data-text', 'Stop');
            $('#loud').addClass('mute');
        }
    } else {
        //console.log('PlayerControl Else');
        playerState = 1;
        document.getElementById('play_audio_media').play();
        $('#loud').attr('data-text', 'Stop');

    }
}

function changeURL(url) {
    ++pageState['home'];
    $("#play_audio_media").attr('src', serviceBaseURL + 'uploads/files/Home-screen-5ee5f754207ed.mp3');
    $("#play_audio_media source").attr('src', serviceBaseURL + 'uploads/files/Home-screen-5ee5f754207ed.mp3');
    document.getElementById('play_audio_media').pause();
    //$('#state').text('Play')
    $('.loud').attr('data-text', 'Play');
    $('.loud').addClass('mute');
    $("#pageLoader").attr('src', url);
}

function toggleSound() {
    playerControl();
}

$(document).ready(function () {});
