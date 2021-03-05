export const EnvConfig = {
    mode: 'dev',
    root: 'http://itcutaway.cutawayapp.it/',
    serviceRoot: 'http://itcutaway.cutawayapp.it/',
    proxyServiceRoot: window.location.origin + '/',
    google: {
        webClientId: '666454472312-3h6cujjp38efl86kmfqu6t8nfrlcmbhn.apps.googleusercontent.com'
    },
    stripe: {
        sandbox: {
            published_key: "pk_test_ympBvvTQTTwwaNWtJzSlBtay"
        }
    },
    paypal: {
        production: {
            clientId: 'AbZsIcDzSCPMb7zrsRBEV9wLfxbvxMJZcP_Np1bguN8mBVuLiSoSQvLoqkNiWNnhq5NSSmVnwxc8Fx9q'
        },
        sandbox: {
            clientId: 'AaXxJbv0QK8gMWJpEv_4K4fpKrGuW2jv6lmtbwd__YK9ff5_QE3C5jKiUACrtbaTyfRxURdH9DPssiRD'
        }
    }
}