# 妙廟通 Server Miao-Server

開發及建議環境 ：

-   Ubuntu 24.04.2
-   PHP 8.4.5
-   NGINX 1.27.4
-   mysql Ver 15.1 Distrib 10.11.8-MariaDB
-   redis-server 7.4.1
-   Laravel Framework 12.10.1

-   `妙廟通的Server，後台操作API。`

---

## 開發命名規範說明

### 1. 資料庫層

-   命名規範：底線式。
-   使用場景：資料庫欄位名稱、表名稱。
-   原因：資料庫的命名慣例通常使用底線式，因為大多數資料庫（如 MySQL、PostgreSQL）對大小寫不敏感且底線式可讀性更高。
-   範例：

```SQL
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 2. 程式碼層

#### 類別、方法、屬性

-   命名規範：駝峰式。
-   使用場景：PHP 類別、方法名稱、Eloquent 模型屬性。
-   原因：PHP 和 Laravel 的程式碼慣例使用駝峰式，與大多數現代程式語言保持一致，方便理解和使用。
-   範例：

```PHP
class User {
    public $firstName;
    public $lastName;

    public function getFullName(): string {
        return "{$this->firstName} {$this->lastName}";
    }
}
```

#### 變數

-   命名規範：駝峰式。
-   使用場景：函式內部變數、類別屬性名稱。
-   原因：駝峰式讓變數名稱更簡潔，符合現代編碼風格。
-   範例：

```PHP
$userId = 123;
$userName = 'John Doe';
```

### 3. API 層

#### URL 路徑

-   命名規範：底線式。
-   使用場景：API 路徑。
-   原因：底線式在 URL 中的可讀性較高，且容易與資料庫欄位對應，避免大小寫敏感問題。
-   範例：

```HTML
GET /api/user_profiles/{user_id}
```

#### JSON 屬性

-   命名規範：駝峰式。
-   使用場景：API 回應的 JSON 資料。
-   原因：駝峰式是 JSON 格式的常見慣例，與前端框架（如 React、Vue.js）兼容性更高。
-   範例：

```JSON
{
    "userId": 123,
    "firstName": "John",
    "lastName": "Doe"
}
```

### 4. 路由層

-   命名規範：底線式。
-   使用場景：Laravel 路由參數名稱。
-   原因：Laravel 對路由參數名稱沒有強制規範，但底線式能保持與資料庫欄位一致，減少混淆。
-   範例：

```PHP
Route::get('/users/{user_id}', [UserController::class, 'show']);
```

### 5. Eloquent 模型

-   命名規範：混合。
-   使用場景：模型屬性。
-   原因：Eloquent 模型會自動將駝峰式屬性與底線式資料庫欄位映射，因此內部可用駝峰式，而資料庫仍使用底線式。
-   範例：

```PHP
$user->firstName; // 駝峰式（模型屬性）
$user->first_name; // 底線式（資料庫欄位）
```

### 6. Enum 參數

-   命名規範：key 用駝峰式、value 用底線式。
-   使用場景：適用於 Laravel 的 Enum 定義及其資料庫儲存。
-   原因：駝峰式的成員名稱提高程式可讀性，底線式的成員值符合資料庫命名規範並增加一致性。
-   範例：

```PHP
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
```

---

## Laravel Helper 使用規範（適用於 Service）

以下列出目前專案中 Helper 函式是否可在 Service 中使用，依據原則為：

-   ✅ 可使用：純邏輯、無副作用、不依賴 context
-   ❌ 不可使用：依賴 request、route、server 全域變數或具副作用（log、curl、response）

---

### ✅ 可用於 Service 的 Helper 函式

#### ArrayHelpers.php

-   `filterFields`
-   `diffAssocArray`
-   `filterOriginalByKeys`
-   `isSameAsOriginal`
-   `isAssocArray`

#### HttpHelpers.php

-   `filterRequest` ✅ （唯一可用，僅處理傳入陣列）

#### JsonHelpers.php

-   `jsonValidate`
-   `jsonDiffAssoc`

#### ParamHelpers.php

-   `paramProcess`
-   `getOrder`
-   `getPage`
-   `decodeSearchParams`

---

### ❌ 不可用於 Service 的 Helper 函式

#### HttpHelpers.php

-   `getDomain` （使用 request()->root()）
-   `getSubDomain`（使用 $\_SERVER['HTTP_HOST']）
-   `getRealIp`（依賴 $\_SERVER headers）
-   `getRoutePrefix`（使用 Route::current()）

#### ResponseHelpers.php

-   `respondSuccess`（僅供 Controller 使用）
-   `respondCollection`
-   `respondError`

---

### 📌 原則總結

| 判斷條件                 | 可用於 Service？   |
| ------------------------ | ------------------ |
| 是否操作 request/route？ | ❌ 不可            |
| 是否有副作用（如 curl）  | ❌ 不可            |
| 是否處理純 array/json？  | ✅ 可以            |
| 是否有明確業務邏輯？     | ❌ 移至 Service    |
| 是否處理輸出資料格式？   | ❌ 留給 Controller |

---

## 回傳資料說明

    `**Http Code 除了 200 為成功外，其餘均為失敗。**`

    -   success 為執行狀態 true => false => 失敗。
    -   message 回傳訊息。
    -   code 為回傳代碼。
    -   data 回傳的資料。
    -   refer Enums相關資訊。
