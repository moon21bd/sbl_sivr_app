
'use strict';


/**
 * Encryption class for encrypt/decrypt data.
 */
export default class Encryption {


    /**
     * @see constructor()
     * @property {object} options The options.
     */
    #options = {};


    /**
     * Class constructor.
     * 
     * @link https://crypto.stackexchange.com/questions/41601/aes-gcm-recommended-iv-size-why-12-bytes IV size.
     * @param {object} options The options.
     * @param {boolean} options.debug Debugging message.
     * @param {string} options.algorithm Algorithm. Example: 'aes-256-gcm'.
     * @param {int} options.ivByteLength The Initialization vector (IV). ( byte to bit = 1*8; so 16*8 = 96 )
     */
    constructor({} = {}) {
        const defaults = {
            debug: false,
            algorithm: 'aes-256-gcm',
            ivByteLength: 12,
        };

        const options = {
            ...defaults,
            ...arguments[0],
        }

        this.#options = options;
    }// constructor


    /**
     * Base64 encoded string to ArrayBuffer.
     * 
     * @link https://stackoverflow.com/a/41106346/128761 Original source.
     * @param {string} base64 Base64 encoded string.
     * @returns {ArrayBuffer}
     */
    #base64ToBuffer(base64) {
        return Uint8Array.from(
            window.atob(base64), 
            (c) => {
                return c.charCodeAt(0);
            }
        )
    }// #base64ToBuffer


    /**
     * Concat Array Buffer.
     * 
     * @link https://pilabor.com/series/dotnet/js-gcm-encrypt-dotnet-decrypt/ Original source.
     * @param {ArrayBuffer} iv
     * @param {ArrayBuffer} encrypted
     * @returns {ArrayBuffer}
     */
    #concatArrayBuffer(iv, encrypted) {
        let tmp = new Uint8Array(iv.byteLength + encrypted.byteLength);
        tmp.set(new Uint8Array(iv), 0);
        tmp.set(new Uint8Array(encrypted), iv.byteLength);
        return tmp.buffer;
    }// #concatArrayBuffer


    /**
     * Disjoin (un-concatenate) ArrayBuffer.
     * 
     * @param {ArrayBuffer} arrayBuffer
     * @returns {Array}
     */
    #disJoinArrayBuffer(arrayBuffer) {
        const iv = arrayBuffer.slice(0, this.#options.ivByteLength);
        const encryptedMessage = arrayBuffer.slice(-(arrayBuffer.byteLength - this.#options.ivByteLength)).buffer;
        return [iv, encryptedMessage];
    }// disJoinArrayBuffer


    /**
     * Get algorithm parts.
     * 
     * @returns {mixed} Returns object with 'cipher', 'length', 'mode' if found valid algorithm string, returns the algorithm string as in parameter if invalid.
     */
    #getAlgoParts() {
        const algorithm = this.#options.algorithm;

        const regex = /(?<cipher>[a-z]+)\-(?<length>\d+)\-(?<mode>[a-z]+)/gmi;
        const matches = regex.exec(algorithm);

        if (
            typeof(matches.groups?.cipher) !== 'undefined' &&
            typeof(matches.groups?.length) !== 'undefined' &&
            typeof(matches.groups?.mode) !== 'undefined'
        ) {
            return matches.groups;
        }

        throw new Error('Invalid algorithm.');
    }// #getAlgoParts


    /**
     * Array Buffer to Base 64.
     * 
     * @link https://pilabor.com/series/dotnet/js-gcm-encrypt-dotnet-decrypt/ Original source.
     * @param {ArrayBuffer} arrayBuffer
     * @returns {string} Returns base64 encoded string.
     */
    bufferToBase64(arrayBuffer) {
        return window.btoa(
            String.fromCharCode(
                ...new Uint8Array(arrayBuffer)
            )
        );
    }// bufferToBase64


    /**
     * Decrypt the data.
     * 
     * @link https://gist.github.com/themikefuller/aca9491f960cbb8d94cdd7236698f0cd Original source.
     * @param {string} data The encrypted data to be decrypted.
     * @param {CryptoKey} key The secret key or passphrase.
     * @returns {string} Return the decrypted string on success
     */
    async decrypt(data, key) {
        const encryptedArrayBuffer = this.#base64ToBuffer(data);
        const [iv, ciphertext] = this.#disJoinArrayBuffer(encryptedArrayBuffer);
        if (this.#options.debug === true) {
            console.debug('  disjoined IV: ', iv, this.bufferToBase64(iv));
            console.debug('  disjoined ciphertext: ', ciphertext, new Uint8Array(ciphertext), this.bufferToBase64(ciphertext));
        }

        const algoParts = this.#getAlgoParts();
        let decrypted = await crypto.subtle.decrypt(
            {
                'name': algoParts.cipher.toUpperCase() + '-' + algoParts.mode.toUpperCase(),
                'iv': iv,
            }, 
            key, 
            ciphertext
        );

        const decoder = new TextDecoder();
        let decoded = decoder.decode(decrypted);
        return decoded;
    }// decrypt


    /**
     * Encrypt the data.
     * 
     * @link https://gist.github.com/themikefuller/aca9491f960cbb8d94cdd7236698f0cd Original source.
     * @async
     * @param {mixed} data The data to be encrypted.
     * @param {CryptoKey} key The secret key or passphrase.
     * @param {Uint8Array} iv Initialization Vector. Leave undefined to auto generated.
     * @return {string} Return base64 encoded of encrypted data
     */
    async encrypt(data, key, iv) {
        const encoder = new TextEncoder();
        let encodedData = encoder.encode(data);
        if (this.#options.debug === true) {
            console.debug('  data encoded: ', encodedData, this.bufferToBase64(encodedData));
        }
        
        const algoParts = this.#getAlgoParts();
        if (typeof(iv) === 'undefined') {
            iv = this.getIV();
        }
        if (this.#options.debug === true) {
            console.debug('  IV: ', iv, this.bufferToBase64(iv));
        }
        const ciphertext = await crypto.subtle.encrypt(
            {
                'name': algoParts.cipher.toUpperCase() + '-' + algoParts.mode.toUpperCase(),
                'iv': iv,
            },
            key,
            encodedData
        );

        if (this.#options.debug === true) {
            console.debug('  ciphertext: ', ciphertext, new Uint8Array(ciphertext), this.bufferToBase64(ciphertext));
        }
        return this.bufferToBase64(
            this.#concatArrayBuffer(iv.buffer, ciphertext)
        );
    }// encrypt


    /**
     * Generate key.
     * 
     * @link https://gist.github.com/themikefuller/aca9491f960cbb8d94cdd7236698f0cd Original source.
     * @see https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/generateKey
     * @async
     * @returns {Promise<CryptoKey>} Returns a `Promise` with a CryptoKey.
     */
    async generateKey() {
        const algoParts = this.#getAlgoParts();
        const name = algoParts.cipher.toUpperCase() + '-' + algoParts.mode.toUpperCase();
        const length = parseInt(algoParts.length);

        return await crypto.subtle.generateKey(
            {
                'name': name,
                'length': length
            }, 
            true, 
            ['encrypt', 'decrypt']
        );
    }// generateKey


    /**
     * Get CryptoKey from string.
     * 
     * @link https://gist.github.com/chrisveness/43bcda93af9f646d083fad678071b90a Original source.
     * @param {string} key The secret key or passphrase.
     * @returns {CrytoKey} Return `CryptoKey` object of the secret key.
     */
    async getCryptoKeyFromString(key) {
        const algoParts = this.#getAlgoParts();
        const encoder = new TextEncoder();
        let encodedkey = encoder.encode(key);
        const keyHashed = await crypto.subtle.digest('SHA-256', encodedkey);

        return await crypto.subtle.importKey(
            'raw', 
            keyHashed, 
            {
                'name':algoParts.cipher.toUpperCase() + '-' + algoParts.mode.toUpperCase()
            }, 
            false, 
            ['encrypt', 'decrypt']
        );
    }// getCryptoKeyFromString


    /**
     * Get Initialization Vector (IV)
     * 
     * @link https://gist.github.com/themikefuller/aca9491f960cbb8d94cdd7236698f0cd Original source.
     * @returns {Uint8Array}
     */
    getIV() {
        const ivByteLength = this.#options.ivByteLength;

        return crypto.getRandomValues(new Uint8Array(ivByteLength));
    }// getIV


}