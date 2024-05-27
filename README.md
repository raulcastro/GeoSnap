# GeoSnap: Geolocate and visualize your photos on a map

GeoSnap is an open-source project that allows you to upload photos with metadata, geolocate them, and visualize their exact locations on a map with interactive polygons. Ideal for mapping properties, tracking outdoor activities, and more.

## Features
- Upload photos with metadata
- Geolocate photos and display them on a Google Map
- Create albums and draw polygons around photo clusters
- Open-source and free to use

## Getting Started

### Prerequisites
- PHP >= 7.3
- Composer
- Laravel
- MySQL
- Node.js & npm

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/raulcastro/GeoSnap.git
   cd GeoSnap
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Copy the `.env` file:**
   ```bash
   cp .env.example .env
   ```

4. **Generate the application key:**
   ```bash
   php artisan key:generate
   ```

5. **Configure your `.env` file:**
   Update your `.env` file with your database credentials and other necessary configuration.

6. **Run migrations:**
   ```bash
   php artisan migrate
   ```

7. **Serve the application:**
   ```bash
   php artisan serve
   ```

8. **Access the application:**
   Open your web browser and go to `http://localhost:8000`.

## Technical Stack
- **Backend:** Laravel
- **Database:** MySQL
- **Storage:** Firebase Storage or Amazon S3 (optional)
- **Maps:** Google Maps API
- **Admin Panel:** AdminLTE

## License
GeoSnap is licensed under the [GPL-3.0](LICENSE) license.

## Contributing
We welcome contributions from the community. Please read our [contributing guidelines](CONTRIBUTING.md) for more information.

## Contact
For any questions or suggestions, please contact [Raúl Castro](mailto:raul.castro.developer@gmail.com).
