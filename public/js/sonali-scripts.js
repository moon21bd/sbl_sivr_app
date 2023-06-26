/*
 * ScriptName: carnival-script.js
 * AuthorName: Raqibul Hasan Moon
 * Date: 5 July, 2020
 */

let base = 'webservices';
let checkCarnivalIdUrl = base + "/checkCarnivalId.php";
let getPaymentInfo = base + "/getPaymentInfo.php";
let emailOrTicketUrl = base + "/emailOrTicket.php";
let generateReferralUrl = base + "/generateRefer.php";
let isLinkExpiredUrl = 'api/isLinkExpired.php';
let getPackageProcessUrl = base + '/get-package-process.php';
let packageProcessLeadGenerateUrl = base + '/packageProcessLeadGenerate.php';
let sendotp = base + '/send.php';
let validateotp = base + '/validate.php';
let getCId = base + '/getCId.php';

// var userAuthApiBase = "http://45.125.222.225/carnival_ivr/", // local baseurl
var userAuthApiBase = "https://selfcare.carnival.com.bd/carnival_ivr/",
    carnivalUserAuthApi = userAuthApiBase + "user_auth.php?auth_key=832rh8f7eivr3844car9ni34val&";

// page names
const UserHomePage = 'home.html';
const UserHomeExistsPage = 'home-exists.html';
const UnknownUserPage = 'home.html';
const UserLoginPage = 'carnival-login.html';
const PayNowPage = 'pay-now.html';
const OfflineNoInternetPage = 'no-internet.html';
const noInternetMessagePage = 'no-internet-message.html';
const noInternetLanOrPonMessagePage = 'no-internet-lp-message.html';
const noInternetLosMessagePage = 'no-internet-los-message.html';
const changePackagePage = 'change-package.html';
const changePackageReviewPage = 'change-package-review.html';
const changePackageThanksPage = 'change-package-thanks.html';
const shiftConnectionPage = 'shift-connection.html';
const shiftConnectionReviewPage = 'shift-connection-review.html';
const updateInfoPage = 'update-info.html';
const thanksPage = 'thanks.html';
const shiftConnectionThanksPage = 'shift-connection-thanks.html';
const getACallBackPage = 'get-a-call-back.html';
const payNowRegularPage = 'pay-now-regular.html';
const slowInternetReasonPage = 'slow-internet-reason.html';
const updateInfoReviewPage = 'update-info-review.html';
const carnivalSendOtpPage = 'carnival-send-otp.html';
const serviceRequestPage = 'service-request.html';
const referralPage = 'referral.html';
const userAccountPage = 'user-account.html';

function getACBWrapper() {

    var isMultiOpen = sessionStorage.getItem("is_multi");
    if (!isEmpty(isMultiOpen) && isMultiOpen == 'enable') {
        // console.log('hello im your multiple get a callback');
        // callMultiple('gacb');
        loadNewPage('pageLoader', 'user-account.html');
    } else {
        getACallBack();
        return false;
    }
}

function getACallBack() {

    // console.log('Hi from getACallBack!!');
    var cAno = sessionStorage.getItem("c_ano"),
        cId = sessionStorage.getItem("userid"),
        cuserid = sessionStorage.getItem("cuserid"),
        userNumber = getUserNumber(),
        CarnivalID = "";

    if (!isEmpty(cuserid)) {
        CarnivalID = cuserid;
    } else if (!isEmpty(userNumber)) {
        CarnivalID = userNumber;
    } else if (!isEmpty(cAno)) {
        CarnivalID = cAno;
    } else if (!isEmpty(cId)) {
        CarnivalID = cId;
    }

    if (!LoggedIn()) { // if user is not logged in

        sessionStorage.setItem('page', "support-callback");
        loadNewPage('pageLoader', UserLoginPage);
    } else {
        if (!isEmpty(CarnivalID)) {
            var getACallBackResp = getAjaxReqRes(emailOrTicketUrl, {
                'purpose': 'get_a_call_back',
                'carnival_id': CarnivalID
            }, 'POST');
            // console.log('getACallBackResp ', getACallBackResp);
            loadNewPage('pageLoader', getACallBackPage);
        } else {
            loadNewPage('pageLoader', getACallBackPage);
        }
    }
}

