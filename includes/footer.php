    <!-- --------------------------Footer----------------------------- -->
    <footer class="footer">
        <div class="container">
            <div class="about-us" data-aos="fade-right" data-aos-delay="200">
                <h2>About Us</h2>
                <p>
                    Modern blog platform built with PHP, JavaScript, and modern web technologies. 
                    Sharing knowledge, experiences, and insights about technology, lifestyle, and more.
                </p>
            </div>
            <div class="newsletter" data-aos="fade-right" data-aos-delay="200">
                <h2>Newsletter</h2>
                <p>Stay update with our latest</p>
                <div class="form-element">
                    <form id="newsletter-form" method="POST" action="<?php echo getBaseUrl(); ?>/api/newsletter.php">
                        <input type="email" name="email" placeholder="Email" required />
                        <button type="submit"><i class="fas fa-chevron-right"></i></button>
                    </form>
                </div>
            </div>
            <div class="instagram" data-aos="fade-left" data-aos-delay="200">
                <h2>Instagram</h2>
                <div class="flex-row">
                    <img src="<?php echo asset('instagram/thumb-card3.png'); ?>" alt="instagram" />
                    <img src="<?php echo asset('instagram/thumb-card4.png'); ?>" alt="instagram2" />
                    <img src="<?php echo asset('instagram/thumb-card5.png'); ?>" alt="instagram3" />
                </div>
                <div class="flex-row">
                    <img src="<?php echo asset('instagram/thumb-card6.png'); ?>" alt="instagram4" />
                    <img src="<?php echo asset('instagram/thumb-card7.png'); ?>" alt="instagram5" />
                    <img src="<?php echo asset('instagram/thumb-card8.png'); ?>" alt="instagram6" />
                </div>
            </div>
            <div class="follow" data-aos="fade-left" data-aos-delay="200">
                <h2>Follow Us</h2>
                <p>Let us be Social</p>
                <div>
                    <i class="fab fa-facebook-f"></i>
                    <i class="fab fa-twitter"></i>
                    <i class="fab fa-instagram"></i>
                    <i class="fab fa-youtube"></i>
                </div>
            </div>
        </div>

        <div class="rights flex-row">
            <h4 class="text-gray">
                Copyright &copy; <?php echo date('Y'); ?> All rights reserved | made by
                <a href="#" target="_blank">Leranuva</a>
            </h4>
        </div>

        <div class="move-up">
            <span><i class="fas fa-arrow-circle-up fa-2x"></i></span>
        </div>
    </footer>
    <!-- --------------X-----------End the Footer--------------X-------------- -->
    
    <!--Jquery Library file-->
    <script src="<?php echo asset('js/jquery3.6.0.main.js'); ?>"></script>

    <!--Owl-Carousel-JS-->
    <script src="<?php echo asset('js/owl.carousel.min.js'); ?>"></script>

    <!--AOS JS Library-->
    <script src="<?php echo asset('js/aos.js'); ?>"></script>

    <!--Custom Javascript file-->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    
    <!--Modern JavaScript-->
    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>

