# データベース定義

- データベース名: `health_tracker`
- 文字セット: `utf8mb4`
- 照合順序: `utf8mb4_general_ci`

---

## テーブル一覧

| テーブル名 | 説明 |
|---|---|
| [users](#users) | ユーザー情報 |
| [health_records](#health_records) | 体重・血圧・心拍数の記録 |
| [exercise_records](#exercise_records) | 運動記録 |
| [sleep_records](#sleep_records) | 睡眠記録 |
| [meal_records](#meal_records) | 食事記録 |

---

## users

ユーザーのアカウント情報を管理する。

| カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---|---|---|---|
| `id` | INT | NO | AUTO_INCREMENT | 主キー |
| `name` | VARCHAR(100) | NO | | ユーザー名 |
| `email` | VARCHAR(255) | NO | | メールアドレス（UNIQUE） |
| `password_hash` | VARCHAR(255) | NO | | パスワードハッシュ |
| `created_at` | DATETIME | YES | CURRENT_TIMESTAMP | 作成日時 |
| `updated_at` | DATETIME | YES | CURRENT_TIMESTAMP | 更新日時（自動更新） |

---

## health_records

日次の体重・心拍数・血圧を記録する。1日1レコード（`recorded_at` に UNIQUE 制約）。

| カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---|---|---|---|
| `id` | INT | NO | AUTO_INCREMENT | 主キー |
| `user_id` | INT | NO | | ユーザーID（FK: users.id） |
| `weight` | FLOAT | NO | | 体重（kg） |
| `heart_rate` | INT | NO | | 心拍数（bpm） |
| `systolic` | INT | YES | | 収縮期血圧（上の血圧） |
| `diastolic` | INT | YES | | 拡張期血圧（下の血圧） |
| `recorded_at` | DATE | NO | | 記録日（UNIQUE） |
| `created_at` | DATETIME | YES | CURRENT_TIMESTAMP | 作成日時 |
| `updated_at` | DATETIME | YES | CURRENT_TIMESTAMP | 更新日時（自動更新） |

---

## exercise_records

運動の種類・時間・消費カロリー・距離を記録する。

| カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---|---|---|---|
| `id` | INT | NO | AUTO_INCREMENT | 主キー |
| `user_id` | INT | NO | | ユーザーID（FK: users.id） |
| `exercise_date` | DATE | NO | | 運動日 |
| `exercise_type` | VARCHAR(100) | NO | | 運動の種類 |
| `duration_minutes` | INT | NO | | 運動時間（分） |
| `calories_burned` | INT | YES | | 消費カロリー（kcal） |
| `distance_km` | DECIMAL(6,2) | YES | | 距離（km） |
| `memo` | TEXT | YES | | メモ |
| `created_at` | DATETIME | YES | CURRENT_TIMESTAMP | 作成日時 |
| `updated_at` | DATETIME | YES | CURRENT_TIMESTAMP | 更新日時（自動更新） |

**インデックス:** `idx_exercise_records_user_date` (user_id, exercise_date)

**外部キー:** `user_id` → `users.id` ON DELETE CASCADE

---

## sleep_records

就寝・起床時刻と睡眠の質を記録する。

| カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---|---|---|---|
| `id` | INT | NO | AUTO_INCREMENT | 主キー |
| `user_id` | INT | NO | | ユーザーID（FK: users.id） |
| `sleep_date` | DATE | NO | | 睡眠日 |
| `bedtime` | DATETIME | NO | | 就寝時刻 |
| `wake_time` | DATETIME | NO | | 起床時刻 |
| `sleep_duration_minutes` | INT | NO | | 睡眠時間（分） |
| `sleep_quality` | TINYINT | YES | | 睡眠の質（1〜5、CHECK制約） |
| `memo` | TEXT | YES | | メモ |
| `created_at` | DATETIME | YES | CURRENT_TIMESTAMP | 作成日時 |
| `updated_at` | DATETIME | YES | CURRENT_TIMESTAMP | 更新日時（自動更新） |

**インデックス:** `idx_sleep_records_user_date` (user_id, sleep_date)

**外部キー:** `user_id` → `users.id` ON DELETE CASCADE

**CHECK制約:** `sleep_quality IS NULL OR sleep_quality BETWEEN 1 AND 5`

---

## meal_records

食事の内容・カロリー・栄養素を記録する。1日複数レコード登録可能。

| カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---|---|---|---|
| `id` | INT | NO | AUTO_INCREMENT | 主キー |
| `user_id` | INT | NO | | ユーザーID（FK: users.id） |
| `meal_date` | DATE | NO | | 食事日 |
| `meal_type` | ENUM | NO | | 食事区分（breakfast / lunch / dinner / snack） |
| `food_name` | VARCHAR(255) | NO | | 食品名 |
| `calories` | INT | YES | | カロリー（kcal） |
| `protein_g` | DECIMAL(5,1) | YES | | タンパク質（g） |
| `fat_g` | DECIMAL(5,1) | YES | | 脂質（g） |
| `carbohydrate_g` | DECIMAL(5,1) | YES | | 炭水化物（g） |
| `memo` | TEXT | YES | | メモ |
| `created_at` | DATETIME | YES | CURRENT_TIMESTAMP | 作成日時 |
| `updated_at` | DATETIME | YES | CURRENT_TIMESTAMP | 更新日時（自動更新） |

**インデックス:** `idx_meal_records_user_date` (user_id, meal_date)

**外部キー:** `user_id` → `users.id` ON DELETE CASCADE
