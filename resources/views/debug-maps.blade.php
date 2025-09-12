<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-info { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e8; color: #2e7d32; }
        #map { height: 400px; width: 100%; border: 1px solid #ccc; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Google Maps API Debug</h1>

    <div class="debug-info">
        <h3>Configuration</h3>
        <p><strong>API Key:</strong> {{ config('services.google_maps.api_key') ? 'Present' : 'Missing' }}</p>
        <p><strong>API Key Value:</strong> {{ config('services.google_maps.api_key') ? substr(config('services.google_maps.api_key'), 0, 10) . '...' : 'Not set' }}</p>
    </div>

    <div id="debug-log" class="debug-info">
        <h3>Debug Log</h3>
        <div id="log-content"></div>
    </div>

    <div id="map"></div>

    <script>
        function log(message, type = 'info') {
            const logContent = document.getElementById('log-content');
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;
            logEntry.className = type;
            logContent.appendChild(logEntry);
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        function initGoogleMaps() {
            const apiKey = '{{ config("services.google_maps.api_key") }}';

            log('Starting Google Maps initialization...');
            log(`API Key: ${apiKey ? 'Present' : 'Missing'}`);

            if (!apiKey || apiKey === 'your_google_maps_api_key_here') {
                log('No valid API key found', 'error');
                document.getElementById('map').innerHTML = '<p style="text-align: center; padding: 50px;">No API key configured</p>';
                return;
            }

            // Check if Google Maps is already loaded
            if (window.google && window.google.maps) {
                log('Google Maps already loaded, initializing directly');
                initMap();
                return;
            }

            // Load Google Maps API
            log('Loading Google Maps API...');
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places,marker&callback=initMap`;
            script.async = true;
            script.defer = true;
            script.onerror = function() {
                log('Failed to load Google Maps API script', 'error');
                document.getElementById('map').innerHTML = '<p style="text-align: center; padding: 50px; color: red;">Failed to load Google Maps API</p>';
            };
            document.head.appendChild(script);
        }

        function initMap() {
            try {
                log('Initializing Google Maps...');

                // Check if Google Maps is available
                if (!window.google || !window.google.maps) {
                    throw new Error('Google Maps API not loaded');
                }

                // Initialize map
                const map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 40.7128, lng: -74.0060 },
                    zoom: 13
                });

                log('Google Maps initialized successfully', 'success');

                // Test geocoder
                const geocoder = new google.maps.Geocoder();
                log('Geocoder initialized', 'success');

                // Test places autocomplete
                const input = document.createElement('input');
                input.type = 'text';
                input.placeholder = 'Test address autocomplete...';
                input.style.width = '100%';
                input.style.padding = '10px';
                input.style.margin = '10px 0';
                input.style.border = '1px solid #ccc';

                const autocomplete = new google.maps.places.Autocomplete(input);
                document.getElementById('map').parentNode.insertBefore(input, document.getElementById('map'));

                log('Places Autocomplete initialized', 'success');

                // Test marker
                const marker = new google.maps.Marker({
                    position: { lat: 40.7128, lng: -74.0060 },
                    map: map,
                    title: 'Test Marker'
                });

                log('Marker created successfully', 'success');

            } catch (error) {
                log(`Error initializing Google Maps: ${error.message}`, 'error');
                document.getElementById('map').innerHTML = `<p style="text-align: center; padding: 50px; color: red;">Error: ${error.message}</p>`;
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            log('Page loaded, starting initialization...');
            initGoogleMaps();
        });
    </script>
</body>
</html>
