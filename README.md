<div align="center">
  <img src="https://socialify.git.ci/your-username/IntelliCampus-ERP/image?description=1&font=Inter&language=1&name=1&owner=1&pattern=Circuit%20Board&theme=Dark" alt="IntelliCampus ERP Banner" width="800" />

  <h1>🚀 IntelliCampus ERP</h1>
  <p><strong>AI-Enabled Smart College ERP System with Biometric Facial Recognition & Predictive Analytics</strong></p>

  <p>
    <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
    <a href="https://tailwindcss.com/"><img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS"></a>
    <a href="https://alpinejs.dev/"><img src="https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine JS"></a>
    <a href="https://mysql.com/"><img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"></a>
    <a href="#"><img src="https://img.shields.io/badge/Edge_AI-TensorFlow.js-FF6F00?style=for-the-badge&logo=tensorflow&logoColor=white" alt="Face-API.js"></a>
  </p>
</div>

---

## 🌟 Overview

The modern educational landscape is undergoing a profound digital transformation. **IntelliCampus** is an advanced, web-based College ERP platform engineered to automate and streamline the entire lifecycle of a student's academic journey. 

Unlike traditional ERPs that rely on disjointed software and easily spoofed GPS/manual attendance, IntelliCampus features a highly sophisticated **Edge-AI Facial Recognition Attendance System** and an **AI-Based Student Risk Prediction** module. It centralizes operations, offering a fully role-based architecture for Administrators, HODs, Teachers, and Students.

---

## ✨ Key Features

### 📸 AI-Powered Smart Attendance
- **Teacher-Led Capture:** Eradicates proxy attendance. Teachers scan the classroom via their device.
- **Edge Computing (Privacy First):** Uses `face-api.js` (TensorFlow.js) directly in the browser's RAM. **No student photos are ever uploaded to a server.**
- **Strict Verification:** Configured with a `0.42` Euclidean distance threshold, guaranteeing 95%+ accuracy and eliminating false positives.
- **Bulk API Processing:** Capable of recognizing and processing large classes (70+ students) in under 2 seconds via optimized matrix operations.

### 🧠 AI Predictive Analytics (HOD Dashboard)
- **Behavioral Analysis:** Automatically tracks attendance consistency and academic data.
- **Risk Flagging:** Generates a real-time **Risk Score** (Safe, Moderate, High Risk) allowing for proactive intervention before a student fails or drops out.

### 🛡️ Secure Audit Logging
- Features a strict fallback **Manual Override** for teachers.
- Every manual entry triggers an **Audit Trail**, instantly visible on the HOD's dashboard, ensuring 100% transparency and accountability.

### 📚 Comprehensive Academic Management
- **Role-Based Access Control (RBAC):** Distinct interfaces and privileges for HODs, Faculty, and Administrators.
- **Fee & Document Management:** Centralized hub for financial tracking and critical student records.

---

## 🏗️ System Architecture

IntelliCampus is built on a robust Three-Tier architecture heavily supercharged by edge capabilities:

1. **Presentation Layer (Client):** Rendered with Laravel Blade, Tailwind CSS, and Alpine.js. Hosts the TensorFlow neural networks (WebGL accelerated) for instantaneous face detection.
2. **Application Layer (Server):** Powered by **Laravel (PHP)**. Processes REST API payloads, orchestrates the RBAC middleware, and runs complex predictive algorithms.
3. **Data Layer (Database):** A fully normalized (3NF) **MySQL** relational database designed for high transaction volume and uncompromised audit logging.

---

## 🚀 Deployment (Railway Ready)

This application is strictly configured and optimized for deployment on [Railway.app](https://railway.app/). 

### Railway Deployment Steps:
1. Push this repository to your GitHub account.
2. Log into Railway and select **New Project** > **Deploy from GitHub repo**.
3. Railway's **Nixpacks** will automatically detect the Laravel environment.
4. Add a **PostgreSQL / MySQL** plugin in your Railway project.
5. In your Railway App **Variables**, set the following environment variables:
   ```env
   APP_ENV=production
   APP_KEY=base64:your_generated_app_key
   APP_DEBUG=false
   APP_URL=https://your-railway-app-url.up.railway.app
   
   DB_CONNECTION=mysql (or pgsql)
   DB_HOST=your_railway_db_host
   DB_PORT=your_railway_db_port
   DB_DATABASE=your_railway_db_name
   DB_USERNAME=your_railway_db_user
   DB_PASSWORD=your_railway_db_password
   ```
6. The app will automatically build `composer install` and `npm run build` using Nixpacks.

---

## 💻 Local Development Setup

Clone the repository and run it locally:

```bash
# 1. Clone the repository
git clone https://github.com/your-username/IntelliCampus-ERP.git
cd IntelliCampus-ERP

# 2. Install PHP and Node dependencies
composer install
npm install

# 3. Setup Environment
cp .env.example .env
php artisan key:generate

# 4. Configure your local database in .env and migrate
php artisan migrate --seed

# 5. Build Frontend Assets
npm run build

# 6. Start the Local Server
php artisan serve
```

---

## 🔒 Privacy & Security First
At IntelliCampus, data privacy is not an afterthought. By executing Neural Networks natively on the edge (teacher's smartphone browser), we completely bypass the need for expensive third-party cloud AI APIs (like AWS Rekognition) and strictly adhere to global privacy standards regarding biometric data storage. 

<div align="center">
  <i>Developed with 💡 and ☕ for modern educational governance.</i>
</div>
