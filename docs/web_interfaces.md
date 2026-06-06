# Web インターフェース一覧

## 認証

- **方式**: PHP セッション（`$_SESSION['user']`）
- **未認証時**: `/login/` へリダイレクト
- **CSRF 対策**: フォーム送信時に `csrf_token` を検証

---

## 公開ページ（認証不要）

| カテゴリ | 項目 | エンドポイント | メソッド | パラメータ | 備考 |
|---|---|---|---|---|---|
| トップ | ランディングページ | `/` | GET | — | 登録・ログインへのリンク |
| 認証 | ログインフォーム | `/login/` | GET | — | CSRF トークン発行 |
| 認証 | ログイン処理 | `/login/auth.php` | POST | `email`, `password`, `csrf_token` | 成功→`/dashboard/`、失敗→`/login/` |
| 認証 | ログアウト | `/logout/` | GET | — | セッション破棄→`/` |
| 認証 | ユーザー登録フォーム | `/register/` | GET | — | CSRF トークン発行 |
| 認証 | ユーザー登録処理 | `/register/store.php` | POST | `name`, `email`, `password`, `password_confirmation`, `csrf_token` | PW 8文字以上・一致・メール重複チェック |

---

## ダッシュボード（認証必須）

| カテゴリ | 項目 | エンドポイント | メソッド | パラメータ | 備考 |
|---|---|---|---|---|---|
| ダッシュボード | サマリー表示 | `/dashboard/` | GET | — | 最新の健康・運動・睡眠・食事データを一覧表示 |

---

## 健康記録（認証必須）

| カテゴリ | 項目 | エンドポイント | メソッド | パラメータ | 備考 |
|---|---|---|---|---|---|
| 健康記録 | 一覧 | `/health/` | GET | — | 体重・心拍数・血圧の記録一覧 |
| 健康記録 | 追加フォーム | `/health/add.php` | GET | — | — |
| 健康記録 | 追加処理 | `/health/insert.php` | POST | `recorded_at`, `weight`, `heart_rate`, `systolic`*, `diastolic`* | 同日レコードがある場合はエラー |
| 健康記録 | 編集フォーム | `/health/edit.php` | GET | `id`（クエリ） | — |
| 健康記録 | 更新処理 | `/health/update.php` | POST | `id`, `recorded_at`, `weight`, `heart_rate`, `systolic`*, `diastolic`* | 同日の別レコードがある場合はエラー |
| 健康記録 | 削除処理 | `/health/delete.php` | POST | `id` | — |
| 健康記録 | グラフ表示 | `/health/chart.php` | GET | — | 体重・心拍数・血圧の推移グラフ、画像ダウンロード可 |

*: 任意項目

---

## 運動記録（認証必須）

| カテゴリ | 項目 | エンドポイント | メソッド | パラメータ | 備考 |
|---|---|---|---|---|---|
| 運動記録 | 一覧 | `/activity/` | GET | — | 運動種類・時間・カロリー・距離を表示 |
| 運動記録 | 追加フォーム | `/activity/add.php` | GET | — | — |
| 運動記録 | 追加処理 | `/activity/insert.php` | POST | `exercise_date`, `exercise_type`, `duration_minutes`, `calories_burned`*, `distance_km`*, `memo`* | 運動時間は1分以上 |
| 運動記録 | 編集フォーム | `/activity/edit.php` | GET | `id`（クエリ） | — |
| 運動記録 | 更新処理 | `/activity/update.php` | POST | `id`, `exercise_date`, `exercise_type`, `duration_minutes`, `calories_burned`*, `distance_km`*, `memo`* | — |
| 運動記録 | 削除処理 | `/activity/delete.php` | POST | `id` | — |

*: 任意項目

---

## 食事記録（認証必須）

| カテゴリ | 項目 | エンドポイント | メソッド | パラメータ | 備考 |
|---|---|---|---|---|---|
| 食事記録 | 一覧 | `/meal/` | GET | — | 食事区分・メニュー・カロリー・PFC を表示 |
| 食事記録 | 追加フォーム | `/meal/add.php` | GET | — | — |
| 食事記録 | 追加処理 | `/meal/insert.php` | POST | `meal_date`, `meal_type`, `food_name`, `calories`*, `protein_g`*, `fat_g`*, `carbohydrate_g`*, `memo`* | `meal_type`: breakfast / lunch / dinner / snack |
| 食事記録 | 編集フォーム | `/meal/edit.php` | GET | `id`（クエリ） | — |
| 食事記録 | 更新処理 | `/meal/update.php` | POST | `id`, `meal_date`, `meal_type`, `food_name`, `calories`*, `protein_g`*, `fat_g`*, `carbohydrate_g`*, `memo`* | — |
| 食事記録 | 削除処理 | `/meal/delete.php` | POST | `id` | — |

*: 任意項目

---

## API（認証必須）

| カテゴリ | 項目 | エンドポイント | メソッド | パラメータ | レスポンス |
|---|---|---|---|---|---|
| 健康 API | グラフ用データ取得 | `/api/health/get/` | GET | — | JSON 配列（`recorded_at`, `weight`, `heart_rate`, `systolic`, `diastolic`）最新30件昇順 |
| 健康 API | AI 健康アドバイス | `/api/health/ai/` | GET | — | JSON `{ status, advice }` Gemini による分析テキスト |
| 健康 API | CSV ダウンロード | `/api/health/csv/` | GET | — | `health_records_latest.csv`（最新30件） |
| テスト | 疎通確認 | `/api/test/` | GET | — | JSON `{"text": "test"}`、認証不要 |

---

## 管理画面（認証不要・初期セットアップ用）

| カテゴリ | 項目 | エンドポイント | メソッド | パラメータ | 備考 |
|---|---|---|---|---|---|
| 管理 | リダイレクト | `/admin/` | GET | — | `/admin/create_database.php` へリダイレクト |
| 管理 | DB 初期化 | `/admin/create_database.php` | GET | — | schema.sql・insert_data.sql のプレビュー表示 |
| 管理 | DB 初期化実行 | `/admin/create_database.php` | POST | `csrf_token` | テーブル作成＋テストデータ挿入を実行 |
