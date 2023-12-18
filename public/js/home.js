document.addEventListener('DOMContentLoaded', function () {
    let locale = getSavedLocale();

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
                        title: apiResponse.message,
                        icon: apiResponse.status === 'success' ? 'success' : 'error',
                        allowOutsideClick: false
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
                    title: error.message, icon: 'error', allowOutsideClick: false
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
                        title: apiResponse.message,
                        icon: apiResponse.status === 'success' ? 'success' : 'error',
                        allowOutsideClick: false
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
                    title: error.message, icon: 'error', allowOutsideClick: false
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
                        title: apiResponse.message,
                        icon: apiResponse.status === 'success' ? 'success' : 'error',
                        allowOutsideClick: false
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
                    title: error.message, icon: 'error', allowOutsideClick: false
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
                        title: apiResponse.message,
                        icon: apiResponse.status === 'success' ? 'success' : 'error',
                        allowOutsideClick: false
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
                    title: error.message, icon: 'error', allowOutsideClick: false
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function showCascadingDropdownsForCreatingAnIssue() {
        let locale = getSavedLocale();
        try {
            showLoader();
            const callCategoryDropdownValuesResponse = await callDynamicAPI({
                'purpose': 'GET-CALL-CATEGORY-OPTIONS', 'page': 'home', 'button': 'createIssue'
            });

            const callCategoryDropdownValues = callCategoryDropdownValuesResponse.data;
            hideLoader();

            let textCallType = (locale === 'en') ? "Call Type" : "কল টাইপ";
            let textCallCategory = (locale === 'en') ? "Complaint Type" : "অভিযোগের ধরণ";
            let textCallSubCategory = (locale === 'en') ? "Complaint Sub Type" : "অভিযোগের উপ ধরণ";
            let textCallSubSubCategory = (locale === 'en') ? "Complaint Sub Sub Type" : "অভিযোগের উপ-উপ ধরণ";
            let textSelectSubCallCategory = (locale === 'en') ? "Select Sub Category" : "অভিযোগের উপ ধরণ নির্বাচন করুন";
            let textSelectCallType = (locale === 'en') ? "Select Type" : "টাইপ নির্বাচন করুন";
            let textSelectCallCategory = (locale === 'en') ? "Select Complaint Type" : "অভিযোগের ধরণ নির্বাচন করুন";
            let textSelectSubSubCallCategory = (locale === 'en') ? "Select Sub Sub Complaint Type" : "অভিযোগের উপ-উপ ধরণ নির্বাচন করুন";
            let textReason = (locale === 'en') ? "Reason" : "অভিযোগের কারণ";
            let textSubmitComplaint = (locale === 'en') ? 'Submit Complaint' : "অভিযোগ জমা দিন";

            const swalOptions = {
                title: textSubmitComplaint,
                html: `<label for="callCategorySelect">${textCallCategory}:</label>
                <select id="callCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="${textSelectCallCategory}" required>
                    <option value="" disabled selected>${textSelectCallCategory}</option>
                    ${getOptionsHtml(callCategoryDropdownValues)}
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
                    // const callTypeOpts = document.getElementById('callTypeSelect').value;
                    const callTypeOpts = 2; // service request
                    const callCategoryOpts = document.getElementById('callCategorySelect').value;
                    const callSubCategoryOpts = document.getElementById('callSubCategorySelect').value;
                    const callSubSubCategoryOpts = document.getElementById('callSubSubCategorySelect').value;
                    const reason = document.getElementById('reasonInput').value;

                    if (!callTypeOpts || !callCategoryOpts || !callSubCategoryOpts || !callSubSubCategoryOpts || !reason) {
                        Swal.showValidationMessage((locale === 'en') ? "Please fill in all required fields." : "দয়া করে সবগুলো তথ্যই প্রদান করুন । ");
                    }

                    // console.log('SUBMIT', callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason);

                    return {
                        callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason
                    };
                },
                showCancelButton: true,
                confirmButtonText: (locale === 'en') ? "Submit" : "জমা দিন",
                cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল",
                didOpen: () => {
                    // const callTypeSelect = document.getElementById('callTypeSelect');
                    const callTypeSelect = 2;
                    const callCategorySelect = document.getElementById('callCategorySelect');
                    const callSubCategorySelect = document.getElementById('callSubCategorySelect');
                    const callSubSubCategorySelect = document.getElementById('callSubSubCategorySelect');

                    // CALL TYPE EVENT
                    /*callTypeSelect.addEventListener('change', async () => {
                        const callType = callTypeSelect.value;
                        const callCategories = await fetchDropdownOptions('callCategorySelect', {'callType': callType});
                        const categoryDataValues = callCategories.data;
                        callCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectCallCategory}</option>` + getOptionsHtml(categoryDataValues);
                    });*/

                    // CALL CATEGORY EVENT
                    callCategorySelect.addEventListener('change', async () => {
                        const callTypeVal = callTypeSelect;
                        const callCategoryVal = callCategorySelect.value;
                        const callSubCategoryVal = callSubCategorySelect.value;
                        console.log('callTypeVal', callTypeVal, 'callCategoryVal', callCategoryVal, 'callSubCategoryVal', callSubCategoryVal);

                        const callSubCategories = await fetchDropdownOptions('callSubCategorySelect', {
                            "callType": callTypeVal, "callCategory": callCategoryVal
                        });

                        const subCategoryDataValues = callSubCategories.data;
                        // console.log('subCategoryDataValues', subCategoryDataValues)
                        callSubCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectSubCallCategory}</option>` + getOptionsHtml(subCategoryDataValues);
                    });

                    // CALL SUB CATEGORY EVENT
                    callSubCategorySelect.addEventListener('change', async () => {
                        const callTypeVal = callTypeSelect;
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
                        // console.log('subSubCategoryDataValues', subSubCategoryDataValues)
                        callSubSubCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectSubSubCallCategory}</option>` + getOptionsHtml(subSubCategoryDataValues);
                    });
                },
            };

            const {value: selectedValues, dismiss} = await Swal.fire(swalOptions);
            // console.log('selectedValues', selectedValues, dismiss);

            if (selectedValues && !dismiss) {
                const {
                    callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason
                } = selectedValues;

                /*console.log('callTypeOpts', callTypeOpts, 'callCategoryOpts', callCategoryOpts, 'callSubCategoryOpts', callSubCategoryOpts, 'callSubSubCategoryOpts', callSubSubCategoryOpts, 'reason', reason);*/

                const apiResponse = await callDynamicAPI({
                    'purpose': 'CREATEISSUE', 'page': 'home', 'button': 'btnCreateIssue', ...selectedValues
                });
                // console.log('apiResponse', apiResponse);

                const issueId = apiResponse.data?.issueId;
                const issue = issueId ? issueId : null;
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    allowOutsideClick: false,
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
                    title: error.message, icon: 'error', allowOutsideClick: false
                });
                playErrorAudio(error.prompt);
            }
        } finally {
            hideLoader();
        }
    }

    /*  async function showCascadingDropdownsForCreatingAnIssueForEWallet() {
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
                      <option value="" disabled selected>${textSelectCallType}</option>${getOptionsHtml(dropdownValues)}
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

                      // console.log('SUBMIT', callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason);

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
                          // console.log('subCategoryDataValues', subCategoryDataValues)
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
                          // console.log('subSubCategoryDataValues', subSubCategoryDataValues)
                          callSubSubCategorySelect.innerHTML = `<option value="" disabled selected>${textSelectSubSubCallCategory}</option>` + getOptionsHtml(subSubCategoryDataValues);
                      });
                  },
              };

              const {value: selectedValues, dismiss} = await Swal.fire(swalOptions);
              // console.log('selectedValues', selectedValues, dismiss);

              if (selectedValues && !dismiss) {
                  const {
                      callTypeOpts, callCategoryOpts, callSubCategoryOpts, callSubSubCategoryOpts, reason
                  } = selectedValues;

                  /!*console.log('callTypeOpts', callTypeOpts, 'callCategoryOpts', callCategoryOpts, 'callSubCategoryOpts', callSubCategoryOpts, 'callSubSubCategoryOpts', callSubSubCategoryOpts, 'reason', reason);*!/

                  const apiResponse = await callDynamicAPI({
                      'purpose': 'CREATEISSUE', 'page': 'home', 'button': 'btnCreateIssue', ...selectedValues
                  });
                  // console.log('apiResponse', apiResponse);

                  const issueId = apiResponse.data?.issueId;
                  const issue = issueId ? issueId : null;
                  Swal.fire({
                      title: apiResponse.message,
                      icon: apiResponse.status === 'success' ? 'success' : 'error',
                      text: "IssueId: " + issue,
                      allowOutsideClick: false
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
                      title: error.message, icon: 'error', allowOutsideClick: false
                  });
                  playErrorAudio(error.prompt);
              }
          } finally {
              hideLoader();
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

    /*const getOptionsHtml = (options, purpose = 'default') => {
        return Object.entries(options)
            .map(([value, text]) => {
                if (purpose === 'eWallet' && value === 4 && text === 'Request') {
                    return `<option value="${value}" selected>${text}</option>`;
                } else {
                    return `<option value="${value}">${text}</option>`;
                }
            })
            .join('');
    };*/

    if (currentPath === '/') { // home/root path

        const btnCards = document.getElementById('btnCards');
        btnCards.addEventListener('click', handleCardsButtonClick);

        const accountOrLoan = document.getElementById('btnAccountAndLoan');
        accountOrLoan.addEventListener('click', handleAccountOrLoanButtonClick);

        addClickEventWithAsyncHandler('btnAgentBanking', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandler('btnESheba', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));*/

        /*const btnESheba = document.getElementById('btnESheba');
        btnESheba.addEventListener('click', function () {
            showDownloadOptions('esheba');
        });

        const btnSPG = document.getElementById('btnSPG');
        btnSPG.addEventListener('click', function () {
            showDownloadOptions('spg');
        });*/

        const btnESheba = document.getElementById('btnESheba');
        btnESheba.addEventListener('click', function () {
            redirectUserToAppStore('esheba');
        });

        const btnSPG = document.getElementById('btnSPG');
        btnSPG.addEventListener('click', function () {
            redirectUserToAppStore('spg');
        });

        const btnEWallet = document.getElementById('btnEWallet');
        btnEWallet.addEventListener('click', handleEWalletClick);

        const btnIslamiBanking = document.getElementById('btnIslamiBanking');
        btnIslamiBanking.addEventListener('click', handleIslamiBankingClick);

        addClickEventWithAsyncHandler('btnSonaliBankProduct', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        /*addClickEventWithAsyncHandler('btnSPG', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));*/


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

        const btnEWApproveOrReject = document.getElementById('btnEWApproveOrReject');
        btnEWApproveOrReject.addEventListener('click', () => {
            handleAPIRequestWithAccountVerification('EW-APPROVE-OR-REJECT', 'Approve wallet request received.', 'btnEWApproveOrReject');
        });

        const btnEWChangeOrResetEWalletPIN = document.getElementById('btnEWChangeOrResetEWalletPIN');
        btnEWChangeOrResetEWalletPIN.addEventListener('click', () => {
            handleAPIRequestWithAccountVerification('EW-CHANGE-OR-RESET-PIN', 'PIN Change or Reset request received.', 'btnEWChangeOrResetEWalletPIN');
        });

        const btnEWDeviceBind = document.getElementById('btnEWDeviceBind');
        btnEWDeviceBind.addEventListener('click', () => {
            handleAPIRequestWithAccountVerification('EW-DEVICE-BIND', 'Device Bind request received.', 'btnEWDeviceBind');
        });

        const btnEWLockOrBlock = document.getElementById('btnEWLockOrBlock');
        btnEWLockOrBlock.addEventListener('click', () => {
            handleAPIRequestWithAccountVerification('EW-LOCK-BLOCK', 'Wallet Lock Or Block request received.', 'btnEWLockOrBlock');
        });

        const btnEWEWalletClose = document.getElementById('btnEWEWalletClose');
        btnEWEWalletClose.addEventListener('click', () => {
            handleAPIRequestWithAccountVerification('EW-CLOSE-WALLET', 'Wallet closing request received.', 'btnEWEWalletClose');
        });

        const btnEWUnlockOrActive = document.getElementById('btnEWUnlockOrActive');
        btnEWUnlockOrActive.addEventListener('click', () => {
            handleAPIRequestWithAccountVerification('EW-UNLOCK-ACTIVE', 'Wallet Lock or Active request received.', 'btnEWUnlockOrActive');
        });

        addClickEventWithAsyncHandler('btnEWAboutSonaliEWallet', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));

        const btnCreateIssueEWallet = document.getElementById('btnCreateIssueEWallet');
        btnCreateIssueEWallet.addEventListener('click', showCascadingDropdownsForCreatingAnIssue);

        /*const btnCreateIssueEWallet = document.getElementById('btnCreateIssueEWallet');
        btnCreateIssueEWallet.addEventListener('click', showCascadingDropdownsForCreatingAnIssueForEWallet);*/

        /*const btnEWChangeOrResetEWalletPIN = document.getElementById('btnEWChangeOrResetEWalletPIN');
       btnEWChangeOrResetEWalletPIN.addEventListener('click', handleEWChangeOrResetEWalletPINClick);*/

        /*const btnEWDeviceBind = document.getElementById('btnEWDeviceBind');
        btnEWDeviceBind.addEventListener('click', handleEWDeviceBindClick);*/

        /*const btnEWLockOrBlock = document.getElementById('btnEWLockOrBlock');
        btnEWLockOrBlock.addEventListener('click', handleEWLockOrBlockClick);*/

        /*const btnEWUnlockOrActive = document.getElementById('btnEWUnlockOrActive');
        btnEWUnlockOrActive.addEventListener('click', handleEWUnlockOrActiveClick);*/

        /*const btnEWApproveOrReject = document.getElementById('btnEWApproveOrReject');
        btnEWApproveOrReject.addEventListener('click', handleEWApproveOrRejectClick);*/

        /*addClickEventWithAsyncHandler('btnEWEWalletClose', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));*/

        /*const btnEWEWalletClose = document.getElementById('btnEWEWalletClose');
        btnEWEWalletClose.addEventListener('click', handleEWCloseWalletClick);*/

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

    const btnAccountSwitch = document.getElementById('btnAccountSwitch');
    if (btnAccountSwitch) {
        btnAccountSwitch.addEventListener('click', handleAccountSwitchClick);
    } else {
        console.info('Element with ID "btnAccountSwitch" not found.');
    }

    async function handleAccountSwitchClick() {
        try {
            const response = await axios.get('/getac');
            const accounts = response.data;
            console.log('accounts', accounts.acLists)
            showAccountSelectionPopupCommon(accounts.acLists.acList);
        } catch (error) {
            console.error('Error fetching accounts:', error);
        }
    }


    function showAccountSelectionPopupCommon(accounts) {
        // stopAllAudio();
        /*const accountOptions = accounts.map(account => `
            <div class="account-option">
                <p>${account.accountName}</p>
                <p>${account.accountNo}</p>
                <button class="ac-select-button" data-account-id="${account.accEnc}">Select</button>
            </div>`).join('');*/

        const accountOptions = accounts.map(account => `
    <div class="account-option">
        <input type="radio" name="selectedAccount" value="${account.accEnc}" id="account-${account.accEnc}">
        <label for="account-${account.accEnc}">
            <div class="account-details">
                <p>Account Name: ${account.accountName}</p>
                <p>Account No: ${account.accountNo}</p>
            </div>
        </label>
    </div>`).join('');


        /*Swal.fire({
            title: (locale === 'en') ? selectAnAccountEn : selectAnAccountBn,
            html: accountOptions,
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            showConfirmButton: false,
            allowOutsideClick: false
        });*/

        /*Swal.fire({
            title: (locale === 'en') ? selectAnAccountEn : selectAnAccountBn,
            html: `${accountOptions}<button class="ac-submit-button">${(locale === 'en') ? "Submit" : "জমা দিন"}</button>`,
            showCancelButton: true,
            cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল",
            showConfirmButton: false,
            allowOutsideClick: false
        });

        const submitButton = document.querySelector('.ac-submit-button');
        submitButton.addEventListener('click', handleAccountSwitchCommonSubmitButtonClick);*/

        Swal.fire({
            title: (locale === 'en') ? selectAnAccountEn : selectAnAccountBn,
            html: `
        ${accountOptions}
        <div class="button-container">
            <button class="ac-submit-button">${(locale === 'en') ? "Submit" : "জমা দিন"}</button>
            <button class="ac-cancel-button">${(locale === 'en') ? "Cancel" : "বাতিল"}</button>
        </div>
    `,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        const submitButton = document.querySelector('.ac-submit-button');
        const cancelButton = document.querySelector('.ac-cancel-button');

        submitButton.addEventListener('click', handleAccountSwitchCommonSubmitButtonClick);
        cancelButton.addEventListener('click', handleAccountSwitchCommonCancelButtonClick);

        /*document.querySelectorAll('.ac-select-button').forEach(button => {
            button.addEventListener('click', handleSelectCommonButtonClick);
        });*/
    }

    function handleAccountSwitchCommonSubmitButtonClick() {
        const selectedAccountId = document.querySelector('input[name="selectedAccount"]:checked');

        if (selectedAccountId) {
            console.log('Selected Account Id:', selectedAccountId.value);

            // Perform any additional actions here if needed

            axios.post('/save', {"ac": selectedAccountId.value, "purpose": "ACCOUNT-SWITCH"})
                .then(response => handleSaveResponseCommon(response))
                .catch(error => console.error('Error saving selected account:', error));

            Swal.close(); // Close the SweetAlert popup
        } else {
            // Display a message or handle the case where no account is selected
            console.log('No account selected');
        }
    }

    function handleAccountSwitchCommonCancelButtonClick() {
        Swal.close();
    }


    function handleSelectCommonButtonClick() {
        const selectedAccountId = this.getAttribute('data-account-id');
        console.log('selectedAccountId', selectedAccountId);

        axios.post('/save', {"ac": selectedAccountId, "purpose": "ACCOUNT-SWITCH"})
            .then(response => handleSaveResponseCommon(response))
            .catch(error => console.error('Error saving selected account:', error));
    }

    function handleSaveResponseCommon(response) {
        const {data: respData, status: statusCode} = response;

        if (statusCode === 200 && respData.status === 'success') {
            storeData('pn', respData.pn);
            storeData('acn', respData.acn);
            goTo(respData.url);
        } else {
            const audioUrl = respData.prompt;
            playErrorAudio(audioUrl);
            displayErrorMessage(respData.message, errorMessageDiv);
        }
    }


    async function handleEWChangeOrResetEWalletPINClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                showLoader();

                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-CHANGE-OR-RESET-PIN',
                    'reason': 'PIN Change or Reset request received.',
                    'page': 'ewallet',
                    'button': 'btnEWChangeOrResetEWalletPIN',
                });

                hideLoader();
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    allowOutsideClick: false
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWApproveOrReject click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
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

    async function handleEWApproveOrRejectClick_OLD() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {

                showLoader();
                const apiResponse = await callDynamicAPI({
                    'purpose': 'EW-APPROVE-OR-REJECT',
                    'reason': 'Approve wallet request received.',
                    'page': 'ewallet',
                    'button': 'btnEWApproveOrReject',
                });
                hideLoader();

                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    allowOutsideClick: false
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWApproveOrReject click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleAPIRequestWithAccountVerification(apiPurpose, apiReason, btnName, pageName = "") {
        try {
            let locale = getSavedLocale();

            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const {value: accountAndDob} = await Swal.fire({
                    title: (locale === 'en') ? "Enter Account & Date of Birth." : "অ্যাকাউন্ট এবং জন্ম তারিখ লিখুন ।",
                    html: `<input id="swal-input1" class="swal2-input" placeholder="${(locale === 'en' ? 'Account Number' : 'অ্যাকাউন্ট নাম্বার')}"><input id="swal-input2" class="swal2-input" type="date" placeholder="Date of Birth">`,
                    showCancelButton: true,
                    confirmButtonText: (locale === 'en') ? "OK" : "ওকে",
                    cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল",
                    focusConfirm: false,
                    allowOutsideClick: false,
                    preConfirm: () => {
                        const account = Swal.getPopup().querySelector('#swal-input1').value;
                        const dob = Swal.getPopup().querySelector('#swal-input2').value;
                        if (!account || !/^\d{10,20}$/.test(account)) {
                            Swal.showValidationMessage((locale === 'en') ? "Account is required and must be a number with 10 to 20 digits." : "অ্যাকাউন্ট নাম্বার আবশ্যক এবং ১০ থেকে ২০ সংখ্যা বিশিষ্ট হতে হবে।");
                        }
                        if (!dob || !/^\d{4}-\d{2}-\d{2}$/.test(dob)) {
                            Swal.showValidationMessage((locale === 'en') ? "Invalid date format." : "তারিখ ভুল হয়েছে ।");
                        }
                        return {account, dob};
                    }
                });

                if (!accountAndDob) {
                    return;
                }

                const {account, dob} = accountAndDob;
                if (account && dob) {
                    showLoader();

                    const verifyResp = await callDynamicAPI({
                        'purpose': 'USER-INFO-VERIFY',
                        'page': pageName,
                        'button': btnName,
                        'account': account,
                        'dob': dob
                    });

                    hideLoader();
                    // console.log('verifyResp', verifyResp);
                    if (verifyResp.status === 'success') {
                        const apiResponse = await callDynamicAPI({
                            'purpose': apiPurpose, 'page': 'ewallet', 'button': btnName, 'reason': apiReason
                        });

                        hideLoader();

                        Swal.fire({
                            title: apiResponse.message,
                            icon: apiResponse.status === 'success' ? 'success' : 'error',
                            allowOutsideClick: false,
                            confirmButtonText: (locale === 'en') ? "OK" : "ওকে",
                        });
                        playErrorAudio(apiResponse.prompt);
                    } else {
                        // Handle unsuccessful verification
                        Swal.fire({
                            title: verifyResp.message, icon: 'error', allowOutsideClick: false,
                            confirmButtonText: (locale === 'en') ? "OK" : "ওকে",
                        });
                        playErrorAudio(verifyResp.prompt);
                    }
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            hideLoader();
            // console.error('Error:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
                });
                // playErrorAudio(error.prompt);
            }
        }
    }

    /*const someButton = document.getElementById('someButton');
    someButton.addEventListener('click', () => {
        handleAPIRequest('SOME-PURPOSE', 'Some reason for the request');
    });*/

    async function handleEWApproveOrRejectClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // Prompt for NID and date of birth
                const {value: nidAndDob} = await Swal.fire({
                    title: 'Enter NID and Date of Birth',
                    html: '<input id="swal-input1" class="swal2-input" placeholder="National ID">' + '<input id="swal-input2" class="swal2-input" type="date" placeholder="Date of Birth">',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    focusConfirm: false,
                    allowOutsideClick: false,
                    preConfirm: () => {
                        const nid = Swal.getPopup().querySelector('#swal-input1').value;
                        const dob = Swal.getPopup().querySelector('#swal-input2').value;
                        if (!nid || !/^\d{10,17}$/.test(nid)) {
                            Swal.showValidationMessage('National ID is required and must be a number with 10 to 17 digits');
                        }
                        if (!dob || !/^\d{4}-\d{2}-\d{2}$/.test(dob)) {
                            Swal.showValidationMessage('Invalid date format. Please use YYYY-MM-DD');
                        }
                        return {nid, dob};
                    }
                });

                if (!nidAndDob) {
                    return;
                }

                const {nid, dob} = nidAndDob;
                if (nid && dob) {
                    showLoader();
                    let reqObj = {
                        'purpose': 'USER-INFO-VERIFY',
                        'page': 'ewallet',
                        'button': 'btnEWApproveOrReject',
                        'nid': nid,
                        'dob': dob
                    }

                    const verificationResponse = await callDynamicAPI(reqObj);

                    hideLoader();
                    console.log('verificationResponse', verificationResponse);

                    // Proceed with API call if verification is successful
                    if (verificationResponse.status === 'success') {
                        const apiResponse = await callDynamicAPI({
                            'purpose': 'EW-APPROVE-OR-REJECT',
                            'page': 'ewallet',
                            'button': 'btnEWApproveOrReject',
                            'reason': 'Approve wallet request received.'
                        });
                        hideLoader();

                        Swal.fire({
                            title: apiResponse.message,
                            icon: apiResponse.status === 'success' ? 'success' : 'error',
                            allowOutsideClick: false
                        });
                        playErrorAudio(apiResponse.prompt);
                    } else {
                        // Handle unsuccessful verification
                        Swal.fire({
                            title: verificationResponse.message, icon: 'error', allowOutsideClick: false
                        });
                        playErrorAudio(verificationResponse.prompt);
                    }
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            hideLoader();
            // console.error('Error in btnEWApproveOrReject click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
                });
                // playErrorAudio(error.prompt);
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
                    'reason': 'Device Bind request received.',
                    'page': 'ewallet',
                    'button': 'btnEWDeviceBind',
                });
                hideLoader();
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    allowOutsideClick: false
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLAOutstandingLoanBalance click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
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
                    'reason': 'Wallet closing request received.',
                    'page': 'ewallet',
                    'button': 'btnEWEWalletClose',
                });
                hideLoader();
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    allowOutsideClick: false
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWEWalletClose click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
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
                    'reason': 'Wallet Lock Or Block request received.',
                    'page': 'ewallet',
                    'button': 'btnEWLockOrBlock',
                });
                hideLoader();
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    allowOutsideClick: false
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWLockOrBlock click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
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
                    'reason': 'Wallet Lock or Active request received.',
                    'page': 'ewallet',
                    'button': 'btnEWUnlockOrActive',
                });
                hideLoader();

                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    allowOutsideClick: false
                });
                playErrorAudio(apiResponse.prompt);

            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnEWUnlockOrActive click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error', allowOutsideClick: false
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
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
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
    }


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

    function redirectUserToAppStore(appType) {
        let appStoreLink;

        if (appType === 'esheba') {
            // Choose the appropriate link based on the user's platform
            appStoreLink = isIOS() ? eShebaiOS : eShebaAndroid;
        } else if (appType === 'spg') {
            // Choose the appropriate link based on the user's platform
            appStoreLink = isIOS() ? SPGiOS : SPGAndroid;
        }

        if (appStoreLink) {
            // Redirect the user to the selected app store link
            window.location.href = appStoreLink;
            return false;
        } else {
            // Handle unsupported platform or other error
            console.error('Unsupported platform or invalid app type');
        }
    }

    /*function showDownloadOptions(appName) {
        let locale = getSavedLocale();
        Swal.fire({
            title: (locale === 'en') ? 'Select your app store' : 'অ্যাপ স্টোর নির্বাচন করুন',
            showCancelButton: true,
            confirmButtonText: '<img class="transparent-button" src="/img/app-store.png" alt="App Store" width="50" height="50">',
            cancelButtonText: '<img class="transparent-button" src="/img/google-play.png" alt="Play Store" width="50" height="50">',
            reverseButtons: true,
            focusConfirm: false,
            focusCancel: false
        }).then((result) => {
            if (result.isConfirmed) { // apple appStore
                redirectToAppStore(appName);
            } else if (result.dismiss === Swal.DismissReason.cancel) { // google playStore
                redirectToPlayStore(appName);
            }
        });
    }

    function redirectToAppStore(appName) {
        let appUrl = (appName === 'esheba') ? eShebaiOS : SPGiOS;
        console.log('appUrl', appUrl);
        window.open(appUrl, '_blank');
        // window.location.href = appUrl;
        return false;
    }

    function redirectToPlayStore(appName) {
        let appUrl = (appName === 'esheba') ? eShebaAndroid : SPGAndroid;
        console.log('appUrl', appUrl);
        window.open(appUrl, '_blank');
        // window.location.href = appUrl;
        return false;
    }*/
});
