<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
    ?>  
    <title>Floreet</title>
</head>

<body>
    <?php require_once ROOT . '/include/header.php'; ?>

    <div class="main-container">
        <div class="l-container">
            <div class="t-container">
                <section class="hero">
                    <div class="hero-overlay">
                        <div class="hero-content">
                            <h1>Send Love with Custom Bouquets</h1>
                            <p>Choose flowers from our freshest stock and craft your perfect bouquet.</p>
                            <a href="/custom.php" class="btn peach">Start Customizing</a>
                        </div>
                    </div>
                </section>
            </div>
            <div class="b-container">
                <section class="s1 premade">
                    <div class="s1-overlay">
                        <div class="s1-content">
                            <p>premade.</p>
                        </div>
                    </div>
                </section>
                <section class="s1 idk">
                    <div class="s1-overlay">
                        <div class="s1-content">
                            <p>testimonials</p>
                        </div>
                    </div>
                </section>
                <section class="s1 featured">
                    <div class="s1-overlay">
                        <div class="s1-content">
                            <p>flower care tips</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="r-container">
            <section class="s1 by-color ">
                <div class="s1-overlay">
                    <div class="s1-content">
                        <p>by ocasion</p>
                    </div>
                </div>
            </section>
            <section class="s1 other-products">
                <div class="s1-overlay">
                    <div class="s1-content">
                        <p>popular products.</p>
                    </div>
                </div>
            </section>
            <section class="s1 about-section">
                <div class="s1-overlay about-overlay">
                    <div class="s1-content about-content">
                        <h2>Why Us?</h2>
                        <p>At Floreet, we help you send love and joy through personalized floral arrangements. Whether it's a celebration or a gesture of care, our bouquet builder puts creativity in your hands.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php require_once ROOT . '/include/footer.php'; ?>


</body>

</html>