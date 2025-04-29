# App Pointment 📅

Modern ve kullanıcı dostu randevu yönetim sistemi.

App Pointment, hastaneler, klinikler ve sağlık merkezleri için tasarlanmış kapsamlı bir randevu yönetim sistemidir.
Sistem, doktorlar ve hastalar arasındaki randevu sürecini dijitalleştirerek, hasta takibini kolaylaştırır ve sağlık
hizmetlerinin verimli bir şekilde planlanmasını sağlar. Kullanıcılar randevuları kolayca oluşturabilir, düzenleyebilir
ve takip edebilir. Ayrıca tedavi planlaması, servis yönetimi ve hasta bilgilerinin güvenli bir şekilde saklanması gibi
özellikler sunar.

## 🚀 Teknolojiler

- Laravel 11
- Vue.js 3
- MySQL
- Tailwind CSS
- PrimeVue

## ⚙️ Kurulum

1. Projeyi klonlayın ve bağımlılıkları yükleyin:
   ```bash
   composer install
   npm install
   ```

2. Ortam değişkenlerini ayarlayın:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Veritabanını hazırlayın:
   ```bash
   php artisan migrate
   ```

4. Projeyi çalıştırın:
   ```bash
   php artisan serve
   npm run dev
   ```

## 💡 Özellikler

- Randevu oluşturma ve yönetimi
- Takvim görünümü
- Kullanıcı yönetimi
- Bildirim sistemi

## 📝 Lisans

MIT lisansı altında lisanslanmıştır.
