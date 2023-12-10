document.addEventListener('DOMContentLoaded', function () {

    const currentPath = window.location.pathname;

    async function callDynamicAPI(data) {
        try {
            const response = await axios.post(callDynamically, data);
            return response.data;
        } catch (error) {
            throw error.response.data;
        }
    }

    async function enterReason(title, message, audioFile) {
        const popupAudio = new Audio(`/uploads/prompts/${audioFile}.m4a`);
        popupAudio.play();

        return Swal.fire({
            title: title,
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: false,
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true,
            inputValidator: (reason) => {
                if (!reason) {
                    popupAudio.play();
                    return message;
                }
                return null;
            }
        }).then((result) => {
            if (popupAudio) {
                popupAudio.pause();
            }
            return result;
        });
    }

    async function handleResetPin() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for resetting PIN?', "Please enter the reason for resetting PIN.", 'enter-reason-resetting-pin');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'resetPin', 'page': 'home', 'button': 'btnResetPin', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleResetPin:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCardActivateClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for card activation?', "Please enter the reason for card activation.", 'enter-reason-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'cardActivate', 'page': 'home', 'button': 'btnCardActivate', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCardActivate click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleDeviceBindClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for device binding?', "Please enter the reason for binding the device.", 'enter-reason-device-bind');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'deviceBind', 'page': 'home', 'button': 'btnDeviceBind', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnDeviceBind click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLockWalletClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for locking the wallet?', "Please enter the reason for locking the wallet.", 'enter-reason-locking-wallet');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'lockWallet', 'page': 'home', 'button': 'btnLockWallet', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLockWallet click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }


    /*async function showCascadingDropdownsForCreatingAnIssue() {

        let locale = getSavedLocale();
        try {
            showLoader();
            const dropdownValuesResponse = await callDynamicAPI({
                'purpose': 'GET-CALL-TYPES-DROPDOWN-VALUES', 'page': 'home', 'button': 'createIssue',
            });

            const dropdownValues = dropdownValuesResponse.data;
            hideLoader();

            let textCallType = (locale === 'en') ? "Call Type" : "কল টাইপ";
            let textCallCategory = (locale === 'en') ? "Call Category" : "কল ক্যাটাগরি";
            let textSelectCallType = (locale === 'en') ? "Select Type" : "টাইপ নির্বাচন করুন";
            let textSelectCallCategory = (locale === 'en') ? "Select Category" : "ক্যাটাগরি নির্বাচন করুন";
            let textSelectSubCallCategory = (locale === 'en') ? "Select Sub Category" : "সাব ক্যাটাগরি নির্বাচন করুন";
            let textSelectSubSubCallCategory = (locale === 'en') ? "Select Sub Sub Category" : "সাব সাব ক্যাটাগরি নির্বাচন করুন";
            let textReason = (locale === 'en') ? "Reason" : "অভিযোগের কারণ";
            let textSubmitComplaint = (locale === 'en') ? 'Submit Complaint' : "অভিযোগ জমা দিন";

            const {value: selectedValues, dismiss} = Swal.fire({
                title: textSubmitComplaint,
                html: `<label for="callTypeSelect">${textCallType}:</label>
                <select id="callTypeSelect" class="swal2-input" style="width: 100% !important;" placeholder="${textCallType}" required>
                    <option value="" disabled selected>${textSelectCallType}</option>
                    ${getOptionsHtml(dropdownValues)}
                </select>
                <label for="callCategorySelect">${textCallCategory}:</label>
                <select id="callCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="${textSelectCallCategory}" required>
                    <option value="" disabled selected>${textSelectCallCategory}</option>
                </select>
                <label for="callSubCategorySelect">Call Sub-Category:</label>
                <select id="callSubCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="Select Sub-Category" required>
                    <option value="" disabled selected>Select Sub-Category</option>
                </select>
                <label for="callSubSubCategorySelect">Call Sub-Sub-Category:</label>
                <select id="callSubSubCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="Select Sub-Sub-Category" required>
                    <option value="" disabled selected>${textSelectSubSubCallCategory}</option>
                </select>

                <label for="reasonInput">${textReason}:</label>
                <input id="reasonInput" class="swal2-input" style="width: 100% !important;" placeholder="${textReason}" required />`,
                focusConfirm: false,
                preConfirm: () => {

                    const callTypeOpts = document.getElementById('callTypeSelect').value;
                    const callCategoryOpts = document.getElementById('callCategorySelect').value;
                    const callSubCategoryOpts = document.getElementById('callSubCategorySelect').value;
                    const callSubSubCategoryOpts = document.getElementById('callSubSubCategorySelect').value;
                    const reason = document.getElementById('reasonInput').value;

                    // Validate that all required fields are filled
                    if (!callTypeOpts || !callCategoryOpts || !callSubCategoryOpts || !callSubSubCategoryOpts || !reason) {
                        Swal.showValidationMessage((locale === 'en') ? "Please fill in all required fields." : "দয়া করে সবগুলো তথ্যই প্রদান করুন । ");
                    }

                    console.log('SUBMIT', callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason);
                    return {
                        callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason
                    };

                },
                showCancelButton: true,
                confirmButtonText: (locale === 'en') ? "Submit" : "জমা দিন",
                cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল",
                didOpen: () => {
                    const callTypeSelect = document.getElementById('callTypeSelect');
                    const callCategorySelect = document.getElementById('callCategorySelect');
                    const callSubCategorySelect = document.getElementById('callSubCategorySelect');
                    const callSubSubCategorySelect = document.getElementById('callSubSubCategorySelect');

                    // CALL TYPE EVENT
                    callTypeSelect.addEventListener('change', async () => {
                        const callType = callTypeSelect.value;
                        const callCategories = await fetchDropdownOptions('callCategorySelect', {'callType': callType});
                        const categoryDataValues = callCategories.data;
                        callCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectCallCategory}</option>` + getOptionsHtml(categoryDataValues);

                    });

                    // CALL CATEGORY EVENT
                    callCategorySelect.addEventListener('change', async () => {
                        const callTypeVal = callTypeSelect.value;
                        const callCategoryVal = callCategorySelect.value;
                        const callSubCategoryVal = callSubCategorySelect.value;
                        console.log('callTypeVal', callTypeVal, 'callCategoryVal', callCategoryVal, 'callSubCategoryVal', callSubCategoryVal);

                        const callSubCategories = await fetchDropdownOptions('callSubCategorySelect', {
                            "callType": callTypeVal, "callCategory": callCategoryVal
                        });

                        const subCategoryDataValues = callSubCategories.data;
                        console.log('subCategoryDataValues', subCategoryDataValues)
                        callSubCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectSubCallCategory}</option>` + getOptionsHtml(subCategoryDataValues);

                    });
                    // CALL SUB CATEGORY EVENT
                    callSubCategorySelect.addEventListener('change', async () => {
                        const callTypeVal = callTypeSelect.value;
                        const callCategoryVal = callCategorySelect.value;
                        const callSubCategoryVal = callSubCategorySelect.value;
                        const callSubSubCategoryVal = callSubSubCategorySelect.value;

                        console.log('callTypeVal', callTypeVal, 'callCategoryVal', callCategoryVal, 'callSubCategoryVal', callSubCategoryVal, 'callSubSubCategoryVal', 'callSubSubCategoryVal');

                        const callSubSubCategories = await fetchDropdownOptions('callSubSubCategorySelect', {
                            "callType": callTypeVal,
                            "callCategory": callCategoryVal,
                            "callSubCategory": callSubCategoryVal
                        });

                        const subSubCategoryDataValues = callSubSubCategories.data;
                        console.log('subSubCategoryDataValues', subSubCategoryDataValues)
                        callSubSubCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectSubSubCallCategory}</option>` + getOptionsHtml(subSubCategoryDataValues);

                    });

                },
            });
            console.log('selectedValues', selectedValues, dismiss)
            return  false
            if (selectedValues && !dismiss) {
                const {
                    callTypeOpts,
                    callCategoryOpts,
                    callSubCategoryOpts,
                    callSubSubCategoryOpts,
                    reason
                } = selectedValues;

                console.log('callTypeOpts', callTypeOpts);
                console.log('callCategoryOpts', callCategoryOpts);
                console.log('callSubCategoryOpts', callSubCategoryOpts);
                console.log('callSubSubCategoryOpts', callSubSubCategoryOpts);
                console.log('reason', reason);
                return false;
                /!*const apiResponse = await callDynamicAPI({
                    'purpose': 'CREATEISSUE', 'page': 'home', 'button': 'btnCreateIssue', ...selectedValues
                });
                console.log('apiResponse', apiResponse);

                const issueId = apiResponse.data?.issueId;
                const issue = issueId ? issueId : null;
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    text: "IssueId: " + issue
                });
                playErrorAudio(apiResponse.prompt);*!/
            } else if (dismiss === Swal.DismissReason.cancel) {
                // Handle cancel action if needed
            }

            /!*.then((result) => {
            if (result.isConfirmed) {
                const {value} = result;
                // Perform actions with the selected values
                console.log(value);
            }
        })*!/

        } catch (error) {
            console.error('Error in btnCreateIssue click:', error);
            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        } finally {
            hideLoader();
        }

    }*/
    async function showCascadingDropdownsForCreatingAnIssue() {
        let locale = getSavedLocale();
        try {
            showLoader();
            const dropdownValuesResponse = await callDynamicAPI({
                'purpose': 'GET-CALL-TYPES-DROPDOWN-VALUES', 'page': 'home', 'button': 'createIssue',
            });

            const dropdownValues = dropdownValuesResponse.data;
            hideLoader();

            let textCallType = (locale === 'en') ? "Call Type" : "কল টাইপ";
            let textCallCategory = (locale === 'en') ? "Call Category" : "কল ক্যাটাগরি";
            let textCallSubCategory = (locale === 'en') ? "Call Sub Category" : "কল সাব ক্যাটাগরি";
            let textCallSubSubCategory = (locale === 'en') ? "Call Sub Category" : "কল সাব সাব ক্যাটাগরি";
            let textSelectSubCallCategory = (locale === 'en') ? "Select Sub Category" : "সাব ক্যাটাগরি নির্বাচন করুন";
            let textSelectCallType = (locale === 'en') ? "Select Type" : "টাইপ নির্বাচন করুন";
            let textSelectCallCategory = (locale === 'en') ? "Select Category" : "ক্যাটাগরি নির্বাচন করুন";
            let textSelectSubSubCallCategory = (locale === 'en') ? "Select Sub Sub Category" : "সাব সাব ক্যাটাগরি নির্বাচন করুন";
            let textReason = (locale === 'en') ? "Reason" : "অভিযোগের কারণ";
            let textSubmitComplaint = (locale === 'en') ? 'Submit Complaint' : "অভিযোগ জমা দিন";

            const swalOptions = {
                title: textSubmitComplaint,
                html: `<label for="callTypeSelect">${textCallType}:</label>
                <select id="callTypeSelect" class="swal2-input" style="width: 100% !important;" placeholder="${textCallType}" required>
                    <option value="" disabled selected>${textSelectCallType}</option>
                    ${getOptionsHtml(dropdownValues)}
                </select>
                <label for="callCategorySelect">${textCallCategory}:</label>
                <select id="callCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="${textSelectCallCategory}" required>
                    <option value="" disabled selected>${textSelectCallCategory}</option>
                </select>
                <label for="callSubCategorySelect">${textCallSubCategory}:</label>
                <select id="callSubCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="${textCallSubCategory}" required>
                    <option value="" disabled selected>${textSelectSubCallCategory}</option>
                </select>
                <label for="callSubSubCategorySelect">${textCallSubSubCategory}:</label>
                <select id="callSubSubCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="${textSelectSubSubCallCategory}" required>
                    <option value="" disabled selected>${textSelectSubSubCallCategory}</option>
                </select>

                <label for="reasonInput">${textReason}:</label>
                <input id="reasonInput" class="swal2-input" style="width: 100% !important;" placeholder="${textReason}" required />`,
                focusConfirm: false,
                preConfirm: () => {
                    const callTypeOpts = document.getElementById('callTypeSelect').value;
                    const callCategoryOpts = document.getElementById('callCategorySelect').value;
                    const callSubCategoryOpts = document.getElementById('callSubCategorySelect').value;
                    const callSubSubCategoryOpts = document.getElementById('callSubSubCategorySelect').value;
                    const reason = document.getElementById('reasonInput').value;

                    if (!callTypeOpts || !callCategoryOpts || !callSubCategoryOpts || !callSubSubCategoryOpts || !reason) {
                        Swal.showValidationMessage((locale === 'en') ? "Please fill in all required fields." : "দয়া করে সবগুলো তথ্যই প্রদান করুন । ");
                    }

                    console.log('SUBMIT', callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason);

                    return {
                        callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason
                    };
                },
                showCancelButton: true,
                confirmButtonText: (locale === 'en') ? "Submit" : "জমা দিন",
                cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল",
                didOpen: () => {
                    const callTypeSelect = document.getElementById('callTypeSelect');
                    const callCategorySelect = document.getElementById('callCategorySelect');
                    const callSubCategorySelect = document.getElementById('callSubCategorySelect');
                    const callSubSubCategorySelect = document.getElementById('callSubSubCategorySelect');

                    // CALL TYPE EVENT
                    callTypeSelect.addEventListener('change', async () => {
                        const callType = callTypeSelect.value;
                        const callCategories = await fetchDropdownOptions('callCategorySelect', {'callType': callType});
                        const categoryDataValues = callCategories.data;
                        callCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectCallCategory}</option>` + getOptionsHtml(categoryDataValues);
                    });

                    // CALL CATEGORY EVENT
                    callCategorySelect.addEventListener('change', async () => {
                        const callTypeVal = callTypeSelect.value;
                        const callCategoryVal = callCategorySelect.value;
                        const callSubCategoryVal = callSubCategorySelect.value;
                        console.log('callTypeVal', callTypeVal, 'callCategoryVal', callCategoryVal, 'callSubCategoryVal', callSubCategoryVal);

                        const callSubCategories = await fetchDropdownOptions('callSubCategorySelect', {
                            "callType": callTypeVal, "callCategory": callCategoryVal
                        });

                        const subCategoryDataValues = callSubCategories.data;
                        console.log('subCategoryDataValues', subCategoryDataValues)
                        callSubCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectSubCallCategory}</option>` + getOptionsHtml(subCategoryDataValues);
                    });

                    // CALL SUB CATEGORY EVENT
                    callSubCategorySelect.addEventListener('change', async () => {
                        const callTypeVal = callTypeSelect.value;
                        const callCategoryVal = callCategorySelect.value;
                        const callSubCategoryVal = callSubCategorySelect.value;
                        const callSubSubCategoryVal = callSubSubCategorySelect.value;

                        console.log('callTypeVal', callTypeVal, 'callCategoryVal', callCategoryVal, 'callSubCategoryVal', callSubCategoryVal, 'callSubSubCategoryVal', callSubSubCategoryVal);

                        const callSubSubCategories = await fetchDropdownOptions('callSubSubCategorySelect', {
                            "callType": callTypeVal,
                            "callCategory": callCategoryVal,
                            "callSubCategory": callSubCategoryVal
                        });

                        const subSubCategoryDataValues = callSubSubCategories.data;
                        console.log('subSubCategoryDataValues', subSubCategoryDataValues)
                        callSubSubCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectSubSubCallCategory}</option>` + getOptionsHtml(subSubCategoryDataValues);
                    });
                },
            };

            const {value: selectedValues, dismiss} = await Swal.fire(swalOptions);
            console.log('selectedValues', selectedValues, dismiss);

            if (selectedValues && !dismiss) {
                const {
                    callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason
                } = selectedValues;

                console.log('callTypeOpts', callTypeOpts, 'callCategoryOpts', callCategoryOpts, 'callSubCategoryOpts', callSubCategoryOpts, 'callSubSubCategoryOpts', callSubSubCategoryOpts, 'reason', reason);

                const apiResponse = await callDynamicAPI({
                    'purpose': 'CREATEISSUE', 'page': 'home', 'button': 'btnCreateIssue', ...selectedValues
                });
                console.log('apiResponse', apiResponse);

                const issueId = apiResponse.data?.issueId;
                const issue = issueId ? issueId : null;
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    text: "IssueId: " + issue
                });
                playErrorAudio(apiResponse.prompt);
            } else if (dismiss === Swal.DismissReason.cancel) {
                // Handle cancel action if needed
                hideLoader();
            }
        } catch (error) {
            console.error('Error in btnCreateIssue click:', error);
            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        } finally {
            hideLoader();
        }
    }

    /*async function handleCreateIssueClick() {
        let locale = getSavedLocale();
        const callTypeSelect = document.getElementById('callTypeSelect');
        const callCategorySelect = document.getElementById('callCategorySelect');
        const callSubCategorySelect = document.getElementById('callSubCategorySelect');
        const callSubSubCategorySelect = document.getElementById('callSubSubCategorySelect');
        const reasonInput = document.getElementById('reasonInput');

        let textCallType = (locale === 'en') ? "Call Type" : "কল টাইপ";
        let textCallCategory = (locale === 'en') ? "Call Category" : "কল ক্যাটাগরি";
        let textSelectCallType = (locale === 'en') ? "Select Type" : "টাইপ নির্বাচন করুন";
        let textSelectCallCategory = (locale === 'en') ? "Select Category" : "ক্যাটাগরি নির্বাচন করুন";
        let textReason = (locale === 'en') ? "Reason" : "অভিযোগের কারণ";
        let textSubmitComplaint = (locale === 'en') ? 'Submit Complaint' : "অভিযোগ জমা দিন";

        callTypeSelect.addEventListener('change', async () => {
            const selectedValues = {'callType': callTypeSelect.value};
            await updateDropdownOptions('callCategorySelect', selectedValues);
            resetDependentDropdowns('callCategorySelect', 'callSubCategorySelect', 'callSubSubCategorySelect');
        });

        callCategorySelect.addEventListener('change', async () => {
            const selectedValues = {
                'callType': callTypeSelect.value, 'callCategory': callCategorySelect.value
            };
            await updateDropdownOptions('callSubCategorySelect', selectedValues);
            resetDependentDropdowns('callSubCategorySelect', 'callSubSubCategorySelect');
        });

        callSubCategorySelect.addEventListener('change', async () => {
            const selectedValues = {
                'callType': callTypeSelect.value,
                'callCategory': callCategorySelect.value,
                'callSubCategory': callSubCategorySelect.value
            };
            await updateDropdownOptions('callSubSubCategorySelect', selectedValues);
        });

        const {value: selectedValues, dismiss} = await Swal.fire({
            title: textSubmitComplaint,
            html: `<label for="callTypeSelect">${textCallType}:</label>
            <select id="callTypeSelect" class="swal2-input" style="width: 100% !important;" placeholder="${textCallType}" required>
                <option value="" disabled selected>${textSelectCallType}</option>
                ${getOptionsHtml(dropdownValues.callType)}
            </select>
            <label for="callCategorySelect">${textCallCategory}:</label>
            <select id="callCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="${textSelectCallCategory}" required>
                <option value="" disabled selected>${textSelectCallCategory}</option>
            </select>
            <label for="callSubCategorySelect">Call Sub-Category:</label>
            <select id="callSubCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="Select Sub-Category" required>
                <option value="" disabled selected>Select Sub-Category</option>
            </select>
            <label for="callSubSubCategorySelect">Call Sub-Sub-Category:</label>
            <select id="callSubSubCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="Select Sub-Sub-Category" required>
                <option value="" disabled selected>Select Sub-Sub-Category</option>
            </select>
            <label for="reasonInput">${textReason}:</label>
            <input id="reasonInput" class="swal2-input" style="width: 100% !important;" placeholder="${textReason}" required />`,
            focusConfirm: false,
            preConfirm: () => {
                return validateForm(callTypeSelect.value, callCategorySelect.value, callSubCategorySelect.value, callSubSubCategorySelect.value, reasonInput.value);
            },
            showCancelButton: true,
            confirmButtonText: (locale === 'en') ? "Submit" : "জমা দিন",
            cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল"
        });

        if (selectedValues && !dismiss) {
            const apiResponse = await callDynamicAPI({
                'purpose': 'createIssue', 'page': 'home', 'button': 'btnCreateIssue', ...selectedValues
            });

            const issueId = apiResponse.data?.issueId;
            const issue = issueId ? issueId : null;
            Swal.fire({
                title: apiResponse.message,
                icon: apiResponse.status === 'success' ? 'success' : 'error',
                text: "IssueId: " + issue
            });
            playErrorAudio(apiResponse.prompt);
        } else if (dismiss === Swal.DismissReason.cancel) {
            // Handle cancel action if needed
        }
    }*/

    async function fetchDropdownOptions(targetDropdownId, selectedValues = {}) {
        let purpose;
        switch (targetDropdownId) {
            case 'callCategorySelect':
                purpose = 'GET-CALL-CATEGORY-OPTIONS';
                break;
            case 'callSubCategorySelect':
                purpose = 'GET-SUB-CATEGORY-OPTIONS';
                break;
            case 'callSubSubCategorySelect':
                purpose = 'GET-SUB-SUB-CATEGORY-OPTIONS';
                break;
            // Add more cases if needed for additional dropdowns
        }
        return callDynamicAPI({
            'purpose': purpose, 'selectedValues': selectedValues, 'button': targetDropdownId, 'page': 'home'
        });
    }

    function resetDependentDropdowns(...dropdownIds) {
        dropdownIds.forEach((dropdownId) => {
            const dropdown = document.getElementById(dropdownId);
            dropdown.innerHTML = `<option value="" disabled selected>Select ${dropdownId.replace('Select', '')}</option>`;
        });
    }


    /*
    // backup code of my complaint
    async function handleCreateIssueClick() {
        let locale = getSavedLocale();
        try {
            showLoader();
            // Fetch dropdown values from the API
            const dropdownValuesResponse = await callDynamicAPI({
                'purpose': 'GET-DROP-DOWN-VALUES', 'page': 'home', 'button': 'createIssue',
            });

            const dropdownValues = dropdownValuesResponse.data;
            hideLoader();

            let textCallType = (locale === 'en') ? "Call Type" : "কল টাইপ";
            let textCallCategory = (locale === 'en') ? "Call Category" : "কল ক্যাটাগরি";
            let textSelectCallType = (locale === 'en') ? "Select Type" : "টাইপ নির্বাচন করুন";
            let textSelectCallCategory = (locale === 'en') ? "Select Category" : "ক্যাটাগরি নির্বাচন করুন";
            let textReason = (locale === 'en') ? "Reason" : "অভিযোগের কারণ";
            let textSubmitComplaint = (locale === 'en') ? 'Submit Complaint' : "অভিযোগ জমা দিন";
            const {value: selectedValues, dismiss, inputValue: reasonInput} = await Swal.fire({
                title: textSubmitComplaint,
                html: `<label for="callTypeSelect">${textCallType}:</label>
                <select id="callTypeSelect" class="swal2-input" style="width: 100% !important;" placeholder="${textCallType}" required>
                    <option value="" disabled selected>${textSelectCallType}</option>
                    ${getOptionsHtml(dropdownValues.callType)}
                </select>
                <label for="callCategorySelect">${textCallCategory}:</label>
                <select id="callCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="${textSelectCallCategory}" required>
                    <option value="" disabled selected>${textSelectCallCategory}</option>
                    ${getOptionsHtml(dropdownValues.callCategory)}
                </select>
                <label for="reasonInput">${textReason}:</label>
                <input id="reasonInput" class="swal2-input" style="width: 100% !important;" placeholder="${textReason}" required />`,
                focusConfirm: false,
                preConfirm: () => {
                    const callTypeOpts = document.getElementById('callTypeSelect').value;
                    const callCategoryOpts = document.getElementById('callCategorySelect').value;
                    const reason = document.getElementById('reasonInput').value;

                    // Validate that all required fields are filled
                    if (!callTypeOpts || !callCategoryOpts || !reason) {
                        Swal.showValidationMessage((locale === 'en') ? "Please fill in all required fields." : "দয়া করে সবগুলো তথ্যই প্রদান করুন । ");
                    }

                    return {
                        callTypeOpts, callCategoryOpts, reason
                    };
                },
                showCancelButton: true,
                confirmButtonText: (locale === 'en') ? "Submit" : "জমা দিন",
                cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল"
            });

            if (selectedValues && !dismiss) {
                const apiResponse = await callDynamicAPI({
                    'purpose': 'createIssue',
                    'page': 'home',
                    'button': 'btnCreateIssue',
                    'callType': selectedValues.callTypeOpts,
                    'callCategory': selectedValues.callCategoryOpts,
                    'reason': selectedValues.reason || reasonInput
                });

                console.log('apiResponse', apiResponse);

                const issueId = apiResponse.data?.issueId;
                const issue = issueId ? issueId : null;
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    text: "IssueId: " + issue
                });
                playErrorAudio(apiResponse.prompt);
            } else if (dismiss === Swal.DismissReason.cancel) {
                // Handle cancel action if needed
            }

        } catch (error) {
            console.error('Error in btnCreateIssue click:', error);
            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }*/

    const getOptionsHtml = (options) => {
        return Object.entries(options).map(([value, text]) => `<option value="${value}">${text}</option>`).join('');
    };


    if (currentPath === '/') { // home/root path

        const btnCards = document.getElementById('btnCards');
        btnCards.addEventListener('click', handleCardsButtonClick);

        const accountOrLoan = document.getElementById('btnAccountAndLoan');
        accountOrLoan.addEventListener('click', handleAccountOrLoanButtonClick);

        addClickEventWithAsyncHandler('btnAgentBanking', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnESheba', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        const btnEWallet = document.getElementById('btnEWallet');
        btnEWallet.addEventListener('click', handleEWalletClick);

        const btnIslamiBanking = document.getElementById('btnIslamiBanking');
        btnIslamiBanking.addEventListener('click', handleIslamiBankingClick);

        addClickEventWithAsyncHandler('btnSonaliBankProduct', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnSPG', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        // Event listener for creation issue button
        /*const btnCreateIssue = document.getElementById('btnCreateIssue');
        btnCreateIssue.addEventListener('click', handleCreateIssueClick);*/

        const btnCreateIssue = document.getElementById('btnCreateIssue');
        btnCreateIssue.addEventListener('click', showCascadingDropdownsForCreatingAnIssue);

    } // end of pathname detects and conditionally assigns event listener
    else if (currentPath === '/cards') {

        const btnDebitCardActivate = document.getElementById('btnDebitCard');
        btnDebitCardActivate.addEventListener('click', handleDebitCardPageClick);

        const btnCreditCard = document.getElementById('btnCreditCard');
        btnCreditCard.addEventListener('click', handleCreditCardPageClick);

        const btnPrepaidCard = document.getElementById('btnPrepaidCard');
        btnPrepaidCard.addEventListener('click', handlePrePaidCardPageClick);

    } else if (currentPath === '/account-and-loan') {

        const btnCASASND = document.getElementById('btnCASASND');
        btnCASASND.addEventListener('click', handleCASASNDClick);

        const btnDPS = document.getElementById('btnDPS');
        btnDPS.addEventListener('click', handleDPSClick);

        const btnFixedDeposit = document.getElementById('btnFixedDeposit');
        btnFixedDeposit.addEventListener('click', handleFixedDepositClick);

        const btnLoansAndAdvances = document.getElementById('btnLoansAndAdvances');
        btnLoansAndAdvances.addEventListener('click', handleLoansAndAdvancesClick);

    } else if (currentPath === '/islami-banking') {

        const btnIBAccountRelated = document.getElementById('btnIBAccountRelated');
        btnIBAccountRelated.addEventListener('click', handleIBAccountRelatedClick);

        const btnIBLoansAndAdvances = document.getElementById('btnIBLoansAndAdvances');
        btnIBLoansAndAdvances.addEventListener('click', handleIBARLoansAndAdvancesClick);

    } else if (currentPath === '/sonali-products') {

        addClickEventWithAsyncHandler('btnSBDepositProducts', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSBPForeignCurrencyExchangeRate', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSBPLoanProducts', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSBPOtherProducts', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSBPScheduleOfCharges', showMessageForHelp);

    } else if (currentPath === '/spg') {

        addClickEventWithAsyncHandler('btnSPGBlazeRemittanceServices', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSPGChallanRelatedServices', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSPGEducationalFeesPayment', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSPGInternetBankingServices', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSPGOnlineBillPaymentService', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSPGSonaliBankAccountToBkash', showMessageForHelp);

    } else if (currentPath === '/loans-advances') {

        addClickEventWithAsyncHandler('btnLALoanClosureProcess', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnLALoanDetails', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandlerForApiCalling('btnLALoanDetails', (event, dataset) => handleLALoanDetailsClick(dataset.voice, dataset.text, dataset.title));*/

        /*addClickEventWithAsyncHandlerForApiCalling('btnLADueDateInstallment', (event, dataset) => handleLADueDateInstallmentClick(dataset.voice, dataset.text, dataset.title));*/

        addClickEventWithAsyncHandler('btnLADueDateInstallment', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnLAOutstandingLoanBalance', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandlerForApiCalling('btnLAOutstandingLoanBalance',
        (event, dataset) => handleLAOutstandingLoanBalanceClick(dataset.voice, dataset.text, dataset.title));*/

    } else if (currentPath === '/fixed-deposit') {

        addClickEventWithAsyncHandler('btnFDEncashmentProcess', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandlerForApiCalling('btnFDFixedDepositDetails', (event, dataset) => handleFDFixedDepositDetailsClick(dataset.voice, dataset.text, dataset.title));*/
        addClickEventWithAsyncHandler('btnFDFixedDepositDetails', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnFDMaturityDate', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandlerForApiCalling('btnFDMaturityDate',
        (event, dataset) => handleFDMaturityDateClick(dataset.voice, dataset.text, dataset.title));*/

    } else if (currentPath === '/account-dps') {

        /*
        // for nid input, use this.
        addClickEventWithAsyncHandlerForApiCalling('btnALAccountDPSAvailableBalance',
        (event, dataset) => handleALAccountDPSAvailableBalanceClick(dataset.voice,
        dataset.text, dataset.title));*/

        addClickEventWithAsyncHandler('btnALAccountDPSAvailableBalance', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandlerForApiCalling('btnALDPSDetails', (event, dataset) => handleALDPSDetailsClick(dataset.voice, dataset.text, dataset.title));*/

        addClickEventWithAsyncHandler('btnALDPSDetails', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnALAccountDPSEncashmentProcess', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnALAccountDPSInstalmentDetails', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandlerForApiCalling('btnALAccountDPSInstalmentDetails',
        (event, dataset) => handleALAccountDPSInstalmentDetailsClick(dataset.voice, dataset.text, dataset.title));*/


    } else if (currentPath === '/ib-account-related') {

        addClickEventWithAsyncHandler('btnIBARChequeBookLeaf', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBARAccountClosureProcess', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBARActivateSmsBanking', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBARAvailableBalance', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBARFundTransferServices', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBARMiniStatement', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBARChequeBookRequisition', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBARIslamicBankingProducts', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));


    } else if (currentPath === '/ewallet') {

        const btnEWChangeOrResetEWalletPIN = document.getElementById('btnEWChangeOrResetEWalletPIN');
        btnEWChangeOrResetEWalletPIN.addEventListener('click', handleEWChangeOrResetEWalletPINClick);

        const btnEWDeviceBind = document.getElementById('btnEWDeviceBind');
        btnEWDeviceBind.addEventListener('click', handleEWDeviceBindClick);

        const btnEWLockOrBlock = document.getElementById('btnEWLockOrBlock');
        btnEWLockOrBlock.addEventListener('click', handleEWLockOrBlockClick);

        const btnEWUnlockOrActive = document.getElementById('btnEWUnlockOrActive');
        btnEWUnlockOrActive.addEventListener('click', handleEWUnlockOrActiveClick);

        addClickEventWithAsyncHandler('btnEWAboutSonaliEWallet', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        const btnEWApproveOrReject = document.getElementById('btnEWApproveOrReject');
        btnEWApproveOrReject.addEventListener('click', handleEWApproveOrRejectClick);

        /*addClickEventWithAsyncHandler('btnEWEWalletClose', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));*/

        const btnEWEWalletClose = document.getElementById('btnEWEWalletClose');
        btnEWEWalletClose.addEventListener('click', handleEWCloseWalletClick);

    } else if (currentPath === '/esheba') {

        addClickEventWithAsyncHandler('btnESAccountOpening', showMessageForHelp);
        addClickEventWithAsyncHandler('btnESAboutSonaliESheba', showMessageForHelp);
        addClickEventWithAsyncHandler('btnESOtherServices', showMessageForHelp);

    } else if (currentPath === '/credit-card') {

        addClickEventWithAsyncHandler('btnCCreditCardActivation', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditCardBlock', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditChangeOrResetPIN', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditECommerceActivationOrDeactivation', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditGreenPINGeneration', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditMiniStatement', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditOutstandingBDT', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditOutstandingUSD', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCCreditCardPayment', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

    } else if (currentPath === '/debit-card') {

        addClickEventWithAsyncHandler('btnDCDebitCardActivation', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnDCDebitCardBlock', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnDCDebitChangeOrResetPIN', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCDebitECommerceActivationOrDeactivation', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCDebitGreenPINGeneration', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCDebitMiniStatement', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

    } else if (currentPath === '/prepaid-card') {

        addClickEventWithAsyncHandler('btnCPrepaidCardActivation', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCPrepaidCardBlock', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCPrepaidChangeOrResetPIN', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCPrepaidECommerceActivationOrDeactivation', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCPrepaidMiniStatement', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCPrepaidGreenPINGeneration', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

    } else if (currentPath === '/agent-banking') {
        addClickEventWithAsyncHandler('btnABAgentBankingServices', showMessageForHelp);
    } else if (currentPath === '/casasnd') {

        addClickEventWithAsyncHandler('btnChequeBookLeaf', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnCASAActivateSMSBanking', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*
        // for nid input, use this.
        addClickEventWithAsyncHandlerForApiCalling('btnCASAMiniStatement',
        (event, dataset) => handleCASAMiniStatementClick(dataset.voice, dataset.text, dataset.title));*/

        addClickEventWithAsyncHandler('btnCASAMiniStatement', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));


        addClickEventWithAsyncHandler('btnFundTransferServices', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));


        addClickEventWithAsyncHandler('btnCASAAvailableBalance', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*
        // for nid input, use this.
        addClickEventWithAsyncHandlerForApiCalling('btnCASAAvailableBalance',
        (event, dataset) => handleCASAAvailableBalanceClick(dataset.voice, dataset.text, dataset.title));*/

        addClickEventWithAsyncHandler('btnChequeBookRequisition', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnAccountClosureProcess', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

    } else if (currentPath === '/ib-loans-advances') {

        addClickEventWithAsyncHandler('btnIBLALoanClosureProcess', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBLALoanDetails', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        addClickEventWithAsyncHandler('btnIBLAOutstandingLoanBalance', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

    }

    async function handleEWChangeOrResetEWalletPINClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                showLoader();

                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-CHANGE-OR-RESET-PIN',
                    'page': 'ewallet',
                    'button': 'btnEWChangeOrResetEWalletPIN',
                    'reason': 'PIN Change or Reset request received.'
                });

                hideLoader();
                Swal.fire({
                    title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWApproveOrReject click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    /*async function handleEWChangeOrResetEWalletPINClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'EW-CHANGE-OR-RESET-PIN',
                        'page': 'ewallet',
                        'button': 'btnEWChangeOrResetEWalletPIN',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLAOutstandingLoanBalance click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }*/

    async function handleEWApproveOrRejectClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                showLoader();
                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-APPROVE-OR-REJECT',
                    'page': 'ewallet',
                    'button': 'btnEWApproveOrReject',
                    'reason': 'Approve wallet request received.'
                });
                hideLoader();

                Swal.fire({
                    title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWApproveOrReject click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    /*async function handleEWDeviceBindClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'EW-DEVICE-BIND',
                        'page': 'ewallet',
                        'button': 'btnEWDeviceBind',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLAOutstandingLoanBalance click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }*/

    async function handleEWDeviceBindClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                showLoader();
                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-DEVICE-BIND',
                    'page': 'ewallet',
                    'button': 'btnEWDeviceBind',
                    'reason': 'Device Bind request received.'
                });
                hideLoader();
                Swal.fire({
                    title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLAOutstandingLoanBalance click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleEWCloseWalletClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                showLoader();
                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-CLOSE-WALLET',
                    'page': 'ewallet',
                    'button': 'btnEWEWalletClose',
                    'reason': 'Wallet closing request received.'
                });
                hideLoader();
                Swal.fire({
                    title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWEWalletClose click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleEWLockOrBlockClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                showLoader();
                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-LOCK-BLOCK',
                    'page': 'ewallet',
                    'button': 'btnEWLockOrBlock',
                    'reason': 'Wallet Lock Or Block request received.'
                });
                hideLoader();
                Swal.fire({
                    title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWLockOrBlock click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    /*async function handleEWLockOrBlockClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'EW-LOCK-BLOCK',
                        'page': 'ewallet',
                        'button': 'btnEWLockOrBlock',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWLockOrBlock click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }*/

    async function handleEWUnlockOrActiveClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                showLoader();
                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-UNLOCK-ACTIVE',
                    'page': 'ewallet',
                    'button': 'btnEWUnlockOrActive',
                    'reason': 'Wallet Lock or Active request received.'
                });
                hideLoader();

                Swal.fire({
                    title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWUnlockOrActive click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    /*async function handleEWUnlockOrActiveClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'EW-UNLOCK-ACTIVE',
                        'page': 'ewallet',
                        'button': 'btnEWUnlockOrActive',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWUnlockOrActive click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }*/

    async function handleIBAccountRelatedClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/ib-account-related');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleIBAccountRelatedClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleSonaliBankProductClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/sonali-products');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleSonaliBankProductClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }


    async function handleSPGClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/spg');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleSPGClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleIBARLoansAndAdvancesClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/ib-loans-advances');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleIBAccountRelatedClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLoansAndAdvancesClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/loans-advances');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLoansAdvances click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCreditCardPageClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/credit-card');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleCreditCardPage click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleDebitCardPageClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/debit-card');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleCreditCardPage click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handlePrePaidCardPageClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/prepaid-card');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handlePrePaidCardPage click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleFixedDepositClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/fixed-deposit');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnFixedDeposit click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCASASNDClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/casasnd');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCASASND click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleDPSClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                goTo('/account-dps');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnDPSClick click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleDebitCardActivationClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for debit card activation?', "Please enter the reason for debit card activation.", 'enter-reason-for-debit-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'debitCardActivation',
                        'page': 'cards',
                        'button': 'btnDebitCard',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnDebitCardActivate click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCreditCardActivationClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for credit card activation?', "Please enter the reason for credit card activation.", 'enter-reason-for-credit-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'creditCardActivation',
                        'page': 'cards',
                        'button': 'btnCreditCard',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCreditCard click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handlePrepaidCardActivationClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for prepaid card activation?', "Please enter the reason for prepaid card activation.", 'enter-reason-for-prepaid-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'prepaidCardActivation',
                        'page': 'cards',
                        'button': 'btnPrepaidCard',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnPrepaidCard click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }


    async function handleChequeBookLeafClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for cheque book leaf?', "Please enter the reason for cheque book leaf process.", 'enter-reason-cheque-book-leaf');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'ChequeBookLeaf',
                        'page': 'casasnd',
                        'button': 'btnChequeBookLeaf',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnChequeBookLeaf click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLADueDateInstallmentClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Please enter loan amount.', "Please enter Sanction/Renewed/Re-Schedule loan amount here.", 'enter-loan-amount-due-date-installment');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'LA-DUE-DATE-INSTALLMENT',
                        'page': 'loanAndAdvances',
                        'button': 'btnLADueDateInstallment',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnChequeBookLeaf click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleIBARChequeBookLeafClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Please enter last transaction amount.', "Please enter last transaction amount here.", 'enter-ib-stop-payment-amount');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'IB-AR-CHEQUE-BOOK-LEAF-STOP-PAYMENT',
                        'page': 'ib-account-related',
                        'button': 'btnIBARChequeBookLeaf',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnIBARChequeBookLeaf click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCASAActivateSMSBankingClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for activation of SMS banking?', "Please enter the reason for activation of SMS banking.", 'enter-reason-activate-sms-banking');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'CASAActivateSMSBanking',
                        'page': 'casasnd',
                        'button': 'btnCASAActivateSMSBanking',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCASAActivateSMSBanking click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCASAAvailableBalanceClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'CASA-AVAILABLE-BALANCE',
                        'page': 'casasnd',
                        'button': 'btnCASAAvailableBalance',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCASAActivateSMSBanking click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCASAMiniStatementClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'CASA-MINI-STATEMENT',
                        'page': 'casasnd',
                        'button': 'btnCASAMiniStatement',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCASAMiniStatement click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLADueDateInstallmentClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'LA-DUE-DATE-INSTALLMENT',
                        'page': 'loans-advances',
                        'button': 'btnLADueDateInstallment',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLADueDateInstallment click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLAOutstandingLoanBalanceClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'LA-OUTSTANDING-LOAN-BALANCE',
                        'page': 'loans-advances',
                        'button': 'btnLAOutstandingLoanBalance',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLADueDateInstallment click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLALoanDetailsClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'LA-LOAN-DETAILS',
                        'page': 'loans-advances',
                        'button': 'btnLALoanDetails',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLALoanDetails click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }


    async function handleALAccountDPSAvailableBalanceClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'AL-ACCOUNT-DPS-AVAILABLE-BALANCE',
                        'page': 'account-dps',
                        'button': 'btnALAccountDPSAvailableBalance',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnALAccountDPSAvailableBalance click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleALAccountDPSInstalmentDetailsClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'AL-ACCOUNT-DPS-INSTALMENT-DETAILS',
                        'page': 'account-dps',
                        'button': 'btnALAccountDPSInstalmentDetails',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnALAccountDPSInstalmentDetails click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleALDPSDetailsClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'AL-DPS-DETAILS',
                        'page': 'account-dps',
                        'button': 'btnALDPSDetails',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnALDPSDetails click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleFDMaturityDateClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'FD-MATURITY-DATE',
                        'page': 'fixed-deposit',
                        'button': 'btnFDMaturityDate',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnFDMaturityDate click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleFDFixedDepositDetailsClick(voice = "", text = "", title = "") {

        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let {title, text, voice} = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'FD-FIXED-DEPOSIT-DETAILS',
                        'page': 'fixed-deposit',
                        'button': 'btnFDMaturityDate',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnFDMaturityDate click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    document.querySelectorAll('.radioBtn a').forEach(button => {
        button.addEventListener('click', function () {
            const locale = this.getAttribute('data-locale');

            // Update the active state based on the clicked locale
            setActiveState(locale);

            // Show loader before making the AJAX request
            showLoader();

            // Simulate axios post request with vanilla JavaScript fetch API
            axios.post('/change-locale', {locale: locale})
                .then(response => {
                    console.log(response.data);
                    // Redirect to the received URL
                    goTo(response.data.redirect);
                })
                .catch(error => {
                    console.error(error);
                    // Handle any errors that occur during the request
                })
                .finally(() => {
                    // Hide loader after the AJAX request is complete (success or error)
                    hideLoader();
                });
        });
    });

    // Check for the saved locale in cookie or localStorage
    const savedLocale = getSavedLocale();
    // Set the initial active state based on the saved locale
    setActiveState(savedLocale);

    document.getElementById('btnLogout').addEventListener('click', function (event) {
        event.preventDefault();

        // Make an AJAX request to the logout endpoint
        axios.post('/logout')
            .then(response => {
                // Handle the successful logout response (if needed)
                console.log('logout response', response.data);
                goTo();
            })
            .catch(error => {
                // Handle any errors that occur during the logout request
                console.error(error);
            });
    });

    async function showMessageForHelp(voice = "", text = "") {
        showLoader();
        await new Promise(resolve => setTimeout(resolve, 500));
        hideLoader();

        let voiceToPlay = voice;
        let textToDisplay = text;

        if (typeof voiceToPlay !== 'string' || voiceToPlay.trim() === "") {
            voiceToPlay = "/uploads/prompts/common/call-for-help-bn.m4a";
        }

        if (typeof textToDisplay !== 'string' || textToDisplay.trim() === "") {
            textToDisplay = defaultCallCenterText;
        }

        playErrorAudio(voiceToPlay);
        const result = await Swal.fire({
            icon: 'info',
            title: defaultContactOurCallCenter,
            text: textToDisplay,
            showCancelButton: true,
            confirmButtonText: defaultConfirmButtonText,
            cancelButtonText: defaultCancelButtonText,
            reverseButtons: true,
        });

        if (result.isConfirmed) {
            goTo('tel:' + helpCenterNumber);
        }
    }


    function addClickEventWithAsyncHandler(elementId, asyncHandler) {
        const element = document.getElementById(elementId);

        if (element) {
            element.addEventListener('click', (event) => {
                asyncHandler(event, element.dataset);
            });
        }
    }

    function addClickEventWithAsyncHandlerForApiCalling(elementId, asyncHandler) {
        const element = document.getElementById(elementId);

        if (element) {
            element.addEventListener('click', (event) => {
                asyncHandler(event, element.dataset);
            });
        }
    }

    // Handling cards functionality from here.

    async function handleCardsButtonClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // redirect to cards page
                goTo('/cards');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in cardsButtonClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleAccountOrLoanButtonClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // redirect to cards page
                goTo('/account-and-loan');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in accountAndLoanClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleAgentBankingClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // redirect to cards page
                goTo('/agent-banking');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in accountAndLoanClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleEShebaClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo('/esheba');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleEShebaClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleEShebaClickToAppStore() {
        const androidAppPackageName = "bd.com.sonalibank.sw";
        const iOSAppPackageName = "sonali-esheba/id1626802631";

        if (isAndroid()) {
            // Android: Redirect using intent:// URL
            window.location.href = "intent://details?id=" + androidAppPackageName + "#Intent;scheme=market;action=android.intent.action.VIEW;package=" + androidAppPackageName + ";end";
        } else if (isIOS()) {
            // iOS: Redirect to the Apple App Store
            window.location.href = "https://apps.apple.com/us/app/" + iOSAppPackageName;
        }
    }

    async function handleEWalletClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo('/ewallet');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleEWalletClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleIslamiBankingClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo('/islami-banking');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleIslamiBankingClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    function isAndroid() {
        return /Android/i.test(navigator.userAgent);
    }

    function isIOS() {
        return /iPhone|iPad|iPod/i.test(navigator.userAgent);
    }

    function getLocalWiseNIDContent() {
        const locale = getSavedLocale();
        const title = (locale === 'en') ? defaultNIDScriptTitleEn : defaultNIDScriptTitleBn;
        const text = (locale === 'en') ? defaultNIDScriptTextEn : defaultNIDScriptTextBn;
        const voice = `common/enter-nid-${locale}`;

        return {title, text, voice};
    }

});