function getCurrentPath() {
    var path = window.location.pathname,
        page = path.split("/").pop();
    // console.log(page);
    return page;
}

function getUserNumber() {

    var userNumber = sessionStorage.getItem('ano'),
        urlParams = parseURLParams(parent.document.URL);
    if (typeof urlParams != "undefined" && typeof urlParams.ano != "undefined") {
        // userNumber = urlParams.ano[0];
        userNumber = urlParams.ano[0];
    } else {
        userNumber = null;
    }

    return userNumber;

}

function isExpired() {
    var userId = sessionStorage.getItem('userid');
    var isExpired = JSON.parse(getAjaxReqRes(isLinkExpiredUrl, {'tid': sessionStorage.getItem('tid')}, "GET"));
    if (isExpired.expired !== null && (isExpired.expired == 'yes')) {
        // console.log('expired', isExpired.expired);
        // document.open();
        // document.write("Out with the old - in with the new! Your link is expired.");
        // document.close();
    }
}

function goPrevBack() {
    // console.log();
    window.history.back();
}

// var func = isExpired();
// var run = setInterval("func", 100);

const socialShareMsg = "ঘরে থাকার এই সময়, একটা ভালো ইন্টারনেট কানেকশন জীবন কে কতই না সহজ করে তোলে। একটা দারুন ইন্টারনেট সার্ভিস রেফার করলাম। Check this out ";

function LoggedIn() {
    let sessionUserId = sessionStorage.getItem('userid');
    let sessionUserIdLoggedIn = sessionStorage.getItem('logged_in');

    // console.log('LoggedIn ', sessionUserId);
    // console.log('sessionUserIdLoggedIn ', sessionUserIdLoggedIn);
    if (sessionUserIdLoggedIn && sessionUserId !== null) {
        return true;
    } else {
        return false;
    }
}

function noInternetEmailOrTicket(form, carnivalId) {

    var checkedValue = $("input[type=radio][name=active]:checked").val();
    $.ajax({
        type: "POST",
        url: emailOrTicketUrl,
        dataType: "JSON",
        data: {
            'carnival_id': carnivalId,
            'purpose': 'no_internet',
            'no_internet_option': checkedValue
        },
        success: function (response) { // success response

            if (response.status == 'success' && response.code == 200) {

                if ((response.noInternetOption == "lan" || checkedValue == "lan") || (response.noInternetOption == "pon" || checkedValue == "pon")) {
                    goTo(noInternetLanOrPonMessagePage);
                } else if ((response.noInternetOption == "los" || checkedValue == "los")) {
                    goTo(noInternetLosMessagePage);
                } else {
                    goTo(noInternetMessagePage);
                }
            } else {
                let msg = response.msg;
                $('.warning-msg').html(msg);
            }

        },
        error: function (jqXHR, textStatus, errorThrown) { // error response
            let msg = 'Something went wrong!';
            $('.warning-msg').html(msg);
        }
    });

}

function checkCarnivalId(carnivalId) {

    $.ajax({
        type: "POST",
        url: checkCarnivalIdUrl,
        dataType: "JSON",
        data: {
            'carnival_id': carnivalId,
            'from': 'login'
        },
        success: function (response) { // success response

            $('.warning-msg').html('');

            if (response.status == 'success' && (response.code == 200)) { // success

                if (response.account_status == 'multi') { // success and multiple account
                    var data = response.data;
                    $('#multi_account').show();
                    var myHtml = "",
                        myObj = "";

                    for (let m = 0; m < data.length; m++) {
                        var myUserId = data[m].userid,
                            myEmail = data[m].email,
                            myFullname = data[m].fullname,
                            myAddress = data[m].address,
                            mn = data[m].mobile;
                        myHtml += createMultipleAccount(myUserId, myEmail, myFullname, myAddress, btoa(JSON.stringify(data[m])), mn);
                    }

                    $('.review_box_dynamic').empty().html(myHtml);
                    return false;

                } else { // single account

                    let data2 = response.data;
                    // console.log('response.data ', data2);
                    sessionStorage.setItem("userid", data2.userid);
                    sessionStorage.setItem("cuserid", data2.userid);
                    sessionStorage.setItem("c_ano", data2.mobile);
                    sessionStorage.setItem("carnivalUserInfo", btoa(JSON.stringify(data2)));

                    sendp(data2.mobile);

                }


            } else { // failed
                let msg = response.msg;
                $('.warning-msg').html(msg);
            }

            // console.log('response ', response);
            return false;
        },
        error: function (jqXHR, textStatus, errorThrown) { // error response
            // let msg = 'Something went wrong!';
            // $('.warning-msg').html(msg);
        }
    });

}

