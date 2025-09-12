# Google Maps Setup Guide

## Quick Setup

To enable Google Maps functionality in your appointment booking system, follow these steps:

### 1. Get a Google Maps API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. **Enable Billing** (Required - Google provides $200 free credits per month)
4. Enable the following APIs:
   - **Maps JavaScript API** ✅
   - **Geocoding API** ✅
   - **Places API** ✅

### 2. Create API Key

1. Go to "Credentials" in the Google Cloud Console
2. Click "Create Credentials" → "API Key"
3. Copy your API key

### 3. Configure Your Application

1. Copy the `config_example.txt` file to `.env` if you haven't already:
   ```bash
   cp config_example.txt .env
   ```

2. Open your `.env` file and add the following line:
   ```
   GOOGLE_MAPS_API_KEY=your_actual_api_key_here
   ```

3. Generate an application key:
   ```bash
   php artisan key:generate
   ```

4. Save the file and restart your Laravel application

### 4. Secure Your API Key (Recommended)

1. In Google Cloud Console, click on your API key
2. Under "Application restrictions", select "HTTP referrers"
3. Add your domain(s):
   - `http://localhost:8000/*` (for development)
   - `https://yourdomain.com/*` (for production)
4. Under "API restrictions", select "Restrict key" and choose:
   - Maps JavaScript API
   - Geocoding API
   - Places API (if enabled)

### 5. Test the Setup

1. Visit your appointment booking page: `http://localhost:8000/book-appointment`
2. You should see an interactive map with address autocomplete
3. Type an address and select from suggestions, or click on the map to set location
4. The address, latitude, longitude, and address components will be saved automatically

## Troubleshooting

### "BillingNotEnabledMapError"
- **Enable billing** in Google Cloud Console (required for all Google Maps APIs)
- Google provides $200 free credits per month
- Set up billing alerts to monitor usage

### "ApiNotActivatedMapError"
- Enable the required APIs in Google Cloud Console:
  - Maps JavaScript API
  - Geocoding API
  - Places API

### "This page can't load Google Maps correctly"
- Check that your API key is correctly set in the `.env` file
- Verify that the required APIs are enabled in Google Cloud Console
- Ensure your domain is added to the API key restrictions (if any)

### Map shows but clicking doesn't work
- Check browser console for JavaScript errors
- Verify that Geocoding API is enabled

### AdvancedMarkerElement deprecation warning
- The code has been updated to use the newer AdvancedMarkerElement
- This warning can be safely ignored as the old Marker still works

## Fallback Mode

If you don't want to set up Google Maps right now, the system will automatically show a manual address input field where users can type their address directly.

## Support

For more help with Google Maps API:
- [Google Maps Platform Documentation](https://developers.google.com/maps/documentation)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Google Maps API Pricing](https://developers.google.com/maps/billing-and-pricing)
