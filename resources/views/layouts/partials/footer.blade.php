    <footer class="py-5">
      <div class="container-lg">
        <div class="row">

          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="footer-menu">
              <img src="{{ asset('assets/organic/images/logo.svg') }}" width="240" height="70" alt="logo">
              <div class="social-links mt-3">
                <ul class="d-flex list-unstyled gap-2">
                  <li>
                    <a href="#" class="btn btn-outline-light">
                      <svg width="16" height="16"><use xlink:href="#facebook"></use></svg>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="btn btn-outline-light">
                      <svg width="16" height="16"><use xlink:href="#twitter"></use></svg>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="btn btn-outline-light">
                      <svg width="16" height="16"><use xlink:href="#youtube"></use></svg>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="btn btn-outline-light">
                      <svg width="16" height="16"><use xlink:href="#instagram"></use></svg>
                    </a>
                  </li>
                  <li>
                    <a href="#" class="btn btn-outline-light">
                      <svg width="16" height="16"><use xlink:href="#amazon"></use></svg>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-2 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title">Company</h5>
              <ul class="menu-list list-unstyled">
                <li class="menu-item">
                  <a href="{{ route('about') }}" class="nav-link">About Us</a>
                </li>
                <li class="menu-item">
                  <a href="{{ route('contact') }}" class="nav-link">Contact</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-2 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title">Shop</h5>
              <ul class="menu-list list-unstyled">
                <li class="menu-item">
                  <a href="{{ route('agriculture.products.index') }}" class="nav-link">All Products</a>
                </li>
                <li class="menu-item">
                  <a href="{{ route('agriculture.categories.index') }}" class="nav-link">Categories</a>
                </li>
                <li class="menu-item">
                  <a href="{{ route('agriculture.products.index', ['filter' => 'featured']) }}" class="nav-link">Featured Products</a>
                </li>
                <li class="menu-item">
                  <a href="{{ route('agriculture.products.index', ['sort' => 'new_arrivals']) }}" class="nav-link">New Arrivals</a>
                </li>
                <li class="menu-item">
                  <a href="{{ route('agriculture.products.index', ['sort' => 'best_selling']) }}" class="nav-link">Best Sellers</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-2 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title">Account</h5>
              <ul class="menu-list list-unstyled">
                @auth
                  @if(auth()->user()->role === 'admin')
                    <li class="menu-item">
                      <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin Dashboard</a>
                    </li>
                  @elseif(auth()->user()->role === 'dealer')
                    <li class="menu-item">
                      <a href="{{ route('dealer.dashboard') }}" class="nav-link">Dealer Dashboard</a>
                    </li>
                  @else
                    <li class="menu-item">
                      <a href="{{ route('customer.dashboard') }}" class="nav-link">My Dashboard</a>
                    </li>
                  @endif
                  <li class="menu-item">
                    <a href="{{ route('agriculture.wishlist.index') }}" class="nav-link">My Wishlist</a>
                  </li>
                  <li class="menu-item">
                    <a href="{{ route('agriculture.cart.index') }}" class="nav-link">Shopping Cart</a>
                  </li>
                  <li class="menu-item">
                    <form action="{{ route('auth.logout') }}" method="POST">
                      @csrf
                      <button type="submit" class="nav-link btn btn-link p-0 text-start">Logout</button>
                    </form>
                  </li>
                @else
                  <li class="menu-item">
                    <a href="{{ route('auth.login') }}" class="nav-link">Login</a>
                  </li>
                  <li class="menu-item">
                    <a href="{{ route('auth.register') }}" class="nav-link">Register</a>
                  </li>
                  <li class="menu-item">
                    <a href="{{ route('dealer.register') }}" class="nav-link">Become a Dealer</a>
                  </li>
                @endauth
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title">Get In Touch</h5>
              <p>Have questions? Contact our support team for assistance with your orders and products.</p>
              <div class="mt-3">
                <a href="{{ route('contact') }}" class="btn btn-dark w-100">Contact Support</a>
              </div>
              <div class="mt-3">
                <p class="mb-1"><strong>Admin Login</strong></p>
                <a href="{{ route('admin.login') }}" class="text-decoration-none">Access Admin Panel →</a>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </footer>
    <div id="footer-bottom">
      <div class="container-lg">
        <div class="row">
          <div class="col-md-12 copyright text-center">
            <p>© {{ date('Y') }} Nexus Agriculture. All rights reserved. | Powered by Laravel</p>
          </div>
        </div>
      </div>
    </div>
    <script src="{{ asset('assets/organic/js/jquery-1.11.0.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/organic/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/organic/js/script.js') }}"></script>
  </body>
</html>