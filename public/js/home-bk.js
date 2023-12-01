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

    async function handleCreateIssueClick() {
        try {
            // Fetch dropdown values from the API
            const dropdownValuesResponse = await callDynamicAPI({
                'purpose': 'GET-DROP-DOWN-VALUES', 'page': 'home', 'button': 'createIssue',
            });

            const dropdownValues = dropdownValuesResponse.data;

            const {value: selectedValues, dismiss, inputValue: reasonInput} = await Swal.fire({
                title: 'Create Complaint',
                html: `<label for="callTypeSelect">Call Type:</label>
                <select id="callTypeSelect" class="swal2-input" style="width: 100% !important;" placeholder="Call Type" required>
                    <option value="" disabled selected>Select an option</option>
                    ${getOptionsHtml(dropdownValues.callType)}
                </select>
                <label for="callCategorySelect">Call Category:</label>
                <select id="callCategorySelect" class="swal2-input" style="width: 100% !important;" placeholder="Select Call Category" required>
                    <option value="" disabled selected>Select an option</option>
                    ${getOptionsHtml(dropdownValues.callCategory)}
                </select>
                <label for="reasonInput">Reason:</label>
                <input id="reasonInput" class="swal2-input" style="width: 100% !important;" placeholder="Type the reason" required />`,
                focusConfirm: false,
                preConfirm: () => {
                    const callTypeOpts = document.getElementById('callTypeSelect').value;
                    const callCategoryOpts = document.getElementById('callCategorySelect').value;
                    const reason = document.getElementById('reasonInput').value;

                    // Validate that all required fields are filled
                    if (!callTypeOpts || !callCategoryOpts || !reason) {
                        Swal.showValidationMessage('Please fill in all required fields');
                    }

                    return {
                        callTypeOpts, callCategoryOpts, reason
                    };
                },
                showCancelButton: true
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
    }


    const getOptionsHtml = (options) => {
        return Object.entries(options).map(([value, text]) => `<option value="${value}">${text}</option>`).join('');
    };


    /* async function handleCreateIssueClick() {
        try {
            // const isLoggedIn = await checkLoginStatus();
            // if (isLoggedIn) {
            const reason = await enterReason('Reason for creating the issue?', "Please enter the reason for creating an issue.", 'enter-reason-creating-issue');

            if (reason.isConfirmed) {
                const apiResponse = await callDynamicAPI({
                    'purpose': 'createIssue', 'page': 'home', 'button': 'btnCreateIssue', 'reason': reason.value
                });
                console.log('apiResponse', apiResponse)

                const issueId = apiResponse.data?.issueId;
                const issue = issueId ? issueId : null;
                Swal.fire({
                    title: apiResponse.message,
                    icon: apiResponse.status === 'success' ? 'success' : 'error',
                    text: "IssueId: " + issue
                });
                playErrorAudio(apiResponse.prompt);
            }
            // } else {
            //     showVerificationAlert();
            // }
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

    console.log('currentPath: ' + currentPath)
    if (currentPath === '/') {

        const btnCards = document.getElementById('btnCards');
        btnCards.addEventListener('click', handleCardsButtonClick);

        const accountOrLoan = document.getElementById('btnAccountAndLoan');
        accountOrLoan.addEventListener('click', handleAccountOrLoanButtonClick);

        /*const btnAgentBanking = document.getElementById('btnAgentBanking');
        btnAgentBanking.addEventListener('click', handleAgentBankingClick);*/

        addClickEventWithAsyncHandler('btnAgentBanking', showMessageForHelp);

        /*const btnESheba = document.getElementById('btnESheba');
        btnESheba.addEventListener('click', handleEShebaClick);*/

        const btnESheba = document.getElementById('btnESheba');
        btnESheba.addEventListener('click', handleEShebaClickToAppStore);

        const btnEWallet = document.getElementById('btnEWallet');
        btnEWallet.addEventListener('click', handleEWalletClick);

        const btnIslamiBanking = document.getElementById('btnIslamiBanking');
        btnIslamiBanking.addEventListener('click', handleIslamiBankingClick);

        // const btnSonaliBankProduct = document.getElementById('btnSonaliBankProduct');
        // btnSonaliBankProduct.addEventListener('click', handleSonaliBankProductClick);

        addClickEventWithAsyncHandler('btnSonaliBankProduct', showMessageForHelp);

        // const btnSPG = document.getElementById('btnSPG');
        // btnSPG.addEventListener('click', handleSPGClick);

        addClickEventWithAsyncHandler('btnSPG', showMessageForHelp);

        // addClickEventWithAsyncHandler('btnAccountOrLoan', showMessageForHelp);

        // Event listener for creation issue button
        const btnCreateIssue = document.getElementById('btnCreateIssue');
        btnCreateIssue.addEventListener('click', handleCreateIssueClick);

        // Handle click events on the buttons


        // addClickEventWithAsyncHandler('btnIslamiBanking', showMessageForHelp);
        // addClickEventWithAsyncHandler('btnEWallet', showMessageForHelp);

        // addClickEventWithAsyncHandler('btnESheba', showMessageForHelp);

        /*// Event listener for create issue button
        const btnLockWallet = document.getElementById('btnLockWallet');
        btnLockWallet.addEventListener('click', handleLockWalletClick);

        // Event listener for device binding button
        const btnDeviceBind = document.getElementById('btnDeviceBind');
        btnDeviceBind.addEventListener('click', handleDeviceBindClick);

        // Event listener for reset PIN button
        const btnResetPin = document.getElementById('btnResetPin');
        btnResetPin.addEventListener('click', handleResetPin);

        // Event listener for card activation button
        const btnCardActivate = document.getElementById('btnCardActivate');
        btnCardActivate.addEventListener('click', handleCardActivateClick);

        // Handle click events on the buttons
        addClickEventWithAsyncHandler('btnPaymentInfo', showMessageForHelp);
        addClickEventWithAsyncHandler('btnStatement', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCardDetails', showMessageForHelp);
        addClickEventWithAsyncHandler('btnAgentAssist', showMessageForHelp);*/


    } // end of pathname detect and conditionally assign event listener
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

        addClickEventWithAsyncHandler('btnLALoanClosureProcess', showMessageForHelp);
        addClickEventWithAsyncHandler('btnLALoanDetails', showMessageForHelp);

        const btnLADueDateInstallment = document.getElementById('btnLADueDateInstallment');
        btnLADueDateInstallment.addEventListener('click', handleLADueDateInstallmentClick);

        const btnLAOutstandingLoanBalance = document.getElementById('btnLAOutstandingLoanBalance');
        btnLAOutstandingLoanBalance.addEventListener('click', handleLAOutstandingLoanBalanceClick);

    } else if (currentPath === '/fixed-deposit') {

        addClickEventWithAsyncHandler('btnFDEncashmentProcess', showMessageForHelp);
        addClickEventWithAsyncHandler('btnFDFixedDepositDetails', showMessageForHelp);
        addClickEventWithAsyncHandler('btnFDMaturityDate', showMessageForHelp);

    } else if (currentPath === '/account-dps') {

        addClickEventWithAsyncHandler('btnALAccountDPSAvailableBalance', showMessageForHelp);
        addClickEventWithAsyncHandler('btnALAccountClosureProcess', showMessageForHelp);
        addClickEventWithAsyncHandler('btnALAccountDPSEncashmentProcess', showMessageForHelp);
        addClickEventWithAsyncHandler('btnALAccountDPSInstalmentDetails', showMessageForHelp);

    } else if (currentPath === '/ib-account-related') {


        const btnIBARChequeBookLeaf = document.getElementById('btnIBARChequeBookLeaf');
        btnIBARChequeBookLeaf.addEventListener('click', handleIBARChequeBookLeafClick);

        addClickEventWithAsyncHandler('btnIBARActivateSmsBanking', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIBARAvailableBalance', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIBARFundTransferServices', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIBARMiniStatement', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIBARChequeBookRequisition', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIBARIslamicBankingProducts', showMessageForHelp);

    } else if (currentPath === '/ewallet') {

        const btnEWChangeOrResetEWalletPIN = document.getElementById('btnEWChangeOrResetEWalletPIN');
        btnEWChangeOrResetEWalletPIN.addEventListener('click', handleEWChangeOrResetEWalletPINClick);

        const btnEWDeviceBind = document.getElementById('btnEWDeviceBind');
        btnEWDeviceBind.addEventListener('click', handleEWDeviceBindClick);

        const btnEWLockOrBlock = document.getElementById('btnEWLockOrBlock');
        btnEWLockOrBlock.addEventListener('click', handleEWLockOrBlockClick);

        const btnEWUnlockOrActive = document.getElementById('btnEWUnlockOrActive');
        btnEWUnlockOrActive.addEventListener('click', handleEWUnlockOrActiveClick);

        addClickEventWithAsyncHandler('btnEWAboutSonaliEWallet', showMessageForHelp);
        addClickEventWithAsyncHandler('btnEWApproveOrReject', showMessageForHelp);
        addClickEventWithAsyncHandler('btnEWEWalletClose', showMessageForHelp);

    } else if (currentPath === '/esheba') {

        addClickEventWithAsyncHandler('btnESAccountOpening', showMessageForHelp);
        addClickEventWithAsyncHandler('btnESAboutSonaliESheba', showMessageForHelp);
        addClickEventWithAsyncHandler('btnESOtherServices', showMessageForHelp);

    } else if (currentPath === '/credit-card') {

        addClickEventWithAsyncHandler('btnCCreditCardActivation', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditCardBlock', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditChangeOrResetPIN', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditECommerceActivationOrDeactivation', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditGreenPINGeneration', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditMiniStatement', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditOutstandingBDT', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditOutstandingUSD', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCCreditCardPayment', showMessageForHelp);

    } else if (currentPath === '/debit-card') {
        addClickEventWithAsyncHandler('btnDCDebitCardActivation', showMessageForHelp);
        addClickEventWithAsyncHandler('btnDCDebitCardBlock', showMessageForHelp);
        addClickEventWithAsyncHandler('btnDCDebitChangeOrResetPIN', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCDebitECommerceActivationOrDeactivation', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCDebitGreenPINGeneration', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCDebitMiniStatement', showMessageForHelp);
    } else if (currentPath === '/prepaid-card') {
        addClickEventWithAsyncHandler('btnCPrepaidCardActivation', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCPrepaidCardBlock', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCPrepaidChangeOrResetPIN', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCPrepaidECommerceActivationOrDeactivation', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCPrepaidGreenPINGeneration', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCPrepaidMiniStatement', showMessageForHelp);
    } else if (currentPath === '/agent-banking') {
        addClickEventWithAsyncHandler('btnABAgentBankingServices', showMessageForHelp);
    } else if (currentPath === '/casasnd') {

        const btnChequeBookLeaf = document.getElementById('btnChequeBookLeaf');
        btnChequeBookLeaf.addEventListener('click', handleChequeBookLeafClick);

        const btnCASAActivateSMSBanking = document.getElementById('btnCASAActivateSMSBanking');
        btnCASAActivateSMSBanking.addEventListener('click', handleCASAActivateSMSBankingClick);

        addClickEventWithAsyncHandler('btnCASAMiniStatement', showMessageForHelp);
        addClickEventWithAsyncHandler('btnFundTransferServices', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCASAAvailableBalance', showMessageForHelp);
        addClickEventWithAsyncHandler('btnChequeBookRequisition', showMessageForHelp);
        addClickEventWithAsyncHandler('btnAccountClosureProcess', showMessageForHelp);
    } else if (currentPath === '/ib-loans-advances') {

        addClickEventWithAsyncHandler('btnIBLALoanClosureProcess', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIBLALoanDetails', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIBLAOutstandingLoanBalance', showMessageForHelp);
    }


    async function handleEWChangeOrResetEWalletPINClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Enter your NID number.', "Please enter your NID number here.", 'enter-nid-for-ew-change-reset-pin');

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
    }

    async function handleEWDeviceBindClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Enter your NID number.', "Please enter your NID number here.", 'enter-nid-for-ew-device-bind');

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
    }

    async function handleEWLockOrBlockClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Enter your NID number.', "Please enter your NID number here.", 'enter-nid-for-ew-lock-block');

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
    }

    async function handleEWUnlockOrActiveClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Enter your NID number.', "Please enter your NID number here.", 'enter-nid-for-ew-unlock-active');

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
    }

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

    async function handleLAOutstandingLoanBalanceClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Please enter loan amount.', "Please enter Sanction/Renewed/Re-Schedule loan amount here.", 'enter-outstanding-loan-amount-due-date-installment');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'LA-OUTSTANDING-LOAN-BALANCE',
                        'page': 'btnLAOutstandingLoanBalance',
                        'button': 'handleLAOutstandingLoanBalanceClick',
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

    async function showMessageForHelp() {
        // Show the loader while the async operation is in progress
        showLoader();

        // Simulate an async operation (e.g., API call or any other processing)
        // Here, we use setTimeout to simulate the async operation
        await new Promise(resolve => setTimeout(resolve, 500));

        // Close the loader
        hideLoader();

        // Play the error audio
        playErrorAudio('/uploads/prompts/common/call-for-help-bn.m4a');

        // Show the message for help with the "Call" button
        const result = await Swal.fire({
            icon: 'info',
            title: 'Service Not Available',
            text: 'Please call 16639 to get help.',
            showCancelButton: true,
            confirmButtonText: 'Call',
            cancelButtonText: 'OK',
            reverseButtons: true, // To switch the "Call" and "OK" buttons' positions
        });

        // Call the "goTo" function if the user clicked the "Call" button
        if (result.isConfirmed) {
            goTo('tel:' + helpCenterNumber);
        }
    }

    // Helper function to add click event listener with async function
    function addClickEventWithAsyncHandler(elementId, asyncHandler) {
        document.getElementById(elementId).addEventListener('click', asyncHandler);
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


});