function createMultipleAccount(userId, email, fullName, address, data, mn) {

    return `<div class="">
                    <ul>
                        <li>` + userId + `</li>
                        <li>` + email + `</li>
                        <li>` + fullName + `</li>
                        <li>` + address + `</li>
                    </ul>
                    <a class="btn" href="javascript:void(0)"
                       onclick="setUserLoginStatus('` + data + `', '` + mn + `', '` + userId + `')">Select</a>
                </div>`;
}

function setUserLoginStatus(data, mn, userId) {

    // console.log('from multipledata ', data, mn, userId);

    sessionStorage.setItem("userid", userId);
    sessionStorage.setItem("cuserid", userId);
    sessionStorage.setItem("c_ano", mn);
    sessionStorage.setItem("carnivalUserInfo", data);
    sendp(mn);
}

function sendp(mn) {
    var sendStatus = getAjaxReqRes(sendotp, {
        'pn': mn
    }, 'POST');
    // console.log('sendStatus ', sendStatus);
    loadNewPage('pageLoader', carnivalSendOtpPage);
}

function validateP(otp) {

    // console.log('validateP ', sessionStorage.getItem("c_ano"), otp);
    var validate = getAjaxReqRes(validateotp, {
            'pn': sessionStorage.getItem("c_ano"),
            'otp': otp,
        }, 'POST'),
        validateResp = JSON.parse(validate);
    // console.log('validate ', validate);

    if ((validateResp != null) && (validateResp.status == 'success') && (validateResp.code == 200)) { // success case
        $('.warning-msg').html('verified');

        var cData = sessionStorage.getItem("carnivalUserInfo");

        // LOGIN PURPOSE CODE START FROM HERE

        var decodedString = atob(cData),
            //myUser = JSON.parse(decodedString).userid,
            // mobile = JSON.parse(decodedString).mobile,
            //myCarnivalUserInfo = JSON.parse(decodedString);
            myCarnivalUserInfo = decodedString;

        /*var decodedString2 = $.parseJSON(decodedString);
        console.log('atob(cData) ', decodedString);
        console.log('decodedString2 ', decodedString2);
        return;*/

        /*console.log('atob(cData) ', atob(cData));
        console.log('decodedString ', myCarnivalUserInfo);
        console.log('mobile ', mobile);
        console.log('myUser ', myUser);
        return false;*/

        sessionStorage.setItem("logged_in", true);
        // sessionStorage.setItem("userid", myUser);
        // sessionStorage.setItem("cuserid", myUser);
        // sessionStorage.setItem("c_ano", mobile);
        sessionStorage.setItem("carnivalUserInfo", myCarnivalUserInfo);

        var continueVal = sessionStorage.getItem("continue"),
            hasPage = sessionStorage.getItem("page");

        if (!isEmpty(hasPage) && (hasPage == "support-callback")) {
            getACallBack();
            return;
        } else {
            if (!isEmpty(continueVal)) {

                if (!isEmpty(hasPage) && hasPage == 'pay_bills') {
                    var carnivalUserInfo = sessionStorage.getItem('carnivalUserInfo');
                    carnivalUserInfo = $.parseJSON(carnivalUserInfo);
                    var subscriptionStatus = carnivalUserInfo.subscription_status;

                    if (subscriptionStatus.toUpperCase() == "RENEWALFAILED") { // didnt pay bill

                        loadNewPage('pageLoader', PayNowPage);
                    } else {

                        loadNewPage('pageLoader', payNowRegularPage);
                    }
                } else {

                    loadNewPage('pageLoader', continueVal);
                }

            } else {
                loadNewPage('pageLoader', UserHomePage);
            }
        }

        // LOGIN PURPOSE CODE END FROM HERE

    } else { // failed case

        // resetForm(form);
        $('.warning-msg').html(validateResp.msg);
    }

    /*// LOGIN PURPOSE CODE START FROM HERE

    var decodedString = atob(data),
        myUser = JSON.parse(decodedString).userid,
        mobile = JSON.parse(decodedString).mobile,
        myCarnivalUserInfo = decodedString;

    sessionStorage.setItem("logged_in", true);
    sessionStorage.setItem("userid", myUser);
    sessionStorage.setItem("cuserid", myUser);
    sessionStorage.setItem("c_ano", mobile);
    sessionStorage.setItem("carnivalUserInfo", myCarnivalUserInfo);

    var continueVal = sessionStorage.getItem("continue"),
        hasPage = sessionStorage.getItem("page");

    if (!isEmpty(hasPage) && (hasPage == "support-callback")) {
        getACallBack();
        return;
    } else {
        if (!isEmpty(continueVal)) {

            if (!isEmpty(hasPage) && hasPage == 'pay_bills') {
                var carnivalUserInfo = sessionStorage.getItem('carnivalUserInfo');
                carnivalUserInfo = $.parseJSON(carnivalUserInfo);
                var subscriptionStatus = carnivalUserInfo.subscription_status;

                if (subscriptionStatus.toUpperCase() == "RENEWALFAILED") { // didnt pay bill

                    loadNewPage('pageLoader', PayNowPage);
                } else {

                    loadNewPage('pageLoader', payNowRegularPage);
                }
            } else {

                loadNewPage('pageLoader', continueVal);
            }

        } else {
            loadNewPage('pageLoader', UserHomePage);
        }
    }

    // LOGIN PURPOSE CODE END FROM HERE
    */

}

