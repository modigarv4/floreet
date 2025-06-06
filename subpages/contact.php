<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/head.php';
    ?>
    <link rel="stylesheet" href="/css/mini_pages.css">
    <title>Floreet - Contact Us</title>
    <style>
        /* Scoped styles for the contact form page */
        .contact-section {
            height: 100vh;
            padding: 2rem;
            background-color: #faf6f0;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .contact-title {
            font-size: 2rem;
            font-weight: bold;
            color: #eb6a5a;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fdfdfd;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #7a9e7e;
            box-shadow: 0 0 0 2px rgba(122, 158, 126, 0.2);
        }

        .form-submit-btn {
            padding: 0.9rem 1.2rem;
            background-color: #eb6a5a;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-submit-btn:hover {
            background-color: #d6584e;
        }

        @media (max-width: 600px) {
            .contact-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <?php require_once ROOT . '/include/header.php' ?>
    <div class="main-container">
        <section class="contact-section">
            <div class="contact-title">Get in Touch</div>
            <form class="contact-form" action="submit_contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" name="name" id="name" required placeholder="Enter your full name">
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" name="email" id="email" required placeholder="Enter your email address">
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" required placeholder="What is this about?">
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea name="message" id="message" rows="5" required placeholder="Write your message here..."></textarea>
                </div>
                <button type="submit" class="form-submit-btn">Send Message</button>
            </form>
        </section>
    </div>
    <?php require_once ROOT . '/include/footer.php' ?>
</body>

</html>