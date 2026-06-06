<?php
class GeminiService
{
    private $baseURL = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
            'content'       => ''
        ]
    ];

    /**
     * 単一プロンプトでまとめて診断するメソッド
     * @param array  $records  health_records から取得した連想配列の配列
     * @return string|null     Gemini の生成テキスト
     */
    public function chatHealth(array $records): ?string
    {
        $url = sprintf(
            '%s%s:generateContent?key=%s',
            $this->baseURL,
            GEMINI_MODEL,
            GEMINI_API_KEY
        );

        // プロンプトを組み立て
        $lines = ["健康記録、体重(kg)、脈拍(bpm)、血圧(mmHg)の全体傾向と、特に注意すべきポイントを３点以内で日本語で教えてください。"];
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

        $this->options['http']['content'] = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        $context = stream_context_create($this->options);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $json = json_decode($response, true);
        return $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
