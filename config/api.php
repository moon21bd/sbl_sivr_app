<?php

return [
    'basic-auth' => [
        'username' => 'callcen',
        'password' => 'dbBadSbl$erz'
    ],
    'base_url' => 'https://sblapi2022.sblesheba.com:8877/', // Sandbox base URL
    'send_otp_url' => 'api/callcenter/SendOTP',
    'verify_otp_url' => 'api/callcenter/VerifyOTP',
    'get_account_list_url' => 'api/callcenter/GetAccountList',
    'active_wallet_url' => 'api/callcenter/activeWallet',
    'get_wallet_details_url' => 'api/callcenter/getWalletDetails',
    'approve_wallet_request_url' => 'api/callcenter/approveWalletRequest',
    'get_pin_reset_url' => 'api/callcenter/pinReset',
    'device_bind_url' => 'api/callcenter/deviceBind',
    'close_wallet_url' => 'api/callcenter/closeWallet',
    'create_issue_url' => 'api/callcenter/createIssue',
    'lock_wallet_url' => 'api/callcenter/lockWallet',
    'crm_ticket_base_url' => 'https://10.249.3.201/', // Sandbox base URL

    /*'crm_ticket_login_info' => [
        'userid' => '#SBL_05',
        'password' => 'Abc12345678@'
    ],*/

    'crm_ticket_login_info' => [
        'userid' => '#SBL_SMART_IVR',
        'password' => 'Abcd1234@'
    ],
    'crm_ticket_login_url' => 'api/login',
    'crm_ticket_create_url' => 'api/ticket',
    'crm_ticket_call_type_url' => 'api/call-type',
    'crm_ticket_call_category_url' => 'api/call-category',
    'crm_ticket_call_sub_category_url' => 'api/call-sub-category',
    'crm_ticket_call_sub_sub_category_url' => 'api/call-sub-sub-category',

    'crm_ticket_authorization_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNmYwOTJkM2IyMDE3YzZlNzIwODRmODg0Y2FjYzc1N2RlOTg2ODY1YTMwZTI1ODg4MTIwMmRhMjkxODUxZWY2OWE2NGQ5MGNiYTUyZjFiMDYiLCJpYXQiOjE2OTEzOTk5MTAuMDcyNTc5LCJuYmYiOjE2OTEzOTk5MTAuMDcyNTgxLCJleHAiOjE3MjMwMjIzMTAuMDcwNzAyLCJzdWIiOiI1Iiwic2NvcGVzIjpbXX0.pvq9hO1lqzpW5U0BJcE_PPz5d1lBR_LM83S7VMvZ1ibn6BCTTAyaUieXV1d9H19PwL0GbqKgg132prmjw9LMp2Uq2GQJU0I-ACFZGTw7ujcNoMnSKXpfhKYeBUWQ6DeRuSTU293Kts-VddmQlW5GQz02w-aACjkYy__dCHeXXQ6mRnosbQ_bJEaCAcMgEPeclhotE3zG7zzjS4kByNezA_F-KSnISArI7cZoJlrwkO5mOMHXvZM8vv3S3E6UOIjr1KyP2stZW6ORGt_JKs1Tf9JqtldbUY8xSIdMgJ5-q3Ldofmq6Z71fnkIyQORpR22xmEwh-HrtuU3Yoy_f2VgsOsNI-zpS4UpNvacP-Nc1Dost_m3Cy35HqxHgVGor0ZBJSjCsLkh0dblKeLNuDgPE4D3qnXWeByfcMp2r0mBc6iTRQYgXwZjBj-zF5zr5I3Lj246rjP5TeU6nctaPeTAvpxgCNcCcC71bjoR6dSequSqdo_NE-SlEkkAvMIq2QPCkq-_YLPY7YO9_5FjNMf-7ObBQo85M2b1YSi0sbu_94DZj8MAXnyM6YQ0TcClHHIBcC49ldi1aqZp_c3VH1KftYRxh0BBHM5jH-OP1w_xxE336G8mbnj9powo6jZ-e_1gq9yqex2PMBgbRfgXIREiLEDzRoowkzOO7TQJTnpwtMo',
];
