<!DOCTYPE html>
<html>
<head>
    <title>Web Crypto API Example</title>
    <script src="{{ asset('js/axios.min.js') }}"></script>
</head>
<body>

<!-- example.blade.php -->
<h1>Encrypted Data</h1>
<p>Key from PW: {{$keyFromPw}}</p>
<p>IV: {{$iv}}</p>
<p>Encrypted Value: {{$encryptedVal}}</p>
<p>Decrypted Value: {{$decryptedVal}}</p>


<h1>Encrypt/Decrypt use Encryption class.</h1>

<h3>Original:</h3>
{{--<p id="original-text">{{ $decryptedVal }}</p>--}}
<p id="original-text">Moon21</p>

<h3>Encrypted:</h3>
<p id="encrypted-text"></p>

<h3>Decrypted:</h3>
<p id="decrypted-text"></p>

<h3>KeyFromText:</h3>
<p id="keyFromText"></p>

<script type="module">
    import Encryption from './js/Encryption.js';

    const encryptionObj = new Encryption({
        debug: true,
    });

    const secretKey = '{{ $keyFromPw }}';

    window.addEventListener('DOMContentLoaded', async () => {

        /*if (location.protocol !== 'https:') {
            alert('Please open via HTTPS.');
        }*/

        const originalText = document.getElementById('original-text').innerText;
        const encryptedText = document.getElementById('encrypted-text');
        const decryptedText = document.getElementById('decrypted-text');
        const keyFromText = document.getElementById('keyFromText');

        // const key = await encryptionObj.generateKey();
        // console.log('generateKey: ', key);


        console.log('getKeyFromPassword');
        const keyFromPw = await encryptionObj.getCryptoKeyFromString(secretKey);
        console.log(keyFromPw);

       // keyFromText.innerHTML = JSON.parse(keyFromPw);

        const iv = encryptionObj.getIV();
        console.log('getIV: ', iv, encryptionObj.bufferToBase64(iv));

        console.log('-------------');

        console.log('encrypt():');
        const encryptedVal = await encryptionObj.encrypt(originalText, keyFromPw, iv);
        console.log('encrypted: ', encryptedVal);
        encryptedText.innerHTML = encryptedVal;

        console.log('-------------');

        console.log('decrypt():');
        const decryptedVal = await encryptionObj.decrypt(encryptedVal, keyFromPw);
        console.log('decrypted: ', decryptedVal);
        decryptedText.innerHTML = decryptedVal;

        console.log('-------------',{
            "data": encryptedVal,
            "key": secretKey,
        });

        // Make the Axios request
        axios.post('/api/decrypt', {
            "data": encryptedVal,
            "key": secretKey,
        })
            .then(response => {
                console.log('Response:', response.data);
                // Handle the response data as needed
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>
</body>
</html>
