<?php
// Outputs all settings meta tags read by components.js
// Requires $cfg callable to be defined before inclusion
?>
    <meta name="site-logo" content="<?= htmlspecialchars($cfg('site_logo', '')) ?>">
    <meta name="site-name" content="<?= htmlspecialchars($cfg('site_name', 'Nayagara Tours')) ?>">
    <meta name="site-tagline" content="<?= htmlspecialchars($cfg('site_tagline', 'Sri Lanka Travel')) ?>">
    <meta name="wa-number" content="<?= preg_replace('/\D/', '', $cfg('contact_whatsapp', '')) ?>">
    <meta name="site-phone" content="<?= htmlspecialchars($cfg('contact_phone', '')) ?>">
    <meta name="site-email" content="<?= htmlspecialchars($cfg('contact_email', '')) ?>">
    <meta name="site-address" content="<?= htmlspecialchars($cfg('contact_address', '')) ?>">
    <meta name="social-facebook" content="<?= htmlspecialchars($cfg('social_facebook', '')) ?>">
    <meta name="social-instagram" content="<?= htmlspecialchars($cfg('social_instagram', '')) ?>">
    <meta name="social-twitter" content="<?= htmlspecialchars($cfg('social_twitter', '')) ?>">
    <meta name="social-youtube" content="<?= htmlspecialchars($cfg('social_youtube', '')) ?>">
    <meta name="social-tripadvisor" content="<?= htmlspecialchars($cfg('social_tripadvisor', '')) ?>">
