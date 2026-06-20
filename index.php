<?php
require_once 'app.php';
?>
<!DOCTYPE html>
<html lang="ja">
<?php include 'components/head.php'; ?>

<body class="bg-white text-slate-800">

    <?php include 'components/nav.php'; ?>

    <main class="w-full">

        <!-- ========== ヒーローセクション ========== -->
        <section class="relative overflow-hidden bg-cover bg-center"
            style="background-image: url('images/back_image.png');">

            <div class="relative grid min-h-[calc(100vh-88px)] w-full items-stretch md:grid-cols-[1.0fr_1.1fr]">

                <!-- TODO: components/top/hero_left.php を表示 -->
                TODO: components/top/hero_left.php を表示

                <!-- components/top/hero_right.php を表示 -->
                <?php include "components/top/hero_right.php" ?>

            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
</body>

</html>
