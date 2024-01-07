window.addEventListener('DOMContentLoaded', () => {
    const audioElement = document.querySelector('audio');
    const toggleButton = document.getElementById('toggleAudio');

    if (audioElement) {
        if (!audioElement.paused && !audioElement.ended) {
            toggleButton.classList.add('pause');
        } else {
            toggleButton.classList.add('play');
        }

        audioElement.autoplay = true;
        audioElement.addEventListener('play', () => {
            toggleButton.classList.remove('play');
            toggleButton.classList.add('pause');
        });

        audioElement.addEventListener('pause', () => {
            toggleButton.classList.remove('pause');
            toggleButton.classList.add('play');
        });
    } else {
        console.error('Audio element not found.');
    }
});
