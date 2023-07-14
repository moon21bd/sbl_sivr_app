<!DOCTYPE html>
<html>
<head>
    <title>Web Crypto API Example</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

<script>
    // Generate a random 256-bit encryption key
    async function generateEncryptionKey() {
        const key = await crypto.subtle.generateKey(
            { name: 'AES-CBC', length: 256 },
            true,
            ['encrypt', 'decrypt']
        );

        const exportedKey = await crypto.subtle.exportKey('jwk', key);
        return exportedKey;
    }

    // Import the encryption key as a CryptoKey object
    async function importEncryptionKey(encryptionKey) {
        const key = await crypto.subtle.importKey(
            'jwk',
            encryptionKey,
            { name: 'AES-CBC' },
            true,
            ['encrypt', 'decrypt']
        );
        return key;
    }

    // Encrypt the payload using the Web Crypto API
    async function encryptPayload(payload, encryptionKey) {
        const encodedPayload = new TextEncoder().encode(JSON.stringify(payload));
        const iv = crypto.getRandomValues(new Uint8Array(16));

        const cryptoKey = await importEncryptionKey(encryptionKey);

        const encryptedPayload = await crypto.subtle.encrypt(
            { name: 'AES-CBC', iv },
            cryptoKey,
            encodedPayload
        );

        const encryptedData = new Uint8Array(encryptedPayload);
        const encryptedBase64 = btoa(String.fromCharCode.apply(null, encryptedData));
        const ivHex = Array.from(iv)
            .map(byte => ('00' + byte.toString(16)).slice(-2))
            .join('');

        return { encryptedData: encryptedBase64, iv: ivHex };
    }

    // Decrypt the response from the server
    async function decryptResponse(response, encryptionKey) {
        try {
            const iv = new Uint8Array(
                response.iv.match(/.{1,2}/g).map(byte => parseInt(byte, 16))
            );
            const encryptedData = new Uint8Array(
                Array.from(atob(response.encryptedData), char => char.charCodeAt(0))
            );

            const cryptoKey = await importEncryptionKey(encryptionKey);

            const decryptedPayload = await crypto.subtle.decrypt(
                { name: 'AES-CBC', iv },
                cryptoKey,
                encryptedData
            );

            const decodedPayload = new TextDecoder().decode(decryptedPayload);
            return JSON.parse(decodedPayload);
        } catch (error) {
            console.error('An error occurred during decryption:', error);
            throw error;
        }
    }

    // Make the API request and handle the response
    async function makeApiRequest() {
        try {
            const encryptionKey = await generateEncryptionKey();
            const payload = { message: 'Hello, Laravel!' };
            const encryptedPayload = await encryptPayload(payload, encryptionKey);

            // Make the API request with the encrypted payload
            const response = await axios.post('/api/endpoint', encryptedPayload);

            // Decrypt and process the response
            const decryptedResponse = await decryptResponse(response.data, encryptionKey);
            console.log(decryptedResponse);
        } catch (error) {
            console.error('An error occurred:', error);
        }
    }

    // Usage
    makeApiRequest();
</script>

</body>
</html>


{{--
<!DOCTYPE html>
<html>
<head>
    <title>Localization Example</title>
</head>
<body>
<h1>{{ __('messages.welcome') }}</h1>
<p>{{ __('This is an example of localization in Laravel') }}</p>

<form action="/change-locale" method="get">
    @csrf
    <label for="locale-select">Select Language:</label>
    <select id="locale-select" name="locale">
        <option value="en">English</option>
        <option value="bn">Bengali</option>
    </select>
    <button type="submit">Change Language</button>
</form>



</body>
</html>
--}}
