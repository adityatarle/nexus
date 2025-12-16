    <footer class="bg-dark text-white" style="background: #1a1a1a !important;">
      <div class="container-lg py-5">
        <div class="row g-4">
          
          <!-- Company Info & Social Links -->
          <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
            <div class="footer-widget">
              <h5 class="text-white mb-3 fw-bold">Green Leaf Agro Implements</h5>
              <p class="text-light mb-3" style="color: #e0e0e0 !important;">Empowering Agriculture, Enriching Harvests</p>
              <p class="text-light small mb-3" style="color: #d0d0d0 !important;">Leading manufacturer of tractor operated agricultural implements & machineries with 33+ years of experience.</p>
              <div class="social-links">
                <h6 class="text-white mb-2 small fw-bold">Follow Us</h6>
                <ul class="list-inline mb-0">
                  <li class="list-inline-item me-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Facebook">
                      <svg width="18" height="18"><use xlink:href="#facebook"></use></svg>
                    </a>
                  </li>
                  <li class="list-inline-item me-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Twitter">
                      <svg width="18" height="18"><use xlink:href="#twitter"></use></svg>
                    </a>
                  </li>
                  <li class="list-inline-item me-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" title="YouTube">
                      <svg width="18" height="18"><use xlink:href="#youtube"></use></svg>
                    </a>
                  </li>
                  <li class="list-inline-item me-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Instagram">
                      <svg width="18" height="18"><use xlink:href="#instagram"></use></svg>
                    </a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" title="LinkedIn">
                      <svg width="18" height="18"><use xlink:href="#link"></use></svg>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Quick Links -->
          <div class="col-lg-2 col-md-3 col-sm-6 mb-4 mb-lg-0">
            <div class="footer-widget">
              <h5 class="text-white mb-3 fw-bold">Quick Links</h5>
              <ul class="list-unstyled mb-0">
                <li class="mb-2">
                  <a href="{{ route('home') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                    <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                    Home
                  </a>
                </li>
                <li class="mb-2">
                  <a href="{{ route('about') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                    <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                    About Us
                  </a>
                </li>
                <li class="mb-2">
                  <a href="{{ route('agriculture.products.index') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                    <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                    Products
                  </a>
                </li>
                <li class="mb-2">
                  <a href="{{ route('agriculture.categories.index') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                    <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                    Categories
                  </a>
                </li>
                <li class="mb-2">
                  <a href="{{ route('contact') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                    <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                    Contact Us
                  </a>
                </li>
                <li class="mb-2">
                  <a href="{{ route('dealer.register') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                    <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                    Become A Dealer
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <!-- Account Links -->
          <div class="col-lg-2 col-md-3 col-sm-6 mb-4 mb-lg-0">
            <div class="footer-widget">
              <h5 class="text-white mb-3 fw-bold">My Account</h5>
              <ul class="list-unstyled mb-0">
                @auth
                  @if(auth()->user()->role === 'admin')
                    <li class="mb-2">
                      <a href="{{ route('admin.dashboard') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                        <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                        Admin Dashboard
                      </a>
                    </li>
                  @elseif(auth()->user()->role === 'dealer')
                    <li class="mb-2">
                      <a href="{{ route('dealer.dashboard') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                        <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                        Dealer Dashboard
                      </a>
                    </li>
                  @else
                    <li class="mb-2">
                      <a href="{{ route('customer.dashboard') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                        <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                        My Dashboard
                      </a>
                    </li>
                  @endif
                  <li class="mb-2">
                    <a href="{{ route('agriculture.wishlist.index') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                      <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                      My Wishlist
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="{{ route('agriculture.cart.index') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                      <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                      Shopping Cart
                    </a>
                  </li>
                @else
                  <li class="mb-2">
                    <a href="{{ route('auth.login') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                      <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                      Login
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="{{ route('auth.register') }}" class="text-light text-decoration-none small d-inline-block" style="color: #e0e0e0 !important; transition: color 0.3s;">
                      <svg width="14" height="14" class="me-1" style="color: #6BB252;"><use xlink:href="#arrow-right"></use></svg>
                      Register
                    </a>
                  </li>
                @endauth
              </ul>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
            <div class="footer-widget">
              <h5 class="text-white mb-3 fw-bold">Contact Information</h5>
              
              <div class="mb-3">
                <h6 class="text-white small fw-bold mb-2">Head Office</h6>
                <p class="text-light small mb-0" style="color: #d0d0d0 !important; line-height: 1.6;">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit,<br>
                  sed do eiusmod tempor incididunt ut labore,<br>
                  Green Leaf City, State - 12345, Country
                </p>
              </div>

              <div class="mb-3">
                <h6 class="text-white small fw-bold mb-2">Get In Touch</h6>
                <ul class="list-unstyled mb-0">
                  <li class="mb-2">
                    <a href="mailto:info@greenleaf.com" class="text-light text-decoration-none small d-flex align-items-center" style="color: #e0e0e0 !important;">
                      <svg width="16" height="16" class="me-2" style="color: #6BB252;"><use xlink:href="#user"></use></svg>
                      <span>info@greenleaf.com</span>
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="mailto:sales@greenleaf.com" class="text-light text-decoration-none small d-flex align-items-center" style="color: #e0e0e0 !important;">
                      <svg width="16" height="16" class="me-2" style="color: #6BB252;"><use xlink:href="#user"></use></svg>
                      <span>sales@greenleaf.com</span>
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="tel:+12345678900" class="text-light text-decoration-none small d-flex align-items-center" style="color: #e0e0e0 !important;">
                      <svg width="16" height="16" class="me-2" style="color: #6BB252;"><use xlink:href="#user"></use></svg>
                      <span>+1 234 567 8900</span>
                    </a>
                  </li>
                  <li>
                    <a href="https://wa.me/12345678900" class="text-light text-decoration-none small d-flex align-items-center" style="color: #e0e0e0 !important;" target="_blank">
                      <svg width="16" height="16" class="me-2" style="color: #6BB252;"><use xlink:href="#link"></use></svg>
                      <span>WhatsApp: +1 234 567 8900</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="border-top" style="border-color: #333 !important; background: #0f0f0f !important;">
        <div class="container-lg py-3">
          <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
              <p class="text-light small mb-0" style="color: #d0d0d0 !important;">
                Copyright © {{ date('Y') }} <strong class="text-white">Green Leaf Agro Implements</strong>. All Rights Reserved.
              </p>
            </div>
            <div class="col-md-6 text-md-end">
              <ul class="list-inline mb-0">
                <li class="list-inline-item">
                  <a href="{{ route('terms') }}" class="text-light text-decoration-none small" style="color: #d0d0d0 !important;">Terms Of Use</a>
                </li>
                <li class="list-inline-item text-light" style="color: #666 !important;">|</li>
                <li class="list-inline-item">
                  <a href="{{ route('privacy') }}" class="text-light text-decoration-none small" style="color: #d0d0d0 !important;">Privacy Policy</a>
                </li>
                <li class="list-inline-item text-light" style="color: #666 !important;">|</li>
                <li class="list-inline-item">
                  <a href="{{ route('contact') }}" class="text-light text-decoration-none small" style="color: #d0d0d0 !important;">Contact</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <style>
      /* Footer Link Hover Effects */
      footer a:hover {
        color: #6BB252 !important;
        text-decoration: none !important;
      }

      /* Footer Widget Spacing */
      .footer-widget {
        height: 100%;
      }

      /* Better text readability */
      footer {
        background: #1a1a1a !important;
      }
      
      footer .text-light {
        color: #e0e0e0 !important;
      }
      
      footer h5, footer h6 {
        color: #ffffff !important;
      }

      /* Responsive Footer */
      @media (max-width: 767.98px) {
        footer .container-lg {
          padding: 2rem 1rem;
        }
        
        .footer-widget {
          margin-bottom: 2rem;
        }
      }
    </style>

    <script src="{{ asset('assets/organic/js/jquery-1.11.0.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/organic/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/organic/js/script.js') }}"></script>
  </body>
</html>
