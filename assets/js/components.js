/**
 * components.js
 * Injects navbar (with topbar) and footer directly — no fetch/server required.
 * Works with file://, Live Server, and any HTTP server.
 * Uses BASE prefix so the same HTML works from root & /pages/ depth.
 */

(function () {
    'use strict';

    /** Detect path prefix relative to site root */
    function getBase() {
        const path = window.location.pathname;
        return path.includes('/pages/') ? '../' : './';
    }

    const BASE = getBase();

    /** True when we are on the homepage (root or index.html) */
    const isHome = !window.location.pathname.includes('/pages/');

    /** On homepage → scroll anchor; on inner pages → full link */
    function homeLink(anchor, pageHref) {
        return isHome ? anchor : `${BASE}${pageHref}`;
    }

    // ── Navbar + Topbar HTML 
    function getNavbarHTML() {
        return `
<div class="topbar" id="topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">
            <a href="tel:+94112345678"><i class="fa fa-phone"></i> +94 11 234 5678</a>
            <a href="mailto:info@nayagaratours.lk"><i class="fa fa-envelope"></i> info@nayagaratours.lk</a>
        </div>
        <div class="topbar-right">
            <span><i class="fa fa-clock"></i> Mon – Sat: 9 AM – 6 PM</span>
            <div class="topbar-socials">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>
</div>

<nav class="navbar" id="navbar">
    <div class="container nav-container">

        <a href="${BASE}index.html" class="nav-logo">
            <div class="logo-icon">
                <i class="fa-solid fa-compass"></i>
            </div>
            <div class="logo-text">
                <span class="logo-name">Nayagara Tours</span>
                <span class="logo-tagline">Sri Lanka Travel</span>
            </div>
        </a>

        <ul class="nav-menu" id="navMenu">
            <li><a href="${BASE}index.html"                                          class="nav-link" data-page="home">Home</a></li>
            <li><a href="${homeLink('#about',    'index.html#about')}"               class="nav-link" data-page="about">About</a></li>
            <li><a href="${homeLink('#services', 'pages/services.html')}"            class="nav-link" data-page="services">Services</a></li>
            <li><a href="${homeLink('#packages', 'pages/packages.html')}"            class="nav-link" data-page="packages">Packages</a></li>
            <li><a href="${homeLink('#gallery', 'pages/gallery.html')}"               class="nav-link" data-page="gallery">Gallery</a></li>
            <li><a href="${homeLink('#destinations', 'pages/blog.html')}"            class="nav-link" data-page="blog">Blog</a></li>
            <li><a href="${homeLink('#contact',  'index.html#contact')}"             class="nav-link" data-page="contact">Contact</a></li>
        </ul>

        <div class="nav-actions">
            <a href="${BASE}pages/packages.html" class="btn btn-primary nav-btn">
                <i class="fa-solid fa-paper-plane"></i> Book Now
            </a>
            <button class="hamburger" id="hamburger" aria-label="Toggle Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

    </div>
</nav>`;
    }

    // Footer HTML 
    function getFooterHTML() {
        return `
<footer class="footer">
    <div class="container">
        <div class="footer-grid">

            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo-icon"><i class="fa-solid fa-compass"></i></div>
                    <span class="logo-name">Nayagara Tours</span>
                </div>
                <p>Sri Lanka's passionate travel specialists — crafting unforgettable island experiences from the ancient ruins of Sigiriya to the golden shores of Mirissa. Your Pearl of the Indian Ocean journey begins here.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="TripAdvisor"><i class="fab fa-tripadvisor"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="${BASE}index.html"><i class="fa fa-chevron-right"></i> Home</a></li>
                    <li><a href="${BASE}index.html#about"><i class="fa fa-chevron-right"></i> About Us</a></li>
                    <li><a href="${BASE}pages/packages.html"><i class="fa fa-chevron-right"></i> Packages</a></li>
                    <li><a href="${BASE}pages/gallery.html"><i class="fa fa-chevron-right"></i> Gallery</a></li>
                    <li><a href="${BASE}pages/blog.html"><i class="fa fa-chevron-right"></i> Blog</a></li>
                    <li><a href="${BASE}index.html#contact"><i class="fa fa-chevron-right"></i> Contact</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Our Services</h4>
                <ul>
                    <li><a href="${BASE}pages/services.html"><i class="fa fa-chevron-right"></i> Flight Booking</a></li>
                    <li><a href="${BASE}pages/services.html"><i class="fa fa-chevron-right"></i> Hotel Booking</a></li>
                    <li><a href="${BASE}pages/services.html"><i class="fa fa-chevron-right"></i> Tour Guides</a></li>
                    <li><a href="${BASE}pages/services.html"><i class="fa fa-chevron-right"></i> Visa Assistance</a></li>
                    <li><a href="${BASE}pages/services.html"><i class="fa fa-chevron-right"></i> Airport Transfer</a></li>
                    <li><a href="${BASE}pages/services.html"><i class="fa fa-chevron-right"></i> Group Tours</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contact Us</h4>
                <div class="footer-contact-item">
                    <i class="fa fa-map-marker-alt"></i>
                    <span>No. 15, Galle Road, Colombo 03,<br>Sri Lanka</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fa fa-phone"></i>
                    <span>+94 11 234 5678<br>+94 77 123 4567</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fa fa-envelope"></i>
                    <span>info@nayagaratours.lk<br>bookings@nayagaratours.lk</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fa fa-clock"></i>
                    <span>Mon–Sat: 9AM – 7PM<br>Sunday: 10AM – 4PM</span>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; <span id="footerYear"></span> Nayagara Tours. All rights reserved. | Designed &amp; Developed by <a href="https://www.asseminate.com/" target="_blank" rel="noopener">Asseminate</a></p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>`;
    }

    // Inject component HTML 
    function injectComponent(targetId, html, callback) {
        const el = document.getElementById(targetId);
        if (!el) return;
        el.innerHTML = html;
        if (typeof callback === 'function') callback();
    }

    // Mark the correct nav link active 
    function setActiveNav() {
        const page  = window.location.pathname;
        const links = document.querySelectorAll('.nav-link');

        links.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href') || '';

            if      (page.includes('blog')     && href.includes('blog'))     link.classList.add('active');
            else if (page.includes('packages') && href.includes('packages')) link.classList.add('active');
            else if (page.includes('services') && href.includes('services')) link.classList.add('active');
            else if (page.includes('gallery')  && href.includes('gallery'))  link.classList.add('active');
            else if ((page.endsWith('/') || page.endsWith('index.html') || page === '') && href.includes('index.html') && !href.includes('#')) {
                link.classList.add('active');
            }
        });
    }

    // On inner pages the navbar is always solid 
    function solidifyNavOnInnerPages() {
        const nav  = document.getElementById('navbar');
        const path = window.location.pathname;
        if (!nav) return;
        if (!path.endsWith('index.html') && !path.endsWith('/') && path !== '') {
            nav.classList.add('solid');
        }
    }

    // Boot 
    document.addEventListener('DOMContentLoaded', () => {

        injectComponent('navbar-placeholder', getNavbarHTML(), () => {
            setActiveNav();
            solidifyNavOnInnerPages();
            if (typeof initNavbar === 'function') initNavbar();
        });

        injectComponent('footer-placeholder', getFooterHTML(), () => {
            const yearEl = document.getElementById('footerYear');
            if (yearEl) yearEl.textContent = new Date().getFullYear();
        });

    });

})();
