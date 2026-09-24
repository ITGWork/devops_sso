# Loop API (ระบบ SSO เช็ค API แล้ว fallback เมื่อไม่ผ่านครบจำนวนครั้ง)

## สรุปปัญหา

ระบบ SSO เมื่อเช็ค API ภายนอก (DBD / DOPA) แล้ว "ไม่ผ่าน" ครบจำนวนครั้งที่กำหนด ควรจะเข้าโหมด fallback (ไม่ใช้ผลจาก API แต่ปล่อยให้ login ผ่านแทน) แต่ปัจจุบัน **ใช้งานไม่ได้** — ระบบไม่เข้าโหมด fallback ตามที่ควร

---

## จุดที่มี logic นี้ในโค้ด

### 1. `app/Http/Controllers/Auth/LoginController.php:337-403` — `CheckLegalEntity()`
เช็คนิติบุคคลกับ DBD

```php
$i = 1;
start:
if($i <= 3){
    try {
        $json_data = file_get_contents($url, false, $context);
        $api = json_decode($json_data);
        if(!empty($api->JuristicName_TH)){
            ...
        }elseif(property_exists($api, 'result') && trim($api->result)=='Bad Request'){
            // ไม่พบข้อมูล
        }else{
            $response['status'] = 'other';
        }
    } catch (\Exception $e) {
        $i++;
        if ($i <= 3) {
            MOILog::Add(...);
        }
        goto start;
    }
}else{
    $response['status'] = true;   // fail ครบ 3 ครั้ง -> ปล่อยผ่าน (fallback)
}
```

### 2. `app/Http/Controllers/Auth/LoginController.php:405-476` — `getPerson()`
เช็คบุคคลธรรมดากับ DOPA — โครงเดียวกัน

```php
$i = 1;
start:
if($i <= 3){
    try {
        $json_data = file_get_contents($url, false, $context);
        $api = json_decode($json_data);
        if(!empty($api->firstName)){
            $person = $api;
        }elseif(property_exists($api, 'Message') && trim($api->Message)=='CitizenID is not specify'){
            $person = null;
        }
        ...
    } catch (\Exception $e) {
        $i++;
        if ($i <= 3) {
            MOILog::Add(...);
        }
        goto start;
    }
}else{ //เชื่อมไม่ได้ครบ 3 ครั้ง
    $person = true;   // fallback -> ปล่อยผ่าน
}
```

### 3. `app/Helpers/Helper.php:1903-1952` — `getRdVat()`
เช็คคณะบุคคลกับกรมสรรพากร — ไม่ retry วน แค่ try/catch ครั้งเดียว แล้ว set `status = 'no-connect'` เมื่อ exception

### การนำผลไปใช้
`app/Helpers/Helper.php:353-431` — `checkAndSyncApiData()` เช็คว่าถ้า `entity['status']===true` หรือ `person===true` หรือ `rd->status==='no-connect'` จะ return ให้ login ผ่านทันที (ข้ามการเช็ค API)

---

## Logic ที่ตั้งใจให้ทำงาน

- นับจำนวนครั้งด้วยตัวแปร `$i` เริ่มที่ 1
- ทุกครั้งที่ `file_get_contents()` throw exception (เช่น timeout/connection refused) จะ `$i++` แล้ว `goto start` วนใหม่
- Threshold = **3 ครั้งติดกัน**
- เมื่อ `$i > 3` (fail ครบ 3 ครั้ง) → เข้า branch `else` ตั้งค่า flag พิเศษ (`status=true` / `$person=true` / `status='no-connect'`) แทนที่จะ error จริง
- `checkAndSyncApiData()` เห็น flag นี้แล้วตีความว่า "API เชื่อมต่อไม่ได้" → ปล่อยให้ user login ผ่านโดยข้ามการตรวจสอบข้อมูลกับ API ภายนอก
- ไม่มี state ค้างข้าม request (ไม่ใช่ circuit breaker แบบมี cooldown) — ทุกครั้งที่ login ใหม่จะเริ่มนับ `$i=1` ใหม่เสมอ

---

## สาเหตุที่ตอนนี้ "ใช้งานไม่ได้" (Root Cause)

โค้ดเขียนไว้สำหรับรันบน **PHP 7.1** (`composer.json` ระบุ `"php": "^7.1.3"`) แต่ตอนนี้รันจริงบน **PHP 8.4.0** (เช็คแล้วบนเครื่องนี้ด้วยคำสั่ง `php -v`) พฤติกรรมของ PHP 8 เปลี่ยนไปจาก PHP 7 ตรงจุดที่ทำให้ fallback พัง:

1. เมื่อ `file_get_contents($url, ...)` เชื่อม API ไม่ได้ (timeout/connection refused) — **ฟังก์ชันนี้ return `false` ไม่ throw Exception**
2. `json_decode(false)` ได้ค่า `null` เก็บไว้ใน `$api`
3. โค้ดเช็คต่อด้วย `property_exists($api, 'result')` (LoginController.php:379) หรือ `property_exists($api, 'Message')` (LoginController.php:445) โดย `$api` เป็น `null`
4. **ใน PHP 8, `property_exists(null, ...)` จะ throw `TypeError`** (ยืนยันด้วยการรันจริง):
   ```
   NOT CAUGHT BY \Exception - it is: TypeError -
   property_exists(): Argument #1 ($object_or_class) must be of type object|string, null given
   ```
5. `TypeError` เป็นลูกของ `\Error` ไม่ใช่ `\Exception` — โค้ดดักไว้แค่ `catch (\Exception $e)` (line 384 และ 458) จึง **ไม่จับ TypeError ได้**
6. ผลคือ request พังเป็น fatal error (HTTP 500) ทันที **ไม่วนเข้า retry loop และไม่มีทางไปถึง branch `else` ที่ทำ fallback ได้เลย**

สรุป: ระบบไม่เคยไปถึงเงื่อนไข "fail ครบ 3 ครั้ง → เข้าโหมดไม่ใช้ API" เพราะพังตั้งแต่ครั้งแรกที่ API เชื่อมต่อไม่ได้ (crash ก่อนจะนับครบ)

---

## วิธีแก้ที่เสนอ

เปลี่ยน `catch (\Exception $e)` เป็น `catch (\Throwable $e)` ใน 2 จุด:

- `app/Http/Controllers/Auth/LoginController.php:384` (ใน `CheckLegalEntity()`)
- `app/Http/Controllers/Auth/LoginController.php:458` (ใน `getPerson()`)

เพื่อให้ดักทั้ง `Exception` (เช่น network timeout จริงๆ) และ `Error`/`TypeError` (ที่เกิดจาก `$api` เป็น `null` แล้วเรียก `property_exists()`) ได้ครบ ระบบจะกลับไปวน retry ครบ 3 ครั้งแล้ว fallback ได้ตามเดิม

> หมายเหตุ: ยังไม่ได้แก้โค้ดจริง เอกสารนี้บันทึกไว้เพื่อรอ confirm ก่อนแก้
