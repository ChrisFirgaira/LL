// Store data for Pop Attack locations (GeoJSON format)
const storeData = {
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "geometry": {
                "type": "Point",
                "coordinates": [-0.3366, 53.7446] // Hull coordinates
            },
            "properties": {
                "id": "hull-st-stephens",
                "name": "St Stephen's Shopping Centre",
                "address": "110 Ferensway, Hull HU2 8LN, United Kingdom",
                "location": "St Stephen's Shopping Centre",
                "website": "https://popattack.co.uk",
                "opened": "September 2007",
                "owner": "British Land",
                "hours": {
                    "monday": "9:00 AM - 8:00 PM",
                    "tuesday": "9:00 AM - 8:00 PM", 
                    "wednesday": "9:00 AM - 8:00 PM",
                    "thursday": "9:00 AM - 8:00 PM",
                    "friday": "9:00 AM - 9:00 PM",
                    "saturday": "9:00 AM - 6:00 PM",
                    "sunday": "11:00 AM - 5:00 PM"
                },
                "products": [
                    "Pokemon Cards"
                    // "Magic: The Gathering",
                    // "Yu-Gi-Oh!",
                    // "Dragon Ball Super",
                    // "One Piece TCG",
                    // "Collectible Figures"
                ],
                "features": [
                    "Contactless Payments",
                    "Real-time Stock Updates"
                ],
                "description": "Our flagship vending machine located in Hull's premier shopping destination. Featuring cutting-edge vending technology with a full range of collectible trading cards and accessories.",
                "image": "images/hull-location.jpg"
            }
        }
        // Additional locations can be added here
    ]
};

// Export for use in main.js
window.storeData = storeData;