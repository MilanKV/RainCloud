# RainCloud

RainCloud is a cloud storage platform inspired by services like Dropbox and Google Cloud. The project is intentionally built using only core web technologies without relying on frameworks. It emphasizes simplicity and efficiency, utilizing HTML, CSS, JavaScript, PHP, MySQL, and AJAX to provide users with a secure and accessible file storage solution. It allows users to securely store and access their files from anywhere, using any device with an internet connection. This documentation will guide you through the installation, setup, and usage of RainCloud.

## Features

- **Authentication:** Secure user authentication for enhanced data protection.

- **Multiple File Upload:** Enable users to easily upload multiple files simultaneously.

- **Folder Management:** Create, navigate, and effortlessly create new folders within existing ones.

- **Drag and Drop File Upload:** Streamline the file upload process with intuitive drag and drop functionality.

- **File Details:** Access detailed information about uploaded files.

- **User Space:** Allocate dedicated space for each user, ensuring efficient file management.

## Installation

1. **Clone the Repository:**
   ```bash
   https://github.com/MilanKV/RainCloud.git
3. **Move to XAMPP's htdocs Directory:** 
- Copy or move the RainCloud folder into the htdocs directory of your XAMPP installation.
3. **Configure Database:** 
- Start the Apache and MySQL services using the XAMPP control panel.
- Create a MySQL database and update the config.php file in the RainCloud directory with your database credentials.
- Import the ``` raincloud_db.sql ``` file into your database to set up the required tables.
4. **Access RainCloud:**
- Open your web browser and access the RainCloud at
  ```bash
  http://localhost/RainCloud/view/auth/login.php

## Usage
1. **Login or Register** 
- Use the provided authentication system to log in or register for a new account.
2. **Upload Files:**
- Navigate to the upload section to upload multiple files simultaneously.
3. **Folder Management:**
- Create, navigate, and manage your folders to keep your files organized.
4. **Drag and Drop:**
- Simplify file uploads by dragging and dropping files directly into the interface.
5. **File Details:**
- View detailed information about each uploaded file.
6. **User Space:**
- Benefit from dedicated space for each user, ensuring efficient file management.

## Requirements
- PHP 7.4.27 CLI
- MySQL Database

## License
This project is licensed under the MIT License.
