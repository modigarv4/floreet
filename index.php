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
                    <div class="s1-overlay premade-overlay">
                        <div class="s1-content premade-content">
                            <span class="premade-ribbon">💐 Ready-to-Ship</span>
                            <h2 class="premade-heading">Browse Our Signature Bouquets</h2>
                            <a href="/subpages/premade.php" class="btn white small">View Collection</a>
                        </div>
                    </div>
                </section>

                <section class="s1 testimonial">
                    <div class="s1-overlay testimonial-overlay">
                        <div class="s1-content testimonial-content">
                            <span class="testimonial-stamp">📬</span>
                            <p class="testimonial-text">They made my day truly special...</p>
                            <div class="testimonial-btn-container">
                                <a href="/subpages/testimonials.php" class="btn soft small">Read More Stories</a>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="s1 f-care">
                    <div class="s1-overlay flower-care-bg">
                        <div class="s1-content flower-care-content">
                            <div class="flower-title">
                                <svg class="icon-book" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 4.5A2.5 2.5 0 013.5 7v11A1.5 1.5 0 005 19.5h6v-15H6zM13 4.5v15h6a1.5 1.5 0 001.5-1.5V7a2.5 2.5 0 00-2.5-2.5h-5z" clip-rule="evenodd" />
                                </svg>
                                <h2 class="flower-heading">Make Your Flowers Last Longer 🌿</h2>
                            </div>
                            <p class="flower-subtext" style="padding-bottom: 1rem;">Discover simple care tips for fresh, lasting blooms.</p>
                            <div class="flower-btn-container">
                                <a href="/subpages/flower-care.php" class="btn green small">Learn Flower Care</a>
                            </div>
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
                <div class="s1-overlay popular-overlay">
                    <div class="s1-content popular-content">
                        <h2 class="popular-heading">What’s Blooming This Week?🌟</h2>
                        <a href="/subpages/popular.php" class="btn green small">Shop Bestsellers</a>
                    </div>
                </div>
            </section>

            <section class="s1 about-section">
                <div class="s1-overlay about-overlay">
                    <div class="s1-content about-content">
                        <h2>Our Story, Rooted in Love 🌷</h2>
                        <p>
                            At Floreet, every bloom tells a story. We create thoughtfully designed floral pieces that help you express what words often can’t.
                        </p>
                        <p style="font-family: 'Dancing Script', cursive; font-size: 1.2rem;">— The Floreet Team</p>
                        <a href="/subpages/contact.php" class="why-link">Why Floreet →</a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php require_once ROOT . '/include/footer.php'; ?>


</body>

</html>