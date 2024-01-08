let isPageRefreshed = false;

// Detect page refresh
window.addEventListener('beforeunload', function (event) {
    const performance = window.performance || window.webkitPerformance || window.msPerformance || window.mozPerformance;

    if (performance.navigation.type === 1) {
        // Page is being refreshed
        isPageRefreshed = true;
    }
});

// Show prompt on unload
window.addEventListener('unload', function (event) {
    if (!isPageRefreshed) {
        // Page is being closed, show a prompt
        expireSession(isPageRefreshed);
    }
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


/*$(window).on("beforeunload", function () {
    axios.post('/exitintent', {
        isReloaded: 200,
        exitIntentActive: exitIntentActive,
    })
        .then(response => {
            // Handle the response if needed
            console.log('HTTP request successful:', response.data);
        })
        .catch(error => {
            // Handle errors
            console.error('Error making HTTP request:', error);
        });
});*/

/*let exitIntentActive = false;

function handleExitIntent() {
    if (!exitIntentActive) {
        exitIntentActive = true;

        // Check if the page is being reloaded or refreshed
        if (performance.navigation.type === performance.navigation.TYPE_RELOAD ||
            performance.navigation.type === performance.navigation.TYPE_NAVIGATE) {
            alert('hi');
            // Page is being refreshed or reloaded, don't capture the event
            return;
        }

        console.log('Exit Intent Detected!');

        // Perform your exit intent actions here
        axios.post('/exitintent', {
            isReloaded: 200,
            exitIntentActive: exitIntentActive,
            purpose: 'EXIT',
        })
            .then(response => {
                // Handle the response if needed
                console.log('HTTP request successful:', response.data);
            })
            .catch(error => {
                // Handle errors
                console.error('Error making HTTP request:', error);
            });
    }
}

// Detect tab close or browser navigation
// window.addEventListener('beforeunload', handleExitIntent);*/


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

function expireSession(data = null) {

    fetch('/loc', {
        method: 'POST', headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
        }, body: JSON.stringify({action: 'expireSession', data: data}),
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
