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
    'get_pin_reset_url' => 'api/callcenter/pinReset',
    'device_bind_url' => 'api/callcenter/deviceBind',
    'create_issue_url' => 'api/callcenter/createIssue',
    'lock_wallet_url' => 'api/callcenter/lockWallet',
];
