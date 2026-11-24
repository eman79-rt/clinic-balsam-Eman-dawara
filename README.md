# Balsam Clinics API — Laravel 13 (Eman)

مشروع API أولي لإدارة عيادات متعددة الفروع. يعتمد على Laravel 13، وSanctum للمصادقة، وRedis/Horizon للطوابير والكاش. هذا الأرشيف مبني على القالب الرسمي لـ Laravel 13. لا يحتوي `vendor/` أو `composer.lock` لأن بيئة البناء لا تتضمن PHP أو Composer؛ ثبّت الاعتماديات محليًا أول مرة.

## تشغيل

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan install:api
php artisan horizon:install
php artisan migrate --seed
php artisan serve
```

استخدم Redis وشغّل العامل بـ `php artisan horizon`. أنشئ في `.env`:

```dotenv
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## ما يتضمنه الأرشيف

- مخطط فروع، اختصاصات، أطباء، دوام طبيب متعدد الفروع، مرضى، مواعيد، سجل طبي، audit polymorphic ووسائط polymorphic.
- رقم مرجعي يولد في `AppointmentObserver` قبل الحفظ، مع عداد لكل فرع وسنة وقيد فريد على المرجع.
- مسح cache حسب فرع/طبيب/يوم من Observer عند الإنشاء والتعديل المؤثر والحذف.
- سجل تغييرات التشخيص والوصفة داخل `MedicalRecordObserver` للقيم المتغيرة فقط. تحديثات Eloquent الفردية تمر بالـ Observer؛ لا تستخدم bulk update لهذه الحقول إذا كان مطلوبًا تدقيق كل صف.
- `morphMap` مفروض لأسماء قصيرة مستقرة.
- خدمة حجز تتحقق من الحظر والدوام والتعارض ضمن transaction؛ FormRequest عربي، API Resource وترقيم صفحات.
- Event وListener وJob تأكيد منفصلة؛ الـ Job تستقبل ID فقط وتعمل على `critical`. مزود الرسائل التجريبي يكتب إلى Laravel log.
- بيانات Seeder تجريبية للمدير والاستقبال والطبيب، ورمز Sanctum للمدير باسم `admin-demo`.

## API المتاح حاليًا

بعد إنشاء token:

```http
GET /api/v1/appointments
Authorization: Bearer <token>
POST /api/v1/appointments
Content-Type: application/json
Authorization: Bearer <token>

{"branch_id":1,"doctor_id":1,"patient_id":1,"starts_at":"2030-01-07 10:00:00"}
```

الـ Seeder ينشئ الحسابات: `admin@balsam.test`, `reception@balsam.test`, `doctor@balsam.test` وكلمة المرور `password` للتطوير فقط. لا تستخدمها في الإنتاج. يرجع `admin-demo` عبر `personal_access_tokens` بعد التثبيت والتشغيل.

## قرارات معمارية

`AppointmentObserver` يعالج آثار دورة حياة الموديل التي يجب ألا تعتمد على أي Controller. `AppointmentBooked` و`AppointmentCancelled` أحداث أعمال؛ لكل رد فعل مستقبلي Listener مستقل، ويطلق الـ Listener Job منفصلة حسب القناة. لا تضع كل الآثار في Job واحدة. Jobs الخارجية يجب dispatch بعد commit وتستقبل IDs فقط. يُفضّل استخدام `after_commit=true` لاتصال Redis في `config/queue.php` أو `->afterCommit()` عند dispatch.

Global Scope في النسخة الحالية غير مرتبط بالمستخدم داخل الموديل؛ التقييد يتم عند الاستعلام في الـ Controller/Policy. أضف Scope ثابتًا عند الحاجة، مع تجاوز إداري صريح ومحدد. أضف Policies قبل توسيع نقاط تعديل السجلات الطبية.

## فجوات يجب إكمالها قبل اعتبار التسليم النهائي مكتملًا

هذا Starter وليس تنفيذًا كاملًا لكل بند في ورقة الواجب. يلزم إضافة أو استكمال: أوامر Artisan الأربعة وخدماتها، تعريف Scheduler واختبارات `schedule:list`, Jobs للتذكير والتقارير والغياب، Listeners مستقلة لبقية الآثار، Policies كاملة (الاستقبال والطبيب)، API للتعديل والإلغاء وكتابة التشخيص وavailability، قاعدة منع تداخل المواعيد بآلية آمنة لكل محرك DB، CSV شهري، محاكاة الفشل، Postman Collection شاملة، اختبارات Feature، وإعداد Horizon supervisors وقوائم الأولوية. لا أصف هذه الوظائف بأنها منجزة في هذه النسخة.

## اختبارات وفحص تنسيق

بعد توافر PHP/Composer:

```bash
composer install
vendor/bin/pint --test
php artisan test
php artisan route:list --path=api
php artisan schedule:list
```

شغّل Pint على كامل الملفات قبل التسليم. لا يمكن تشغيل Composer أو Pint أو الاختبارات ضمن بيئة الإنشاء الحالية لغياب PHP/Composer؛ لذا اعتبر هذا الأرشيف نقطة انطلاق بحاجة إلى تشغيل وفحص محلي.
