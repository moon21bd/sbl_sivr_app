document.addEventListener("DOMContentLoaded", function () {
    let locale = getSavedLocale();

    const currentPath = window.location.pathname;
    console.log(currentPath);

    async function callDynamicAPI(data) {
        try {
            const response = await axios.post(callDynamically, data);
            return response.data;
        } catch (error) {
            throw error.response.data;
        }
    }

    const getOptionsHtml = (options) => {
        return Object.entries(options)
            .map(([value, text]) => `<option value="${value}">${text}</option>`)
            .join("");
    };

    async function enterReason(title, message, audioFile) {
        const popupAudio = new Audio(`/uploads/prompts/${audioFile}.m4a`);
        await popupAudio.play();

        return Swal.fire({
            title: title,
            input: "text",
            inputAttributes: {
                autocapitalize: "off",
            },
            showCancelButton: true,
            confirmButtonText: "Submit",
            showLoaderOnConfirm: false,
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true,
            inputValidator: (reason) => {
                if (!reason) {
                    popupAudio.play();
                    return message;
                }
                return null;
            },
            willClose: () => {
                stopAllAudioPlayback();
            },
        }).then((result) => {
            if (popupAudio) {
                popupAudio.pause();
            }
            return result;
        });
    }

    async function showCascadingDropdownsForCreatingAnIssue() {
        commonCloseNav();

        let locale = getSavedLocale();
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                try {
                    showLoader();
                    const callCategoryDropdownValuesResponse =
                        await callDynamicAPI({
                            purpose: "GET-CALL-CATEGORY-OPTIONS",
                            page: "home",
                            button: "createIssue",
                        });

                    const callCategoryDropdownValues =
                        callCategoryDropdownValuesResponse.data;
                    hideLoader();

                    let textCallType =
                        locale === "en" ? "Call Type" : "কল টাইপ";
                    let textCallCategory =
                        locale === "en" ? "Complaint Type" : "অভিযোগের ধরণ";
                    let textCallSubCategory =
                        locale === "en"
                            ? "Complaint Sub Type"
                            : "অভিযোগের উপ ধরণ";
                    let textCallSubSubCategory =
                        locale === "en"
                            ? "Complaint Sub Sub Type"
                            : "অভিযোগের উপ-উপ ধরণ";
                    let textSelectSubCallCategory =
                        locale === "en"
                            ? "Select Sub Category"
                            : "অভিযোগের উপ ধরণ নির্বাচন করুন";
                    let textSelectCallType =
                        locale === "en" ? "Select Type" : "টাইপ নির্বাচন করুন";
                    let textSelectCallCategory =
                        locale === "en"
                            ? "Select Complaint Type"
                            : "অভিযোগের ধরণ নির্বাচন করুন";
                    let textSelectSubSubCallCategory =
                        locale === "en"
                            ? "Select Sub Sub Complaint Type"
                            : "অভিযোগের উপ-উপ ধরণ নির্বাচন করুন";
                    let textReason =
                        locale === "en" ? "Reason" : "অভিযোগের কারণ";
                    let textEmail = locale === "en" ? "Email" : "ইমেইল";
                    let textSubmitComplaint =
                        locale === "en" ? "Submit Complaint" : "অভিযোগ জমা দিন";

                    const swalOptions = {
                        // title: textSubmitComplaint,
                        title: `<h3 class="complaint-title"> ${textSubmitComplaint}</h3>`,
                        html: `
                <div>
                <label for="callCategorySelect">${textCallCategory}:</label>
                </div>

                <select id="callCategorySelect" class="swal2-input select2" style="width: 100% !important;" placeholder="${textSelectCallCategory}" required>
                    <option value="" disabled selected>${textSelectCallCategory}</option>
                    ${getOptionsHtml(callCategoryDropdownValues)}
                </select>
                <div>
                <label for="callSubCategorySelect">${textCallSubCategory}:</label>
                </div>
                <select id="callSubCategorySelect" class="swal2-input select2" style="width: 100% !important;" placeholder="${textCallSubCategory}" required>
                    <option value="" disabled selected>${textSelectSubCallCategory}</option>
                </select>
                <div>
                <label for="callSubSubCategorySelect">${textCallSubSubCategory}:</label>
                </div>
                <select id="callSubSubCategorySelect" class="swal2-input select2" style="width: 100% !important;" placeholder="${textSelectSubSubCallCategory}" required>
                    <option value="" disabled selected>${textSelectSubSubCallCategory}</option>
                </select>

                <div>
                <label for="reasonInput">${textReason}:</label>
                </div>
                <input id="reasonInput" class="swal2-input" style="width: 100% !important;" placeholder="${textReason}" required />

                <div>
                <label for="emailInput">${textEmail}:</label>
                </div>
                <input id="emailInput" class="swal2-input" style="width: 100% !important;" placeholder="${textEmail}" />

                `,
                        focusConfirm: false,
                        preConfirm: () => {
                            // const callTypeOpts = document.getElementById('callTypeSelect').value;
                            const callTypeOpts = 2; // service request
                            const callCategoryOpts =
                                document.getElementById(
                                    "callCategorySelect"
                                ).value;
                            const callSubCategoryOpts = document.getElementById(
                                "callSubCategorySelect"
                            ).value;
                            const callSubSubCategoryOpts =
                                document.getElementById(
                                    "callSubSubCategorySelect"
                                ).value;
                            const reason =
                                document.getElementById("reasonInput").value;
                            const email =
                                document.getElementById("emailInput").value;

                            if (
                                !callTypeOpts ||
                                !callCategoryOpts ||
                                !callSubCategoryOpts ||
                                !callSubSubCategoryOpts ||
                                !reason
                            ) {
                                Swal.showValidationMessage(
                                    locale === "en"
                                        ? "Please fill in all required fields."
                                        : "দয়া করে সবগুলো তথ্যই প্রদান করুন । "
                                );
                            }

                            return {
                                callTypeOpts,
                                callCategoryOpts,
                                callSubCategoryOpts,
                                callSubSubCategoryOpts,
                                reason,
                                email,
                            };
                        },
                        showCancelButton: true,
                        customClass: {
                            container: "complaint-swal-bg",
                        },
                        confirmButtonText:
                            locale === "en" ? "Submit" : "জমা দিন",
                        cancelButtonText: locale === "en" ? "Cancel" : "বাতিল",
                        didOpen: () => {
                            const callTypeSelect = 2;
                            const callCategorySelect =
                                document.getElementById("callCategorySelect");
                            const callSubCategorySelect =
                                document.getElementById(
                                    "callSubCategorySelect"
                                );
                            const callSubSubCategorySelect =
                                document.getElementById(
                                    "callSubSubCategorySelect"
                                );

                            // CALL CATEGORY EVENT
                            callCategorySelect.addEventListener(
                                "change",
                                async () => {
                                    const callTypeVal = callTypeSelect;
                                    const callCategoryVal =
                                        callCategorySelect.value;
                                    const callSubCategoryVal =
                                        callSubCategorySelect.value;
                                    console.log(
                                        "callTypeVal",
                                        callTypeVal,
                                        "callCategoryVal",
                                        callCategoryVal,
                                        "callSubCategoryVal",
                                        callSubCategoryVal
                                    );

                                    const callSubCategories =
                                        await fetchDropdownOptions(
                                            "callSubCategorySelect",
                                            {
                                                callType: callTypeVal,
                                                callCategory: callCategoryVal,
                                            }
                                        );

                                    const subCategoryDataValues =
                                        callSubCategories.data;
                                    callSubCategorySelect.innerHTML =
                                        `<option value="" disabled selected>${textSelectSubCallCategory}</option>` +
                                        getOptionsHtml(subCategoryDataValues);
                                }
                            );

                            // CALL SUB CATEGORY EVENT
                            callSubCategorySelect.addEventListener(
                                "change",
                                async () => {
                                    const callTypeVal = callTypeSelect;
                                    const callCategoryVal =
                                        callCategorySelect.value;
                                    const callSubCategoryVal =
                                        callSubCategorySelect.value;
                                    const callSubSubCategoryVal =
                                        callSubSubCategorySelect.value;

                                    const callSubSubCategories =
                                        await fetchDropdownOptions(
                                            "callSubSubCategorySelect",
                                            {
                                                callType: callTypeVal,
                                                callCategory: callCategoryVal,
                                                callSubCategory:
                                                    callSubCategoryVal,
                                            }
                                        );

                                    const subSubCategoryDataValues =
                                        callSubSubCategories.data;
                                    // console.log('subSubCategoryDataValues', subSubCategoryDataValues)
                                    callSubSubCategorySelect.innerHTML =
                                        `<option value="" disabled selected>${textSelectSubSubCallCategory}</option>` +
                                        getOptionsHtml(
                                            subSubCategoryDataValues
                                        );
                                }
                            );
                        },
                        willClose: () => {
                            stopAllAudioPlayback();
                        },
                    };

                    const { value: selectedValues, dismiss } = await Swal.fire(
                        swalOptions
                    );

                    if (selectedValues && !dismiss) {
                        const {
                            callTypeOpts,
                            callCategoryOpts,
                            callSubCategoryOpts,
                            callSubSubCategoryOpts,
                            reason,
                            email,
                        } = selectedValues;

                        /*console.log('callTypeOpts', callTypeOpts, 'callCategoryOpts', callCategoryOpts, 'callSubCategoryOpts', callSubCategoryOpts, 'callSubSubCategoryOpts', callSubSubCategoryOpts, 'reason', reason, 'email', email);*/

                        const apiResponse = await callDynamicAPI({
                            purpose: "CREATEISSUE",
                            page: "home",
                            button: "btnCreateIssue",
                            ...selectedValues,
                        });
                        // console.log('apiResponse', apiResponse);

                        const issueId = apiResponse.data?.issueId;
                        const issue = issueId ? issueId : null;
                        Swal.fire({
                            html: `<img class="" src="./img/icon/checkmark.svg" />
                        <h2 class="swal2-title"> ${apiResponse.message} </h2>
                        <p>${"IssueId: " + issue}</p>
                        `,
                            allowOutsideClick: false, // text: "IssueId: " + issue,
                            confirmButtonText:
                                locale === "en" ? "OK" : "ঠিক আছে",
                            customClass: {
                                container: "issueid-swal-bg",
                            },
                            willClose: () => {
                                stopAllAudioPlayback();
                            },
                        });
                        playErrorAudio(apiResponse.prompt);
                    } else if (dismiss === Swal.DismissReason.cancel) {
                        // Handle cancel action if needed
                        hideLoader();
                    }
                } catch (error) {
                    console.error("Error in btnCreateIssue click:", error);
                    if (error.status === "error") {
                        Swal.fire({
                            html: `<img class="" src="./img/icon/lock-card.svg" />
                        <h2 class="swal2-title"> ${error.message} </h2>
                        `,
                            allowOutsideClick: false,
                            customClass: {
                                container: "issueid-swal-bg",
                            },
                            willClose: () => {
                                stopAllAudioPlayback();
                            },
                        });
                        playErrorAudio(error.prompt);
                    }
                } finally {
                    hideLoader();
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in showActiveYourServiceMessage :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                    focusConfirm: false,
                    allowOutsideClick: false,
                    customClass: {
                        container: "active-your-service-swal-bg",
                    },
                    willClose: () => {
                        stopAllAudioPlayback();
                    },
                });
                // playErrorAudio(error.prompt);
            }
        }
    }

    async function fetchDropdownOptions(targetDropdownId, selectedValues = {}) {
        let purpose;
        switch (targetDropdownId) {
            case "callCategorySelect":
                purpose = "GET-CALL-CATEGORY-OPTIONS";
                break;
            case "callSubCategorySelect":
                purpose = "GET-SUB-CATEGORY-OPTIONS";
                break;
            case "callSubSubCategorySelect":
                purpose = "GET-SUB-SUB-CATEGORY-OPTIONS";
                break;
            // Add more cases if needed for additional dropdowns
        }
        return callDynamicAPI({
            purpose: purpose,
            selectedValues: selectedValues,
            button: targetDropdownId,
            page: "home",
        });
    }

    // if (currentPath === '/') { // home/root path

    addClickEventWithAsyncHandler("btnCards", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnCardsMenu", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    const accountOrLoan = document.getElementById("btnAccountAndLoan");
    if (accountOrLoan) {
        accountOrLoan.addEventListener("click", handleAccountOrLoanButtonClick);
    }

    const greenPIN = document.getElementById("btnGreenPIN");
    if (greenPIN) {
        greenPIN.addEventListener("click", handleGreenPINClick);
    }

    const btnGPPINSet = document.getElementById("btnGPPINSet");
    if (btnGPPINSet) {
        btnGPPINSet.addEventListener("click", function () {
            callGreenPINAPI();
        });
    }

    addClickEventWithAsyncHandler("btnGPPINLockUnlock", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnGPPINChange", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnGPCardActive", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnAgentBanking", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnAgentBankingMenu", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    const btnESheba = document.getElementById("btnESheba");
    if (btnESheba) {
        btnESheba.addEventListener("click", function () {
            redirectUserToAppStore("esheba");
        });
    }

    const btnEShebaMenu = document.getElementById("btnEShebaMenu");
    if (btnEShebaMenu) {
        btnEShebaMenu.addEventListener("click", function () {
            redirectUserToAppStore("esheba");
        });
    }

    const btnSPG = document.getElementById("btnSPG");
    if (btnSPG) {
        btnSPG.addEventListener("click", function () {
            goTo("https://sbl.com.bd:7070/");
        });
    }

    const btnSPGMenu = document.getElementById("btnSPGMenu");
    if (btnSPGMenu) {
        btnSPGMenu.addEventListener("click", function () {
            goTo("https://sbl.com.bd:7070/");
        });
    }

    const btnEWallet = document.getElementById("btnEWallet");
    if (btnEWallet) {
        btnEWallet.addEventListener("click", handleEWalletClick);
    }

    const btnEWalletDisable = document.getElementById("btnEWalletDisable");

    if (btnEWalletDisable) {
        btnEWalletDisable.addEventListener("click", function () {
            let title =
                locale === "en" ? eWalletDisableTitleEn : eWalletDisableTitleBn;
            let text =
                locale === "en" ? eWalletDisableTextEn : eWalletDisableTextBn;
            showActiveYourServiceMessage(title, text);
        });
    }
    const btnEWalletDisableMenu = document.getElementById(
        "btnEWalletDisableMenu"
    );

    if (btnEWalletDisableMenu) {
        btnEWalletDisableMenu.addEventListener("click", function () {
            let title =
                locale === "en" ? eWalletDisableTitleEn : eWalletDisableTitleBn;
            let text =
                locale === "en" ? eWalletDisableTextEn : eWalletDisableTextBn;
            showActiveYourServiceMessage(title, text);
        });
    }

    attachClickEvent("btnSonaliBankProduct", sonaliProductPDFLink);
    attachClickEvent("btnSonaliBankProductMenu", sonaliProductPDFLink);
    attachClickEvent("btnIslamiBanking", islamiBankingPDFLink);
    attachClickEvent(
        "btnIslamiBankingMenu",
        islamiBankingPDFLink,
        commonCloseNav
    );

    function attachClickEvent(buttonId, pdfPath, commonAction = null) {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener("click", function () {
                if (commonAction) {
                    commonAction();
                }
                openPdf(pdfPath);
            });
        }
    }

    function openPdf(pdfPath) {
        window.open(pdfPath, "_blank");
    }

    const btnCreateIssue = document.getElementById("btnCreateIssue");
    if (btnCreateIssue) {
        btnCreateIssue.addEventListener(
            "click",
            showCascadingDropdownsForCreatingAnIssue
        );
    }
    const btnCreateIssueMenu = document.getElementById("btnCreateIssueMenu");
    if (btnCreateIssueMenu) {
        btnCreateIssueMenu.addEventListener(
            "click",
            showCascadingDropdownsForCreatingAnIssue
        );
    }

    // } else if (currentPath === '/cards') { // end of pathname detects and conditionally assigns event listener

    const btnDebitCardActivate = document.getElementById("btnDebitCard");
    if (btnDebitCardActivate) {
        btnDebitCardActivate.addEventListener(
            "click",
            handleDebitCardPageClick
        );
    }

    const btnCreditCard = document.getElementById("btnCreditCard");
    if (btnCreditCard) {
        btnCreditCard.addEventListener("click", handleCreditCardPageClick);
    }

    const btnPrepaidCard = document.getElementById("btnPrepaidCard");
    if (btnPrepaidCard) {
        btnPrepaidCard.addEventListener("click", handlePrePaidCardPageClick);
    }

    // } else if (currentPath === '/account-and-loan') {

    const btnCASASND = document.getElementById("btnCASASND");
    if (btnCASASND) {
        btnCASASND.addEventListener("click", handleCASASNDClick);
    }

    const btnDPS = document.getElementById("btnDPS");
    if (btnDPS) {
        btnDPS.addEventListener("click", handleDPSClick);
    }

    /*const btnFixedDeposit = document.getElementById('btnFixedDeposit');
        if (btnFixedDeposit) {
            btnFixedDeposit.addEventListener('click', handleFixedDepositClick);
        }*/

    addClickEventWithAsyncHandler("btnFixedDeposit", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    const btnLoansAndAdvances = document.getElementById("btnLoansAndAdvances");
    if (btnLoansAndAdvances) {
        btnLoansAndAdvances.addEventListener(
            "click",
            handleLoansAndAdvancesClick
        );
    }

    // } else if (currentPath === '/islami-banking') {

    const btnIBAccountRelated = document.getElementById("btnIBAccountRelated");
    if (btnIBAccountRelated) {
        btnIBAccountRelated.addEventListener(
            "click",
            handleIBAccountRelatedClick
        );
    }

    const btnIBLoansAndAdvances = document.getElementById(
        "btnIBLoansAndAdvances"
    );
    if (btnIBLoansAndAdvances) {
        btnIBLoansAndAdvances.addEventListener(
            "click",
            handleIBARLoansAndAdvancesClick
        );
    }

    //  } else if (currentPath === '/sonali-products') {

    addClickEventWithAsyncHandler("btnSBDepositProducts", showMessageForHelp);
    addClickEventWithAsyncHandler(
        "btnSBPForeignCurrencyExchangeRate",
        showMessageForHelp
    );
    addClickEventWithAsyncHandler("btnSBPLoanProducts", showMessageForHelp);
    addClickEventWithAsyncHandler("btnSBPOtherProducts", showMessageForHelp);
    addClickEventWithAsyncHandler(
        "btnSBPScheduleOfCharges",
        showMessageForHelp
    );

    // } else if (currentPath === '/spg') {

    addClickEventWithAsyncHandler(
        "btnSPGBlazeRemittanceServices",
        showMessageForHelp
    );
    addClickEventWithAsyncHandler(
        "btnSPGChallanRelatedServices",
        showMessageForHelp
    );
    addClickEventWithAsyncHandler(
        "btnSPGEducationalFeesPayment",
        showMessageForHelp
    );
    addClickEventWithAsyncHandler(
        "btnSPGInternetBankingServices",
        showMessageForHelp
    );
    addClickEventWithAsyncHandler(
        "btnSPGOnlineBillPaymentService",
        showMessageForHelp
    );
    addClickEventWithAsyncHandler(
        "btnSPGSonaliBankAccountToBkash",
        showMessageForHelp
    );

    // } else if (currentPath === '/loans-advances') {

    addClickEventWithAsyncHandler("btnLALoanClosureProcess", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnLALoanDetails", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnLADueDateInstallment", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnLAOutstandingLoanBalance",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    // } else if (currentPath === '/fixed-deposit') {

    addClickEventWithAsyncHandler("btnFDEncashmentProcess", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnFDFixedDepositDetails",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnFDMaturityDate", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    // } else if (currentPath === '/account-dps') {

    addClickEventWithAsyncHandler(
        "btnALAccountDPSAvailableBalance",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnALDPSDetails", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnALAccountDPSEncashmentProcess",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    /*addClickEventWithAsyncHandler('btnALAccountDPSInstalmentDetails', (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));*/
    const btnALAccountDPSInstalmentDetails = document.getElementById(
        "btnALAccountDPSInstalmentDetails"
    );

    if (btnALAccountDPSInstalmentDetails) {
        btnALAccountDPSInstalmentDetails.addEventListener("click", async () => {
            await handleALAccountDPSInstalmentDetails();
        });
    }

    async function handleALAccountDPSInstalmentDetails() {
        let locale = getSavedLocale();
        showLoader();
        try {
            const apiResp = await callDynamicAPI({
                purpose: "ACC-LOAN-DPS-INST-DETAILS",
                page: "account-dps",
                button: "btnALAccountDPSInstalmentDetails",
            });

            Swal.fire({
                title:
                    locale === "en"
                        ? "Instalment Details"
                        : "ইনস্টলমেন্ট ডিটেইলস",
                text: apiResp.message,
                icon: "success",
                confirmButtonText: locale === "en" ? "Ok" : "ঠিক আছে",
                cancelButtonText: locale === "en" ? "Cancel" : "বাতিল",
                focusConfirm: false,
                allowOutsideClick: false,
                customClass: {
                    container: "active-your-service-swal-bg",
                },
            });
        } catch (error) {
            Swal.fire({
                title:
                    locale === "en"
                        ? "Instalment Details"
                        : "ইনস্টলমেন্ট ডিটেইলস",
                icon: "error",
                text: apologiesSomethingWentWrongMessage,
                allowOutsideClick: false,
                customClass: {
                    container: "active-your-service-swal-bg",
                },
            });
        } finally {
            hideLoader();
        }
    }

    // } else if (currentPath === '/ib-account-related') {

    addClickEventWithAsyncHandler("btnIBARChequeBookLeaf", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnIBARAccountClosureProcess",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnIBARActivateSmsBanking",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnIBARAvailableBalance", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnIBARFundTransferServices",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnIBARMiniStatement", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnIBARChequeBookRequisition",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnIBARIslamicBankingProducts",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    // } else if (currentPath === '/ewallet') {

    /*addClickHandlerForEWalletMenu('btnEWApproveOrReject', 'EW-APPROVE-OR-REJECT', 'Approve wallet request received.');
        addClickHandlerForEWalletMenu('btnEWApproveOrRejectMenu', 'EW-APPROVE-OR-REJECT', 'Approve wallet request received.');*/

    addClickHandlerForEWalletMenu(
        "btnEWChangeOrResetEWalletPIN",
        "EW-CHANGE-OR-RESET-PIN",
        "PIN Change or Reset request received."
    );
    addClickHandlerForEWalletMenu(
        "btnEWChangeOrResetEWalletPINMenu",
        "EW-CHANGE-OR-RESET-PIN",
        "PIN Change or Reset request received."
    );

    addClickHandlerForEWalletMenu(
        "btnEWDeviceBind",
        "EW-DEVICE-BIND",
        "Device Bind request received."
    );
    addClickHandlerForEWalletMenu(
        "btnEWDeviceBindMenu",
        "EW-DEVICE-BIND",
        "Device Bind request received."
    );

    addClickHandlerForEWalletMenu(
        "btnEWLockOrBlock",
        "EW-LOCK-BLOCK",
        "Wallet Lock Or Block request received."
    );
    addClickHandlerForEWalletMenu(
        "btnEWLockOrBlockMenu",
        "EW-LOCK-BLOCK",
        "Wallet Lock Or Block request received."
    );

    addClickHandlerForEWalletMenu(
        "btnEWEWalletClose",
        "EW-CLOSE-WALLET",
        "Wallet closing request received."
    );
    addClickHandlerForEWalletMenu(
        "btnEWEWalletCloseMenu",
        "EW-CLOSE-WALLET",
        "Wallet closing request received."
    );

    addClickHandlerForEWalletMenu(
        "btnEWUnlockOrActive",
        "EW-UNLOCK-ACTIVE",
        "Wallet Lock or Active request received."
    );
    addClickHandlerForEWalletMenu(
        "btnEWUnlockOrActiveMenu",
        "EW-UNLOCK-ACTIVE",
        "Wallet Lock or Active request received."
    );

    addClickEventWithAsyncHandler("btnEWAboutSonaliEWallet", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );
    addClickEventWithAsyncHandler(
        "btnEWAboutSonaliEWalletMenu",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    const btnCreateIssueEWallet = document.getElementById(
        "btnCreateIssueEWallet"
    );
    if (btnCreateIssueEWallet) {
        btnCreateIssueEWallet.addEventListener(
            "click",
            showCascadingDropdownsForCreatingAnIssue
        );
    }

    const btnCreateIssueEWalletMenu = document.getElementById(
        "btnCreateIssueEWalletMenu"
    );
    if (btnCreateIssueEWalletMenu) {
        btnCreateIssueEWalletMenu.addEventListener(
            "click",
            showCascadingDropdownsForCreatingAnIssue
        );
    }

    function addClickHandlerForEWalletMenu(buttonId, action, message) {
        commonCloseNav();
        const getButtonElement = document.getElementById(buttonId);
        if (getButtonElement) {
            getButtonElement.addEventListener("click", () => {
                handleAPIRequestWithAccountVerification(
                    action,
                    message,
                    buttonId
                );
            });
        }
    }

    // } else if (currentPath === '/esheba') {

    addClickEventWithAsyncHandler("btnESAccountOpening", showMessageForHelp);
    addClickEventWithAsyncHandler("btnESAboutSonaliESheba", showMessageForHelp);
    addClickEventWithAsyncHandler("btnESOtherServices", showMessageForHelp);

    // } else if (currentPath === '/credit-card') {

    addClickEventWithAsyncHandler(
        "btnCCreditCardActivation",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnCCreditCardBlock", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCCreditChangeOrResetPIN",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCCreditECommerceActivationOrDeactivation",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCCreditGreenPINGeneration",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnCCreditMiniStatement", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCCreditOutstandingBDT",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCCreditOutstandingUSD",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnCCreditCardPayment", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    // } else if (currentPath === '/debit-card') {

    addClickEventWithAsyncHandler(
        "btnDCDebitCardActivation",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnDCDebitCardBlock", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnDCDebitChangeOrResetPIN",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCDebitECommerceActivationOrDeactivation",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCDebitGreenPINGeneration",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnCDebitMiniStatement", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    // } else if (currentPath === '/prepaid-card') {

    addClickEventWithAsyncHandler(
        "btnCPrepaidCardActivation",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnCPrepaidCardBlock", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCPrepaidChangeOrResetPIN",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCPrepaidECommerceActivationOrDeactivation",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCPrepaidMiniStatement",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCPrepaidGreenPINGeneration",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    // } else if (currentPath === '/agent-banking') {
    addClickEventWithAsyncHandler(
        "btnABAgentBankingServices",
        showMessageForHelp
    );
    // } else if (currentPath === '/casasnd') {

    addClickEventWithAsyncHandler("btnChequeBookLeaf", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnCASAActivateSMSBanking",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    /*addClickEventWithAsyncHandler('btnCASAMiniStatement',
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text));*/

    const btnCASAMiniStatement = document.getElementById(
        "btnCASAMiniStatement"
    );

    if (btnCASAMiniStatement) {
        btnCASAMiniStatement.addEventListener("click", (event) => {
            handleCASAMiniStatementClick();
        });
    }

    async function handleCASAMiniStatementClick() {
        showLoader();
        try {
            const response = await callDynamicAPI({
                purpose: "CASA-MINI-STATEMENT",
                page: "casasnd",
                button: "btnCASAMiniStatement",
            });

            // const statementData = response.data;
            // console.log('statementData.statement', statementData.statement)
            showCASAMiniStatementPopup(response.data.statement);
        } catch (error) {
            Swal.fire({
                title: locale === "en" ? "Mini Statement" : "মিনি স্টেটমেন্ট",
                icon: "error",
                text: apologiesSomethingWentWrongMessage,
                allowOutsideClick: false,
                customClass: {
                    container: "active-your-service-swal-bg",
                },
            });
        } finally {
            hideLoader();
        }
    }

    function showCASAMiniStatementPopup(statements) {
        let locale = getSavedLocale();
        try {
            const options = statements
                .map(
                    (statement) => `
    <div class="mini-statement-option" style="width: 100%">
        <label for="mini-statement-${statement.tran_serial}">
            <div class="mini-statement-details">
                <p style="text-align: left;">Date: ${statement.tran_date}</p>
                <p style="text-align: left;">Type: ${statement.tran_type}</p>
                <p style="text-align: left;">Amount: ${statement.tran_amount}</p>
                <p style="text-align: left;">Branch: ${statement.tran_origin_branch}</p>
                <p style="text-align: left;">Narration: ${statement.tran_narration}</p>
            </div>
        </label>
    </div>`
                )
                .join("");
            Swal.fire({
                title: `<h3 class="mini-statement-title"> ${
                    locale === "en" ? "Mini Statement" : "মিনি স্টেটমেন্ট"
                }</h3>`,
                html: `
        <div class="overflow-y-auto no-scrollbar w-full" style="max-height: 300px;">
        ${options}
        </div>`,
                confirmButtonText: locale === "en" ? "OK" : "ঠিক আছে",
                showConfirmButton: true,
                allowOutsideClick: false,
                customClass: {
                    container: "active-your-service-swal-bg",
                },
            });
        } catch (error) {
            Swal.fire({
                title: locale === "en" ? "Mini Statement" : "মিনি স্টেটমেন্ট",
                icon: "error",
                text: apologiesSomethingWentWrongMessage,
                allowOutsideClick: false,
                customClass: {
                    container: "active-your-service-swal-bg",
                },
            });
        }
    }

    addClickEventWithAsyncHandler("btnFundTransferServices", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnCASAAvailableBalance", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnChequeBookRequisition",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnAccountClosureProcess",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    // } else if (currentPath === '/ib-loans-advances') {

    addClickEventWithAsyncHandler(
        "btnIBLALoanClosureProcess",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler("btnIBLALoanDetails", (event, dataset) =>
        showMessageForHelp(dataset.voice, dataset.text)
    );

    addClickEventWithAsyncHandler(
        "btnIBLAOutstandingLoanBalance",
        (event, dataset) => showMessageForHelp(dataset.voice, dataset.text)
    );

    // }

    async function handleAccountSwitchButtonClick() {
        commonCloseNav();
        if (await checkLoginStatus()) {
            await handleAccountSwitchClick();
        } else {
            showVerificationAlert();
        }
    }

    const btnAccountSwitch = document.getElementById("btnAccountSwitch");
    if (btnAccountSwitch) {
        btnAccountSwitch.addEventListener(
            "click",
            handleAccountSwitchButtonClick
        );
    }

    async function handleAccountSwitchClick() {
        try {
            const response = await axios.get("/getac");
            const accounts = response.data;
            console.log("accounts", accounts.acLists);
            showAccountSelectionPopupCommon(accounts.acLists.acList);
        } catch (error) {
            console.error("Error fetching accounts:", error);
        }
    }

    function showAccountSelectionPopupCommon(accounts) {
        // stopAllAudio();

        const accountOptions = accounts
            .map(
                (account) => `
    <div class="account-option" style="width: 100%">
        <label for="account-${account.accEnc}">
         <input type="radio" name="selectedAccount" value="${account.accEnc}" id="account-${account.accEnc}">
            <div class="account-details">
                <p style="text-align: left;">Account Name: ${account.accountName}</p>
                <p style="text-align: left;">Account No: ${account.accountNo}</p>
            </div>
        </label>
    </div>`
            )
            .join("");

        Swal.fire({
            title: `<h3 class="account-list-title"> ${
                locale === "en" ? selectAnAccountEn : selectAnAccountBn
            }</h3>`,
            html: `
        <div class="overflow-y-auto no-scrollbar w-full" style="max-height: 250px;">
        ${accountOptions}
        </div>
        <div class="button-container">
            <button class="ac-submit-button" >${
                locale === "en" ? "Submit" : "জমা দিন"
            }</button>
            <button class="ac-cancel-button">${
                locale === "en" ? "Cancel" : "বাতিল"
            }</button>
        </div>`,
            showConfirmButton: false,
            allowOutsideClick: false,
            willClose: () => {
                stopAllAudioPlayback();
            },
        });

        const submitButton = document.querySelector(".ac-submit-button");
        const cancelButton = document.querySelector(".ac-cancel-button");

        submitButton.addEventListener(
            "click",
            handleAccountSwitchCommonSubmitButtonClick
        );
        cancelButton.addEventListener(
            "click",
            handleAccountSwitchCommonCancelButtonClick
        );
    }

    function handleAccountSwitchCommonSubmitButtonClick() {
        showLoader();
        const selectedAccountId = document.querySelector(
            'input[name="selectedAccount"]:checked'
        );

        if (selectedAccountId) {
            console.log("Selected Account Id:", selectedAccountId.value);

            axios
                .post("/save", {
                    ac: selectedAccountId.value,
                    purpose: "ACCOUNT-SWITCH",
                })
                .then((response) => handleSaveResponseCommon(response))
                .catch((error) =>
                    console.error("Error saving selected account:", error)
                )
                .finally(() => hideLoader());

            Swal.close();
        } else {
            console.log("No account selected");
        }
    }

    function handleAccountSwitchCommonCancelButtonClick() {
        Swal.close();
    }

    function handleSelectCommonButtonClick() {
        const selectedAccountId = this.getAttribute("data-account-id");
        console.log("selectedAccountId", selectedAccountId);

        axios
            .post("/save", { ac: selectedAccountId, purpose: "ACCOUNT-SWITCH" })
            .then((response) => handleSaveResponseCommon(response))
            .catch((error) =>
                console.error("Error saving selected account:", error)
            );
    }

    function handleSaveResponseCommon(response) {
        const { data: respData, status: statusCode } = response;

        if (statusCode === 200 && respData.status === "success") {
            storeData("pn", respData.pn);
            storeData("acn", respData.acn);
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
                    purpose: "EW-CHANGE-OR-RESET-PIN",
                    reason: "PIN Change or Reset request received.",
                    page: "ewallet",
                    button: "btnEWChangeOrResetEWalletPIN",
                });

                hideLoader();
                Swal.fire({
                    title: apiResponse.message,
                    icon:
                        apiResponse.status === "success" ? "success" : "error",
                    allowOutsideClick: false,
                    willClose: () => {
                        stopAllAudioPlayback();
                    },
                });
                playErrorAudio(apiResponse.prompt);
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in btnEWApproveOrReject click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                    allowOutsideClick: false,
                    willClose: () => {
                        stopAllAudioPlayback();
                    },
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleAPIRequestWithAccountVerification(
        apiPurpose,
        apiReason,
        btnName,
        pageName = ""
    ) {
        commonCloseNav();
        try {
            const isLoggedIn = await checkLoginStatus();

            if (!isLoggedIn) {
                showVerificationAlert();
                return;
            }

            const locale = getSavedLocale();
            const dobText = locale === "en" ? "YYYY-MM-DD" : "YYYY-MM-DD";

            const swalResult = await Swal.fire({
                title: `<h3 class="verify-account-title"> ${
                    locale === "en"
                        ? "Enter Account & Date of Birth."
                        : "অ্যাকাউন্ট এবং জন্ম তারিখ লিখুন ।"
                }</h3>`,
                html: `<input id="swal-input1" class="swal2-input" placeholder="${
                    locale === "en" ? "Account Number" : "অ্যাকাউন্ট নাম্বার"
                }">
                <input id="swal-input2" readonly class="swal2-input" placeholder="${dobText}" min="1945-01-01" max="2099-12-31">
                <div class="verify-button-container">
                    <button class="verify-submit-button">${
                        locale === "en" ? "Submit" : "জমা দিন"
                    }</button>
                    <button class="verify-cancel-button">${
                        locale === "en" ? "Cancel" : "বাতিল"
                    }</button>
                </div>`,
                showCancelButton: false,
                showConfirmButton: false,
                focusConfirm: false,
                allowOutsideClick: false,
                customClass: {
                    container: "user-info-verify-swal-bg swal2-overflow",
                },
                didOpen: function () {
                    const $swalInput2 = $("#swal-input2");

                    $swalInput2.datepicker({
                        dateFormat: "yy-mm-dd",
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "1945:2099",
                        theme: "smoothness",
                    });

                    const submitButton = document.querySelector(
                        ".verify-submit-button"
                    );
                    submitButton.addEventListener("click", async () => {
                        try {
                            const account =
                                Swal.getPopup().querySelector(
                                    "#swal-input1"
                                ).value;
                            const dob =
                                Swal.getPopup().querySelector(
                                    "#swal-input2"
                                ).value;

                            if (
                                !account ||
                                !/^\d{10,20}$/.test(account) ||
                                !dob ||
                                !/^\d{4}-\d{2}-\d{2}$/.test(dob)
                            ) {
                                Swal.showValidationMessage(
                                    locale === "en"
                                        ? "Invalid input. Please check your account and date of birth."
                                        : "ভুল ইনপুট দেয়া হয়েছে । আপনার অ্যাকাউন্ট এবং জন্ম তারিখটি লিখে পুনরায় চেষ্টা করুন ।"
                                );
                                return;
                            }

                            const accountAndDob = { account, dob };
                            // showLoader();

                            const verifyResp = await callDynamicAPI({
                                purpose: "USER-INFO-VERIFY",
                                page: pageName,
                                button: btnName,
                                account: account,
                                dob: dob,
                            });

                            // hideLoader();

                            if (verifyResp.status === "success") {
                                // showLoader();

                                try {
                                    const apiResponse = await callDynamicAPI({
                                        purpose: apiPurpose,
                                        page: "ewallet",
                                        button: btnName,
                                        reason: apiReason,
                                    });

                                    // hideLoader();
                                    handleEWVerificationApiResponse(
                                        apiResponse,
                                        locale
                                    );
                                } catch (error) {
                                    handleEWVerificationApiError(error, locale);
                                } finally {
                                    // hideLoader();
                                }
                            } else {
                                handleEWVerificationError(verifyResp, locale);
                            }
                        } catch (error) {
                            // console.error('Error during verification:', error);
                            handleEWVerificationError(error, locale);
                        } finally {
                            // hideLoader();
                        }
                    });

                    const cancelButton = document.querySelector(
                        ".verify-cancel-button"
                    );
                    cancelButton.addEventListener("click", () => {
                        // console.log('close called');
                        Swal.close();
                    });
                },
                preConfirm: () => {
                    const account =
                        Swal.getPopup().querySelector("#swal-input1").value;
                    const dob =
                        Swal.getPopup().querySelector("#swal-input2").value;

                    if (
                        !account ||
                        !/^\d{10,20}$/.test(account) ||
                        !dob ||
                        !/^\d{4}-\d{2}-\d{2}$/.test(dob)
                    ) {
                        Swal.showValidationMessage(
                            locale === "en"
                                ? "Invalid input. Please check your account and date of birth."
                                : "ভুল ইনপুট দেয়া হয়েছে । আপনার অ্যাকাউন্ট এবং জন্ম তারিখটি লিখে পুনরায় চেষ্টা করুন ।"
                        );
                    }

                    return { account, dob };
                },
                willClose: () => {
                    stopAllAudioPlayback();
                },
            });
        } catch (error) {
            // hideLoader();
            // console.error('Error in handleAPIRequestWithAccountVerification:', error);
            if (error.status === "error") {
                Swal.fire({
                    html: `<img class="" src="./img/icon/lock-card.svg" />
                <h2 class="swal2-title"> ${error.message} </h2>`,
                    allowOutsideClick: false,
                    customClass: {
                        container: "user-info-verify-swal-bg",
                    },
                    willClose: () => {
                        stopAllAudioPlayback();
                    },
                });
            }
        } finally {
            // hideLoader();
        }
    }

    function handleEWVerificationApiResponse(apiResponse, locale) {
        commonCloseNav();
        console.log("apiResponse", apiResponse);
        let iconAsImg = "lock-card.svg";
        if (apiResponse.status === "success") {
            iconAsImg = "checkmark.svg";
        }

        Swal.fire({
            html: `<img class="" src="./img/icon/${iconAsImg}" />
            <h2 class="swal2-title"> ${apiResponse.message} </h2>
        `,
            allowOutsideClick: false,
            confirmButtonText: locale === "en" ? "OK" : "ঠিক আছে",
            customClass: {
                container: "user-info-verify-swal-bg",
            },
            willClose: () => {
                stopAllAudioPlayback();
            },
        });

        playErrorAudio(apiResponse.prompt);
    }

    function handleEWVerificationApiError(error, locale) {
        // console.error('Error from callDynamicAPI:', error);

        Swal.fire({
            html: `<img class="" src="./img/icon/lock-card.svg" />
            <h2 class="swal2-title"> ${
                error.message || "An error occurred"
            } </h2>
        `,
            allowOutsideClick: false,
            customClass: {
                container: "user-info-verify-swal-bg",
            },
            willClose: () => {
                stopAllAudioPlayback();
            },
        });
    }

    function handleEWVerificationError(verifyResp, locale) {
        // console.log('verifyResp', verifyResp);

        Swal.fire({
            html: `<img class="" src="./img/icon/lock-card.svg" />
            <h2 class="swal2-title"> ${verifyResp.message} </h2>
        `,
            allowOutsideClick: false,
            confirmButtonText: locale === "en" ? "OK" : "ওকে",
            customClass: {
                container: "user-info-verify-swal-bg",
            },
            willClose: () => {
                stopAllAudioPlayback();
            },
        });
    }

    async function handleIBAccountRelatedClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/ib-account-related");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleIBAccountRelatedClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleSPGClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/spg");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleSPGClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleIBARLoansAndAdvancesClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/ib-loans-advances");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleIBAccountRelatedClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLoansAndAdvancesClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/loans-advances");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in btnLoansAdvances click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCreditCardPageClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/credit-card");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleCreditCardPage click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleDebitCardPageClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/debit-card");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleCreditCardPage click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handlePrePaidCardPageClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/prepaid-card");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handlePrePaidCardPage click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleFixedDepositClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/fixed-deposit");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in btnFixedDeposit click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCASASNDClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/casasnd");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in btnCASASND click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleDPSClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/account-dps");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in btnDPSClick click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleFDFixedDepositDetailsClick(
        voice = "",
        text = "",
        title = ""
    ) {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                let { title, text, voice } = getLocalWiseNIDContent();
                const reason = await enterReason(title, text, voice);

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        purpose: "FD-FIXED-DEPOSIT-DETAILS",
                        page: "fixed-deposit",
                        button: "btnFDMaturityDate",
                        reason: reason.value,
                    });

                    Swal.fire({
                        title: apiResponse.message,
                        icon:
                            apiResponse.status === "success"
                                ? "success"
                                : "error",
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in btnFDMaturityDate click:", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    document.querySelectorAll(".radioBtn a").forEach((button) => {
        button.addEventListener("click", function () {
            const locale = this.getAttribute("data-locale");

            // Update the active state based on the clicked locale
            setActiveState(locale);

            // Show loader before making the AJAX request
            showLoader();

            // Simulate axios post request with vanilla JavaScript fetch API
            axios
                .post("/change-locale", { locale: locale })
                .then((response) => {
                    console.log(response.data);
                    // Redirect to the received URL
                    goTo(response.data.redirect);
                })
                .catch((error) => {
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
    const btnLogout = document.getElementById("btnLogout");
    if (btnLogout) {
        document
            .getElementById("btnLogout")
            .addEventListener("click", function (event) {
                event.preventDefault();

                // Make an AJAX request to the logout endpoint
                axios
                    .post("/logout")
                    .then((response) => {
                        // Handle the successful logout response (if needed)
                        console.log("logout response", response.data);
                        goTo();
                    })
                    .catch((error) => {
                        // Handle any errors that occur during the logout request
                        console.error(error);
                    });
            });
    }

    async function showMessageForHelp(voice = "", text = "") {
        commonCloseNav();
        showLoader();
        await new Promise((resolve) => setTimeout(resolve, 500));
        hideLoader();

        let voiceToPlay = voice;
        let textToDisplay = text;

        if (typeof voiceToPlay !== "string" || voiceToPlay.trim() === "") {
            voiceToPlay = "/uploads/prompts/common/call-for-help-bn.m4a";
        }

        if (typeof textToDisplay !== "string" || textToDisplay.trim() === "") {
            textToDisplay = defaultCallCenterText;
        }

        playErrorAudio(voiceToPlay);
        const result = await Swal.fire({
            html: `<img class="" src="./img/icon/default-call-center.svg" /> <h2 class="swal2-title"> ${defaultContactOurCallCenter} </h2>
             <p>${textToDisplay}</p>`,
            showCancelButton: true,
            confirmButtonText: defaultConfirmButtonText,
            cancelButtonText: defaultCancelButtonText,
            reverseButtons: true,
            customClass: {
                container: "default-call-center-swal-bg",
            },
            willClose: () => {
                stopAllAudioPlayback();
            },
        });

        if (result.isConfirmed) {
            goTo("tel:" + helpCenterNumber);
        }
    }

    function addClickEventWithAsyncHandler(elementId, asyncHandler) {
        const element = document.getElementById(elementId);

        if (element) {
            element.addEventListener("click", (event) => {
                asyncHandler(event, element.dataset);
            });
        }
    }

    function addClickEventWithAsyncHandlerForApiCalling(
        elementId,
        asyncHandler
    ) {
        const element = document.getElementById(elementId);

        if (element) {
            element.addEventListener("click", (event) => {
                asyncHandler(event, element.dataset);
            });
        }
    }

    async function handleCardsButtonClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // redirect to cards page
                goTo("/cards");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in cardsButtonClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
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
                goTo("/account-and-loan");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in accountAndLoanClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    /* async function callGreenPINAPI() {
        try {
            const response = await axios.post("/generate-gpin");

            if (
                response.data &&
                response.data.status === "success" &&
                response.data.pin
            ) {
                const PIN = response.data.pin;
               const title =
                   locale === "en"
                       ? "Green PIN"
                       : "গ্রিন পিন";
                const text =
                    locale === "en"
                        ? `Your new PIN is: ${PIN}`
                        : `আপনার পিন হচ্ছে: ${PIN}`;

                Swal.fire({
                    title: title,
                    text: text,
                    icon: "success",
                    confirmButtonText: "OK",
                });
            } else {
                Swal.fire({
                    title: "Error",
                    text:
                        response.data.message ||
                        "Unable to generate PIN, please try again later.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            }
        } catch (error) {
            console.error("Error calling the API:", error);

            if (error.response) {
                Swal.fire({
                    title: "Error",
                    text: `Error: ${
                        error.response.data.message ||
                        "An unexpected error occurred."
                    }`,
                    icon: "error",
                    confirmButtonText: "OK",
                });
            } else {
                Swal.fire({
                    title: "Error",
                    text: "An error occurred while generating the PIN. Please check your internet connection and try again.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            }
        }
    } */

    async function callGreenPINAPI() {
        const messages = {
            en: {
                successTitle: "Green PIN",
                successText: (pin) => `Your new PIN is: ${pin}`,
                errorTitle: "Error",
                errorText: "Unable to generate PIN, please try again later.",
                apiErrorText: "An error occurred while generating the PIN.",
                confirmButton: "OK",
            },
            bn: {
                successTitle: "গ্রিন পিন",
                successText: (pin) => `আপনার পিন হচ্ছে: ${pin}`,
                errorTitle: "ত্রুটি",
                errorText:
                    "পিন তৈরি করা সম্ভব হয়নি, অনুগ্রহ করে পরে চেষ্টা করুন।",
                apiErrorText: "পিন তৈরি করার সময় একটি সমস্যা ঘটেছে।",
                confirmButton: "ঠিক আছে",
            },
        };

        const locale = getSavedLocale();

        try {
            showLoader();
           
            // const locale = document.documentElement.lang === "bn" ? "bn" : "en";
            const texts = messages[locale];

            const response = await axios.post("/generate-gpin");
            const { data } = response;
            
            hideLoader();
            
            if (data && data.status === "success" && data.pin) {
                Swal.fire({
                    title: texts.successTitle,
                    text: texts.successText(data.pin),
                    icon: "success",
                    confirmButtonText: texts.confirmButton,
                    customClass: {
                        container: "active-your-service-swal-bg",
                    },
                });
            } else {
                Swal.fire({
                    title: messages[locale]?.errorTitle || "Error",
                    text: data?.message || texts.errorText,
                    icon: "error",
                    confirmButtonText: texts.confirmButton,
                    customClass: {
                        container: "active-your-service-swal-bg",
                    },
                });
            }
        } catch (error) {
            console.error("Error calling the API:", error);
            hideLoader();
            // const locale = document.documentElement.lang === "bn" ? "bn" : "en";
            const errorMessage =
                error.response?.data?.message ||
                (locale === "en"
                    ? "An unexpected error occurred."
                    : "একটি অপ্রত্যাশিত ত্রুটি ঘটেছে।");

            Swal.fire({
                title: messages[locale]?.errorTitle || "Error",
                text: errorMessage,
                icon: "error",
                confirmButtonText: locale === "en" ? "OK" : "ঠিক আছে",
                customClass: {
                    container: "active-your-service-swal-bg",
                },
            });
        }
    }
        
    async function handleGreenPINClick() {
        try {
            // will be remove this later
            goTo("/green-pin");
            return false;

            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // redirect to cards page
                goTo("/green-pin");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleGreenPINClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
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
                goTo("/agent-banking");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in accountAndLoanClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleEShebaClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/esheba");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleEShebaClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
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
            window.location.href =
                "intent://details?id=" +
                androidAppPackageName +
                "#Intent;scheme=market;action=android.intent.action.VIEW;package=" +
                androidAppPackageName +
                ";end";
        } else if (isIOS()) {
            // iOS: Redirect to the Apple App Store
            window.location.href =
                "https://apps.apple.com/us/app/" + iOSAppPackageName;
        }
    }

    async function handleEWalletClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/ewallet");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleEWalletClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleIslamiBankingClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                goTo("/islami-banking");
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in handleIslamiBankingClick :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function showActiveYourServiceMessage(title, message) {
        commonCloseNav();
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: "error",
                    confirmButtonText: locale === "en" ? "Ok" : "ঠিক আছে",
                    cancelButtonText: locale === "en" ? "Cancel" : "বাতিল",
                    focusConfirm: false,
                    allowOutsideClick: false,
                    customClass: {
                        container: "active-your-service-swal-bg",
                    },
                    willClose: () => {
                        stopAllAudioPlayback();
                    },
                });
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error("Error in showActiveYourServiceMessage :", error);

            if (error.status === "error") {
                Swal.fire({
                    title: error.message,
                    icon: "error",
                    focusConfirm: false,
                    allowOutsideClick: false,
                    customClass: {
                        container: "active-your-service-swal-bg",
                    },
                    willClose: () => {
                        stopAllAudioPlayback();
                    },
                });

                // playErrorAudio(error.prompt);
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
        const title =
            locale === "en" ? defaultNIDScriptTitleEn : defaultNIDScriptTitleBn;
        const text =
            locale === "en" ? defaultNIDScriptTextEn : defaultNIDScriptTextBn;
        const voice = `common/enter-nid-${locale}`;

        return { title, text, voice };
    }

    function redirectUserToAppStore(appType) {
        const appStoreLink =
            appType === "esheba" && isIOS()
                ? eShebaiOS
                : appType === "esheba"
                ? eShebaAndroid
                : appType === "spg" && isIOS()
                ? SPGiOS
                : appType === "spg"
                ? SPGAndroid
                : null;

        if (appStoreLink) {
            window.location.href = appStoreLink;
        } else {
            // console.error('Unsupported platform or invalid app type');
            window.location.href =
                appType === "esheba" ? eShebaAndroid : SPGAndroid;
        }

        return false;
    }
});
