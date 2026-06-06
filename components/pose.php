<section class="container mx-auto p-4 w-1/2">
    <!-- メッセージ -->
    <div class="flex justify-center items-center">
        <div id="message-display" class="text-center w-1/2 my-6 p-3 bg-green-300 text-green-800 rounded">
            モデル読み込み中...
        </div>
    </div>

    <!-- 映像とキャンバス -->
    <div class="relative w-full max-w-3xl aspect-video mx-auto shadow-lg">
        <video id="webcam" class="absolute top-0 left-0 w-full h-full object-cover" autoplay muted></video>
        <canvas id="outputCanvas" class="absolute top-0 left-0 w-full h-full"></canvas>
    </div>

</section>

<!-- CDN TensorFlow -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-core"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-converter"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/pose-detection"></script>

<!-- CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Main -->
<script type="module" src="js/pose.js" defer></script>