<?php
class GeminiService
{
    private $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
            'content'       => ''
        ]
    ];

    private $base_url = "";

    public function __construct()
    {
        // Gemini APIキーが設定されていない場合は例外を投げる
        if (!defined('GEMINI_API_KEY')|| empty(GEMINI_API_KEY)) {
            return ['status' => 'error', 'message' => 'Gemini APIキーが設定されていません。'];
        }
        $this->base_url = GEMINI_API_URL . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY;
    }

    /**
     * 食品名から栄養成分を推定するメソッド
     * @param  string     $foodName  食品名
     * @return array|null            ['calories','protein_g','fat_g','carbohydrate_g'] または null
     */
    public function chatMeal(string $foodName): ?array
    {
        $prompt = implode("\n", [
            '次の食品の1食あたりの標準的な栄養成分を推定',
            '結果は以下のJSON形式のみ',
            '説明文やmarkdownのコードブロックは含めない',
            '',
            '食品名: ' . $foodName,
            '',
            '{"calories":整数値,"protein_g":小数点1桁数値,"fat_g":小数点1桁数値,"carbohydrate_g":小数点1桁数値}',
        ]);
        // リクエストボディ作成
        $requestData = [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ];
        // JSONエンコードしてHTTPリクエストのコンテキストを作成
        $this->options['http']['content'] = json_encode($requestData, JSON_UNESCAPED_UNICODE);

        // HTTPコンテキスト作成
        $context  = stream_context_create($this->options);
        // Gemini API呼び出し
        $response = @file_get_contents($this->base_url, false, $context);
        if ($response === false) {
            return null;
        }
        // JSONをデコードしてテキストを抽出
        $json = json_decode($response, true);
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($text === null) {
            return null;
        }
        // Gemini がコードブロックで囲んで返す場合に除去
        $text = preg_replace('/```(?:json)?\s*([\s\S]*?)```/u', '$1', $text);
        // JSONデコードして配列に変換
        $data = json_decode(trim($text), true);
        // データが配列でない場合は null を返す
        if (!is_array($data)) return null;
        // 各データを検査
        $calories = isset($data['calories']) ? (int) $data['calories'] : null;
        $protein  = isset($data['protein_g']) ? round((float) $data['protein_g'], 1) : null;
        $fat      = isset($data['fat_g']) ? round((float) $data['fat_g'], 1) : null;
        $carb     = isset($data['carbohydrate_g']) ? round((float) $data['carbohydrate_g'], 1) : null;

        // 各値を返す
        return [
            'calories'       => $calories,
            'protein_g'      => $protein,
            'fat_g'          => $fat,
            'carbohydrate_g' => $carb,
        ];
    }

    /**
     * 単一プロンプトでまとめて診断するメソッド
     * @param array  $records  health_records から取得した連想配列の配列
     * @return string|null     Gemini の生成テキスト
     */
    public function chatHealth(array $records)
    {
        // プロンプトを組み立て
        $lines = ["健康記録、体重(kg)、脈拍(bpm)、血圧(mmHg)の全体傾向と、特に注意すべきポイントを日本語で100文字程度で簡潔にまとめて"];
        foreach ($records as $r) {
            $lines[] = sprintf(
                "%s：%.1fkg、%dbpm、%d/%d",
                $r['recorded_at'],
                $r['weight'],
                $r['heart_rate'],
                $r['systolic'],
                $r['diastolic']
            );
        }
        $prompt = implode("\n", $lines);

        // リクエストボディ作成
        $requestData = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ];
        // JSONエンコードしてHTTPリクエストのコンテキストを作成
        $this->options['http']['content'] = json_encode($requestData, JSON_UNESCAPED_UNICODE);

        // HTTPコンテキスト作成
        $context = stream_context_create($this->options);
        // Gemini API呼び出し
        $response = @file_get_contents($this->base_url, false, $context);

        if ($response === false) {
            return ['status' => 'error', 'message' => 'API接続失敗: ' . ($http_response_header[0] ?? '不明なエラー')];
        }

        $json = json_decode($response, true);

        // API側が返すエラーメッセージを取得
        if (isset($json['error'])) {
            return ['status' => 'error', 'message' => 'Gemini APIエラー: ' . $json['error']['message']];
        }

        // 生成されたテキストを取得
        $advice = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;

        // テキストが取得できなかった場合のエラーハンドリング
        if (!$advice) {
            return ['status' => 'error', 'message' => '回答の取得に失敗しました。'];
        }

        // 結果を返す
        return ['status' => 'ok', 'advice' => $advice];
    }
}
