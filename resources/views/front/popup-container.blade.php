<!-- Popup Container Started -->
<div class="popup-container mob-popup-get-started fixed top-0 right-0 left-0 bottom-0 h-full w-full z-50"
     style="background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);">

    <div class="absolute top-11 right-5 w-8 h-8 z-50">
        <button id="toggleAudioGS" class="play playAudioBtn cursor-pointer w-8 h-8 bg-no-repeat z-50"></button>
    </div>

    <!-- BG Dots -->
    <div class="top-right absolute -top-28 right-0 overflow-hidden z-0">
        <img src="{{ asset('img/bg/top-right.svg') }}" alt="">
    </div>

    <div class="left-center absolute -left-24 inset-1/4 overflow-hidden z-0">
        <img src="{{ asset('img/bg/center-left.svg') }}" alt="">
    </div>

    <div class="right-bottom absolute bottom-0 right-0 overflow-hidden z-0">
        <img src="{{ asset('img/bg/bottom-right.svg') }}" alt="">
    </div>
    <!-- BG Dots -->

    <div class="container px-4 mx-auto">
        <div class="flex justify-center items-center h-screen">
            <div class="w-full lg:w-[40%] flex flex-col gap-6 justify-center items-center text-center">

                <div
                    class="flex mob-popup-logo-section flex-col gap-2 justify-center items-center text-center mb-10 z-10">
                    <img src="{{ asset('img/logo-white.png') }}" alt="">
                    <h1 id="ivrTitle"  class="text-white text-visual-ivr  [font-size:var(--font-size-title)] font-bold relative after:absolute after:w-full after:h-[2px] after:left-0 after:-bottom-1 after:bg-gray-300">
                        ভিজ্যুয়াল আইভিআর </h1>
                    <h2 class="text-white [font-size:var(--font-size-title)] font-bold">Sonali Bank PLC</h2>
                </div>

                <div class="bg-white mob-get-started-text rounded-md px-3 py-6 w-full mb-16 z-10"
                     data-text-en="{{ config('voices.defaultGetStarted.text.en')}}"
                     data-text-bn="{{ config('voices.defaultGetStarted.text.bn') }}"
                     data-voice-en="{{ config('voices.defaultGetStarted.voice.en')}}"
                     data-voice-bn="{{ config('voices.defaultGetStarted.voice.bn') }}"
                >
                    <p class="get-started-text text-[color:var(--brand-color-gray)] text-lg">{{ config('voices.defaultGetStarted.text.bn') }}</p>
                </div>

                <div class="relative" style="bottom: 15px;">

                    <div class="flex gap-3 justify-center my-6">

                        <button id="bnButton" type="submit" data-locale="bn"
                                class="language-button text-white text-lg px-4 py-1 hover:bg-[#0F5DA8]  rounded-full border-2 font-bold border-white cursor-pointer transition-colors duration-300 ease-in-out">
                            বাংলা
                        </button>

                        <button id="enButton" type="submit" data-locale="en"
                                class="language-button text-white text-lg px-4 py-1 hover:bg-[#0F5DA8]  rounded-full border-2 font-bold border-white cursor-pointer transition-colors duration-300 ease-in-out">
                            English
                        </button>

                        <button id="skipButton" type="submit" data-skip="yes"
                                class="text-white text-lg font-bold">
                            Skip
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- End of Popup Container -->

