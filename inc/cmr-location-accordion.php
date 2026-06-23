<?php
// CMR Location Accordion Shortcode
// Shortcode: [cmr_location_accordion]
add_shortcode('cmr_location_accordion', 'cmr_location_accordion_shortcode');

function cmr_location_accordion_shortcode($atts) {
    ob_start();
    ?>
    <style>
        .cmr-loc-accordion-wrapper {
            font-family: 'Instrument Sans', sans-serif !important;
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 20px;
            color: #111;
        }

        .cmr-loc-item {
            border-bottom: 1px solid #eaeaea;
        }

        .cmr-loc-item:first-child {
            border-top: 1px solid #eaeaea;
        }

        .cmr-loc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 0;
            cursor: pointer;
            user-select: none;
            transition: background-color 0.3s ease;
        }

        .cmr-loc-title {
            font-size: 32px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: baseline;
            gap: 10px;
            letter-spacing: -1px;
            color: #111;
        }

        .cmr-loc-subtitle {
            font-size: 16px;
            font-weight: 400;
            color: #555;
            letter-spacing: 0;
        }

        .cmr-loc-icon {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #111;
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
        }

        .cmr-loc-icon::before,
        .cmr-loc-icon::after {
            content: '';
            position: absolute;
            background: #111;
            transition: transform 0.3s ease;
        }

        .cmr-loc-icon::before {
            width: 12px;
            height: 1px;
        }

        .cmr-loc-icon::after {
            width: 1px;
            height: 12px;
        }

        .cmr-loc-item.active .cmr-loc-icon::after {
            transform: scaleY(0);
        }

        .cmr-loc-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out, padding 0.5s ease-in-out;
            padding-bottom: 0;
        }

        .cmr-loc-item.active .cmr-loc-content {
            max-height: 1000px;
            padding-bottom: 40px;
        }

        .cmr-loc-content-inner {
            display: flex;
            gap: 60px;
            opacity: 0;
            transition: opacity 0.4s ease;
            transition-delay: 0.1s;
        }

        .cmr-loc-item.active .cmr-loc-content-inner {
            opacity: 1;
        }

        .cmr-loc-details {
            flex: 0 0 350px;
        }

        .cmr-loc-details h4 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            margin-top: 0;
            letter-spacing: -0.5px;
            color: #111;
        }

        .cmr-loc-address-block {
            margin-bottom: 30px;
        }

        .cmr-loc-address-block .icon {
            color: #8B5CF6;
            margin-bottom: 15px;
        }

        .cmr-loc-company {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 8px;
            color: #111;
        }

        .cmr-loc-address {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .cmr-loc-contact {
            margin-top: 25px;
        }

        .cmr-loc-contact a {
            display: block;
            color: #111;
            text-decoration: none;
            margin-bottom: 12px;
            font-size: 18px;
            transition: color 0.3s;
        }

        .cmr-loc-contact a:hover {
            color: #8B5CF6;
        }

        .cmr-loc-contact a.email {
            font-weight: 600;
        }

        .cmr-loc-map {
            flex: 1;
            background: #f5f5f5;
            overflow: hidden;
            min-height: 350px;
            position: relative;
        }

        .cmr-loc-map iframe {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        @media (max-width: 992px) {
            .cmr-loc-content-inner {
                flex-direction: column;
                gap: 30px;
            }
            .cmr-loc-details {
                flex: auto;
            }
            .cmr-loc-map {
                min-height: 250px;
            }
            .cmr-loc-item.active .cmr-loc-content {
                max-height: 1500px;
            }
        }
        @media (max-width: 768px) {
            .cmr-loc-title {
                font-size: 24px;
            }
        }
    </style>

    <div class="cmr-loc-accordion-wrapper">
        <!-- Item 1: Gurugram -->
        <div class="cmr-loc-item active">
            <div class="cmr-loc-header">
                <h3 class="cmr-loc-title">Gurugram <span class="cmr-loc-subtitle">(Headquarter)</span></h3>
                <div class="cmr-loc-icon"></div>
            </div>
            <div class="cmr-loc-content">
                <div class="cmr-loc-content-inner">
                    <div class="cmr-loc-details">
                        <h4>Address</h4>
                        <div class="cmr-loc-address-block">
                            <div class="icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="cmr-loc-company">CYBER HOUSE</div>
                            <div class="cmr-loc-address">
                                B-35, Sector 32<br>
                                Gurgaon-122001
                            </div>
                        </div>
                        <div class="cmr-loc-contact">
                            <a href="mailto:info@cmrindia.com" class="email">info@cmrindia.com</a>
                            <a href="tel:+911244822222" class="phone">+91-124-4822222</a>
                        </div>
                    </div>
                    <div class="cmr-loc-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3507.037227411979!2d77.0366883!3d28.4384462!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1872b7e19bf1%3A0x6b3060c42730b91e!2sCyber%20Media%20Research!5e0!3m2!1sen!2sin!4v1718880000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item 2: Bengaluru -->
        <div class="cmr-loc-item">
            <div class="cmr-loc-header">
                <h3 class="cmr-loc-title">Bengaluru</h3>
                <div class="cmr-loc-icon"></div>
            </div>
            <div class="cmr-loc-content">
                <div class="cmr-loc-content-inner">
                    <div class="cmr-loc-details">
                        <h4>Address</h4>
                        <div class="cmr-loc-address-block">
                            <div class="icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="cmr-loc-company">CMR Bengaluru</div>
                            <div class="cmr-loc-address">
                                [Bengaluru Address Line 1]<br>
                                [Bengaluru Address Line 2]
                            </div>
                        </div>
                        <div class="cmr-loc-contact">
                            <a href="mailto:info@cmrindia.com" class="email">info@cmrindia.com</a>
                            <a href="tel:+911244822222" class="phone">+91-124-4822222</a>
                        </div>
                    </div>
                    <div class="cmr-loc-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3888.001696423075!2d77.5912997148219!3d12.971598690855901!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae1670c9b44e6d%3A0xf8dfc3e8517e4fe0!2sBengaluru%2C%20Karnataka!5e0!3m2!1sen!2sin!4v1718880000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item 3: Mumbai -->
        <div class="cmr-loc-item">
            <div class="cmr-loc-header">
                <h3 class="cmr-loc-title">Mumbai</h3>
                <div class="cmr-loc-icon"></div>
            </div>
            <div class="cmr-loc-content">
                <div class="cmr-loc-content-inner">
                    <div class="cmr-loc-details">
                        <h4>Address</h4>
                        <div class="cmr-loc-address-block">
                            <div class="icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="cmr-loc-company">CMR Mumbai</div>
                            <div class="cmr-loc-address">
                                [Mumbai Address Line 1]<br>
                                [Mumbai Address Line 2]
                            </div>
                        </div>
                        <div class="cmr-loc-contact">
                            <a href="mailto:info@cmrindia.com" class="email">info@cmrindia.com</a>
                            <a href="tel:+911244822222" class="phone">+91-124-4822222</a>
                        </div>
                    </div>
                    <div class="cmr-loc-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.116099518!2d72.7410988628036!3d19.082197839352945!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c6306644edc1%3A0x5da4ed8f8d648c69!2sMumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1718880000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item 4: Singapore -->
        <div class="cmr-loc-item">
            <div class="cmr-loc-header">
                <h3 class="cmr-loc-title">Singapore</h3>
                <div class="cmr-loc-icon"></div>
            </div>
            <div class="cmr-loc-content">
                <div class="cmr-loc-content-inner">
                    <div class="cmr-loc-details">
                        <h4>Address</h4>
                        <div class="cmr-loc-address-block">
                            <div class="icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="cmr-loc-company">CMR Singapore</div>
                            <div class="cmr-loc-address">
                                [Singapore Address Line 1]<br>
                                [Singapore Address Line 2]
                            </div>
                        </div>
                        <div class="cmr-loc-contact">
                            <a href="mailto:info@cmrindia.com" class="email">info@cmrindia.com</a>
                            <a href="tel:+911244822222" class="phone">+91-124-4822222</a>
                        </div>
                    </div>
                    <div class="cmr-loc-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d255282.3235282208!2d103.70416551604928!3d1.313996123018251!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da11238a8b9375%3A0x887869cf52abf5c4!2sSingapore!5e0!3m2!1sen!2sin!4v1718880000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const headers = document.querySelectorAll('.cmr-loc-header');
            
            headers.forEach(header => {
                header.addEventListener('click', function() {
                    const item = this.parentElement;
                    const isActive = item.classList.contains('active');

                    // Close all items
                    document.querySelectorAll('.cmr-loc-item').forEach(i => {
                        i.classList.remove('active');
                    });

                    // If it was NOT active, open it (we already removed active from all)
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
