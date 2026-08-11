# نظام التقسيط والكاشير (Taqseet - Laravel Edition)

تطبيق تقسيط وكاشير متكامل مبني باستخدام **Laravel 11** للباك اند مع واجهات **Blade** أنيقة ومتجاوبة كلياً باللغة العربية لدعم اللغة من اليمين لليسار (RTL).

## 🚀 المميزات الرئيسية (Features)
- **لوحة تحكم وتلخيص (Dashboard)**: لعرض إحصائيات المنتجات، العملاء، والأقساط المتأخرة المستحقة.
- **نقطة البيع والكاشير (POS)**: إدارة السلة، إمكانية البيع كاش أو تحويل الفاتورة لعقد تقسيط.
- **إدارة المنتجات (Products)**: إضافة وعرض المنتجات (الدراجات النارية والسلع) وتتبع المخزون والأسعار.
- **إدارة العملاء والأقساط (Customers & Installments)**: تتبع العملاء، سجل خطط التقسيط، تحصيل الأقساط، وإرسال تذكيرات عبر WhatsApp.
- **واجهة برمجية REST API**: توفير جميع البيانات عبر API (`/api/v1/...`) للاستخدام من تطبيقات الجوال أو React/Next.js.

## 🛠️ متطلبات التشغيل (Requirements)
- PHP >= 8.2
- Composer
- SQLite أو MySQL/MariaDB

## 📦 خطوات التثبيت والتشغيل (Installation)

1. **استنساخ المستودع (Clone Repository)**:
   ```bash
   git clone https://github.com/Shahdhenagl/taqseet_laravel.git
   cd taqseet_laravel
   ```

2. **تثبيت الحزم (Install Dependencies)**:
   ```bash
   composer install
   ```

3. **إعداد البيئة (Environment Configuration)**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **إنشاء وتغذية قاعدة البيانات (Migrations & Seeders)**:
   ```bash
   php artisan migrate --seed
   ```

5. **تشغيل خادم التطوير (Run Development Server)**:
   ```bash
   php artisan serve
   ```
   افتح المتصفح على [http://localhost:8000](http://localhost:8000)

## 📡 مسارات API الرئيسية (API Endpoints)
- `GET /api/v1/dashboard`: الإحصائيات العامة
- `GET /api/v1/products`: قائمة المنتجات
- `POST /api/v1/products`: إضافة منتج
- `GET /api/v1/customers`: قائمة العملاء والأقساط
- `POST /api/v1/customers`: إضافة عميل
- `POST /api/v1/pos/checkout`: إنشاء فاتورة كاش أو تقسيط
- `POST /api/v1/installments/{id}/pay`: تحصيل قسط

## 📄 الترخيص (License)
مشروع مفتوح المصدر بموجب رخصة [MIT](LICENSE).