function callMultiple(page = "") {

    var adata = atob(sessionStorage.getItem("carnivalUserInfo_auto"));
    if (!isEmpty(adata)) { // data received
        adata = $.parseJSON(adata);
        $('#multi_account_in_home').show();
        var myHtml = "",
            myObj = "";

        for (let m = 0; m < adata.length; m++) {
            var myUserId = adata[m].userid,
                myEmail = adata[m].email,
                myFullname = adata[m].fullname,
                // mn = adata[m].mobile,
                myAddress = adata[m].address;
            // console.log('myUserId ', myUserId);

            myHtml += createMultiAccountHome(myUserId, myEmail, myFullname, myAddress, btoa(JSON.stringify(adata[m])), page);
        }

        $('.review_box_dynamic').empty().html(myHtml);
        return false;
    }

}

function createMultiAccountHome(userId, email, fullName, address, data, page) {

    return `<div class="">
                    <ul>
                        <li>` + userId + `</li>
                        <li>` + email + `</li>
                        <li>` + fullName + `</li>
                        <li>` + address + `</li>
                    </ul>
                    <a class="btn" href="javascript:void(0)"
                       onclick="setSingleUserLoginStatusHome('` + data + `', '` + userId + `','` + page + `')">Select</a>
                </div>`;
}

function setSingleUserLoginStatusHome(data, userId, page) {
    sessionStorage.removeItem('is_multi'); // remove multi account status
    sessionStorage.setItem("logged_in", true);
    sessionStorage.setItem("userid", userId);
    sessionStorage.setItem("cuserid", userId);
    sessionStorage.setItem("carnivalUserInfo", atob(data));
    $('#multi_account_in_home').css('visibility', 'hidden'); // hide modal
    if (page == 'paybills') {
        checkPayBills();
    } else if (page == 'gacb') {
        // getACallBack();
        loadNewPage('pageLoader', userAccountPage);
    } else {
        loadNewPage('pageLoader', page);
    }
}


function loadNewPage(iFrameId = 'pageLoader', pageName) {
    return top.document.getElementById('pageLoader').setAttribute("src", pageName);
}

function setRadioButton(name, value) {
    $('input[name="' + name + '"][value="' + value + '"]').prop('checked', true);
}

