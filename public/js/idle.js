/*let isPageRefreshed = false;

// Detect page refresh
window.addEventListener('beforeunload', function (event) {
    const performance = window.performance || window.webkitPerformance || window.msPerformance || window.mozPerformance;

    if (performance.navigation.type === 1) {
        // Page is being refreshed
        isPageRefreshed = true;
    }
});*/

window.addEventListener('unload', function (event) {
    //if (!isPageRefreshed) {
    // Page is being closed, show a prompt
    expireSession();
    //}
});

/*
// WORKS ONLY FOR TAB CLOSE. NOT TRIGGERED IF PAGE RELOADED OR BEING REFRESHED.

window.addEventListener('unload', function() {
    axios.post('/exitintent', {
        isReloaded: false, // Indicate that it's not a reload
        exitIntentActive: true,
        purpose: 'EXIT',
    })
        .then(response => console.log('HTTP request successful:', response.data))
        .catch(error => console.error('Error making HTTP request:', error));
});*/

// detect is user idle start from here.
let idleTimer;
const idleTimeout = 10 * 60 * 1000; // 10 minutes in milliseconds

function resetIdleTimer() {
    clearTimeout(idleTimer);
    idleTimer = setTimeout(() => {
        console.log("User is idle. Session expired.");
        expireSession();
    }, idleTimeout);
}

function expireSession() {

    fetch('/loc', {
        method: 'POST', headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
        }, body: JSON.stringify({action: 'expireSession'}),
    })
        .then(response => {
            console.log('loc-response', response)
            if (response.ok) {
                window.location.href = '/';
            } else {
                console.error('Failed to loc on the server.');
            }
        })
        .catch(error => {
            console.error('Error occurred during loc expiration:', error);
        });
}

function handleUserActivity() {
    resetIdleTimer();
}

// Initial setup
resetIdleTimer();

// Event listeners for user activity
document.addEventListener("mousemove", handleUserActivity);
document.addEventListener("keydown", handleUserActivity);
document.addEventListener("click", handleUserActivity);

// Touch events for mobile devices
document.addEventListener("touchstart", handleUserActivity);
document.addEventListener("touchmove", handleUserActivity);
document.addEventListener("touchend", handleUserActivity);
