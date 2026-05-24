<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Open SpaceGig</title>
    <script>
        function openApp() {
            const deepLink = @json($appDeepLink);
            const androidStore = @json($androidStoreUrl);
            const iosStore = @json($iosStoreUrl);

            const ua = navigator.userAgent || navigator.vendor || window.opera;
            const isAndroid = /android/i.test(ua);
            const isIOS = /iPad|iPhone|iPod/.test(ua);

            window.location = deepLink;

            setTimeout(() => {
                if (isAndroid) {
                    window.location = androidStore;
                } else if (isIOS) {
                    window.location = iosStore;
                }
            }, 2000);
        }

        window.onload = openApp;
    </script>
</head>
<body>
    <div style="font-family:sans-serif;padding:24px;text-align:center;">
        <h2>Opening SpaceGig...</h2>
        <p>If nothing happens, use the button below.</p>
        <a href="{{ $appDeepLink }}">Open App</a>
    </div>
</body>
</html>