function goTo(url) {
    location.replace(url);
    return false;
}

function getAjaxReqRes(url, dataForGetPost, method = "GET") {

    return $.ajax({
        type: method,
        url: url,
        data: dataForGetPost,
        cache: false,
        async: false
    }).responseText;
}

function printThis(value) {
    return document.write(value);
}

function checkUserStatus(purpose, subscriptionStatus, connectionStatus, uId) {

    // console.log('checkUserStatus called.', purpose);

    if (purpose.toUpperCase() == 'PAY_BILLS') {

        if (subscriptionStatus.toUpperCase() == "RENEWALFAILED") { // didnt pay bill
            goTo(PayNowPage);
        } else {
            goTo(payNowRegularPage);
        }

    } else if (purpose.toUpperCase() == 'NO_INTERNET') {

        if (subscriptionStatus !== null && (subscriptionStatus == 'RenewalFailed')) {
            goTo(PayNowPage);
            return false;
        }
        if (subscriptionStatus.toUpperCase() == "REGISTERED" && connectionStatus.toUpperCase() == "OFFLINE") {
            goTo(OfflineNoInternetPage);
        } else if (subscriptionStatus.toUpperCase() == "REGISTERED" && connectionStatus.toUpperCase() == "ONLINE") {
            getAjaxReqRes(emailOrTicketUrl, {
                'purpose': 'no_internet',
                'no_internet_option': encodeURI('Device Online'),
                'carnival_id': uId
            }, 'POST');

            goTo(noInternetMessagePage);

        }

    } else if (purpose.toUpperCase() == 'SLOW_INTERNET') {

        if (subscriptionStatus !== null && (subscriptionStatus == 'RenewalFailed')) {
            goTo(PayNowPage);
            return false;
        }

        sessionStorage.setItem('purpose', purpose.toLowerCase());
        sessionStorage.setItem('slow_carnival_id', uId);
        goTo(slowInternetReasonPage);

    } else if (purpose.toUpperCase() == 'CHANGE_PACKAGE') {
        goTo(changePackagePage);
    } else if (purpose.toUpperCase() == 'SHIFT_CONNECTION') {
        goTo(shiftConnectionPage);
    } else if (purpose.toUpperCase() == 'UPDATE_INFO') {
        goTo(updateInfoPage);
    } else if (purpose.toUpperCase() == 'ROUTER_CONFIG') {

        var routerConfigResponse = JSON.parse(getAjaxReqRes(emailOrTicketUrl, {
            'purpose': 'router_config',
            'carnival_id': uId
        }, 'POST'));
        sessionStorage.setItem('purpose', purpose);
        goTo(thanksPage);

    }
}

function getQryStr(target) {
    if (target == 'parent') {
        var queryStringKeyValue = window.parent.location.search.replace('?', '').split('&');
    } else {
        var queryStringKeyValue = window.location.search.replace('?', '').split('&');
    }

    var qsJsonObject = {};
    if (queryStringKeyValue != '') {
        for (i = 0; i < queryStringKeyValue.length; i++) {
            qsJsonObject[queryStringKeyValue[i].split('=')[0]] = queryStringKeyValue[i].split('=')[1];
        }
    }
    return qsJsonObject;
}

function parseURLParams(url) {
    var queryStart = url.indexOf("?") + 1,
        queryEnd = url.indexOf("#") + 1 || url.length + 1,
        query = url.slice(queryStart, queryEnd - 1),
        pairs = query.replace(/\+/g, " ").split("&"),
        parms = {}, i, n, v, nv;

    if (query === url || query === "")
        return;

    for (i = 0; i < pairs.length; i++) {
        nv = pairs[i].split("=", 2);
        n = decodeURIComponent(nv[0]);
        v = decodeURIComponent(nv[1]);

        if (!parms.hasOwnProperty(n))
            parms[n] = [];
        parms[n].push(nv.length === 2 ? v : null);
    }
    return parms;
}

function isEmpty(value) {
    return (value == null || value.length === 0 || value === '');
}

function resetForm(selector) {
    return $(selector).trigger("reset");
}