window.addEventListener('DOMContentLoaded', () => {
    const audioElement = document.querySelector('audio');
    const toggleButton = document.getElementById('toggleAudio');

    if (audioElement) {
        // Check if the audio element is already playing
        if (!audioElement.paused && !audioElement.ended) {
            console.log('audio paused.')
            toggleButton.classList.add('pause');
        } else {
            console.log('audio playing.')
            toggleButton.classList.add('play');
        }

        audioElement.autoplay = true;

        audioElement.addEventListener('play', () => {
            console.log('audio playing 2.')
            toggleButton.classList.remove('play');
            toggleButton.classList.add('pause');
        });

        audioElement.addEventListener('pause', () => {
            console.log('audio paused 2.')
            toggleButton.classList.remove('pause');
            toggleButton.classList.add('play');
        });
    } else {
        console.error('Audio element not found.');
    }
});