<script type="application/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        let isAudioPlaying = false;
        let currentAudio = null;

        const popupContainer = document.querySelector('.popup-container');
        const bnButton = document.getElementById('bnButton');
        const enButton = document.getElementById('enButton');
        const skipButton = document.getElementById('skipButton');
        let showGetStartedBtn = true;

        const toggleAudioGS = document.getElementById('toggleAudioGS');

        function playPauseAudio() {
            if (currentAudio) {
                isAudioPlaying = !isAudioPlaying;

                if (isAudioPlaying) {
                    currentAudio.play().catch(error => handleError('Error playing audio:', error));
                } else {
                    currentAudio.pause();
                }

                toggleAudioGS.classList.toggle('play', !isAudioPlaying);
                toggleAudioGS.classList.toggle('pause', isAudioPlaying);
            }
        }

        if (toggleAudioGS) {
            toggleAudioGS.addEventListener('click', function (event) {
                event.preventDefault();
                playPauseAudio();
            });
        }

        try {
            showGetStartedBtn = sessionStorage.getItem('hideGetStartedBtn') !== 'show';
        } catch (error) {
            handleError('Error retrieving data from sessionStorage:', error);
        }

        popupContainer.style.display = showGetStartedBtn ? 'block' : 'none';

        function playGSAudio(audio) {
            audio.play().then(() => {
                isAudioPlaying = true;
                toggleAudioGS.classList.add('pause');
                toggleAudioGS.classList.remove('play');
            }).catch(error => handleError('Error playing audio:', error));

            audio.addEventListener('pause', () => {
                isAudioPlaying = false;
                toggleAudioGS.classList.remove('pause');
                toggleAudioGS.classList.add('play');
            });
        }

        function handleError(message, error) {
            console.error(message, error);
        }

        function handleLanguageButtonClick(locale) {
            const selector = `[data-text-${locale}]`;
            const textContent = document.querySelector(selector)?.getAttribute(`data-text-${locale}`);
            const voiceContent = document.querySelector(selector)?.getAttribute(`data-voice-${locale}`);

            if (textContent !== null) {
                updateGetStartedText(textContent);
                updateSelectedLanguageButton(locale);

		// Update the title text for Visual IVR based on the language
       		 const ivrTitle = document.getElementById('ivrTitle');
        	 if (ivrTitle) {
            		ivrTitle.textContent = locale === 'en' ? 'Visual IVR' : 'ভিজ্যুয়াল আইভিআর';
       		 }
                if (currentAudio) {
                    currentAudio.pause();
                }

                const audio = new Audio(voiceContent);
                playGSAudio(audio);

                currentAudio = audio;

                audio.addEventListener('ended', async () => {
                    hidePopupContainer();
                    saveUserConsent(locale);
                    await doSwitchLangRequest(locale);
                });
            }
        }

        function updateGetStartedText(textContent) {
            const getStartedText = document.querySelector('.get-started-text');
            if (getStartedText) {
                getStartedText.textContent = textContent;
            }
        }

        function updateGetStartedText(textContent) {
            const getStartedText = document.querySelector('.get-started-text');
            if (getStartedText) {
                getStartedText.textContent = textContent;
            }
        }

        function updateSelectedLanguageButton(locale) {
            const languageButtons = document.querySelectorAll('.language-button');
            languageButtons.forEach(button => button.classList.remove('selected-language'));
            document.getElementById(`${locale}Button`)?.classList.add('selected-language');

            setSavedLocale(locale);
        }

        async function doSwitchLangRequest(locale) {
            try {
                const response = await axios.post('/change-locale', {locale});
                console.log(response.data);
                goTo(response.data.redirect);
            } catch (error) {
                handleError('Language switch request failed:', error);
            } finally {
                console.info('Language Switch AJAX request completed.');
            }
        }

        function addLanguageButtonEventListener(button, locale) {
            if (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    handleLanguageButtonClick(locale);
                });
            }
        }

        addLanguageButtonEventListener(enButton, 'en');
        addLanguageButtonEventListener(bnButton, 'bn');

        if (skipButton) {
            skipButton.addEventListener('click', async function (event) {
                event.preventDefault();
                const skip = this.getAttribute('data-skip');
                if (skip === 'yes') {
                    await processSkipOperation('bn');
                }
            });
        }

        async function processSkipOperation(localeToBeDecide) {
            hidePopupContainer();
            setShowHide('show');
            setSavedLocale(localeToBeDecide);

            if (bnButton && enButton) {
                bnButton.style.display = 'none';
                enButton.style.display = 'none';
            }

            await doSwitchLangRequest(localeToBeDecide);
        }

        function hidePopupContainer() {
            popupContainer.style.display = 'none';
        }

        function saveUserConsent(button) {
            try {
                setShowHide('show');
                tUj('get-started', {'purpose': 'getStarted', 'page': 'home', 'button': button});
            } catch (error) {
                handleError('Error saving data in sessionStorage:', error);
            }
        }

        function setShowHide(val) {
            sessionStorage.setItem('hideGetStartedBtn', val);
        }
    });
</script>
