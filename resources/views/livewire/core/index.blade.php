  @push('css')
  @endpush
  <style>
      .link {
          display: flex;
          justify-content: center;
          align-items: center;
          overflow: hidden;
      }

      .card-body p {
          max-height: 20vh;
          overflow: hidden;
      }

      .img-media {
          /* flex-shrink: 0; */
          /* display: inline-block; */
          display: flex;
          justify-content: center;
          align-content: center;
          text-align: center;
          max-width: 30%;
          max-height: 40%;
          overflow: hidden;
          border-radius: 5%;

      }

      .fill {
          display: flex;
          justify-content: center;
          align-items: center;
          overflow: hidden;
      }

      .fill img {
          flex-shrink: 0;
          min-width: 100%;
          min-height: 50%;
          border-radius: 5%;
      }

      .floating-menu {
          position: fixed;
          bottom: 20px;
          left: 20px;
          background-color: #8d6868;
          border: 1px solid #ccc;
          padding: 10px;
          border-radius: 5px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          animation: shake 0.5s ease-in-out infinite;
          /* Animation */
          opacity: 0;
          /* Initially hidden */
      }

      li {
          list-style-type: none;

      }

      .card {
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          border-radius: 10px;
          transition: transform 0.2s ease-in-out;
      }

      .card:hover {
          transform: translateY(-10px);
          z-index: 2;
      }

      .li a {
          text-decoration: none;
          color: inherit;
      }
  </style>
  <div class="container-fluid">
      <div class="row">

          <div class="card card-body mx-3 col-md-3 link d-flex justify-content-start" style="z-index: 1;">
              <li class="{{ session('module') === 'HRM' ? 'active' : '' }}">
                  <a href="{{ route('hrm.employees.dashboard') }}">
                      <i class="icofont icofont-users fa-3x"></i>
                      <p class="mx-1 fw-bold txt-dark">HRM</p>
                  </a>
              </li>
          </div>

          <div class="card card-body mx-3 col-md-3 link d-flex justify-content-start" style="z-index: 1;">
              <li class="{{ session('active_module') === 'inventory' ? 'active' : '' }}">
                  <a href="">
                      <i class="icofont icofont-shopping-cart fa-3x"></i>
                      <p class="mx-1 fw-bold txt-dark">Inventory</p>
                  </a>
              </li>
          </div>

          <div class="card card-body mx-3 col-md-3 link d-flex justify-content-start" style="z-index: 1;">
              <li class="{{ session('active_module') === 'sales' ? 'active' : '' }}">
                  <a href="">
                      <i class="icofont icofont-bill fa-3x"></i>
                      <p class="mx-1 fw-bold txt-dark">Payroll</p>
                  </a>
              </li>
          </div>
          <div class="card card-body mx-3 col-md-3 link d-flex justify-content-start" style="z-index: 1;">
              <li class="{{ session('active_module') === 'sales' ? 'active' : '' }}">
                  <a href="">
                      <i class="icofont icofont-bill fa-3x"></i>
                      <p class="mx-1 fw-bold txt-dark">SAFARI</p>
                  </a>
              </li>
          </div>
          <div class="card card-body mx-3 col-md-3 link d-flex justify-content-start" style="z-index: 1;">
              <li class="{{ session('active_module') === 'sales' ? 'active' : '' }}">
                  <a href="">
                      <i class="icofont icofont-calendar fa-3x"></i>
                      <p class="mx-1 fw-bold txt-dark">Leave</p>
                  </a>
              </li>
          </div>

          <!-- Card 4 -->
          <div class="card card-body mx-3 col-md-3 link d-flex justify-content-start" style="z-index: 1;">
              <li class="{{ session('active_module') === 'settings' ? 'active' : '' }}">
                  <a href="">
                      <i class="icofont icofont-settings fa-3x"></i>
                      <p class="mx-1 fw-bold txt-dark">Fuel</p>
                  </a>
              </li>
          </div>
      </div>
  </div>

  @push('scripts')

      {{-- <script src="{{ asset('assets/js/dashboard/dashboard_2.js') }}"></script> --}}

      
  @endpush
