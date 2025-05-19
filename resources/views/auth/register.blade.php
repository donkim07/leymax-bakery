<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - Register</title>

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="/" class="logo d-flex align-items-center w-auto">
                  <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}">
                  <span class="d-none d-lg-block">{{ config('app.name') }}</span>
                </a>
            </div>

              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create an account</p>
                </div>

                  @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                  <form class="row g-3" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               id="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                      <div class="input-group has-validation">
                            <span class="input-group-text">@</span>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                               id="username" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Company Name</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" 
                               id="company_name" value="{{ old('company_name') }}" required>
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" 
                               id="password_confirmation" required>
                      </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Select Business Type</label>
                      <div class="row">
                        <div class="col-md-4 mb-2">
                          <div class="form-check card p-3">
                                <input type="checkbox" name="businesses[]" id="bakery" value="bakery" 
                                    class="form-check-input" {{ in_array('bakery', old('businesses', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bakery">
                              <h6>Bakery Shop</h6>
                              <small class="text-muted">Manage bakery operations</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                          <div class="form-check card p-3">
                                <input type="checkbox" name="businesses[]" id="tools" value="cake_tools" 
                                    class="form-check-input" {{ in_array('cake_tools', old('businesses', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tools">
                              <h6>Cake Tools Shop</h6>
                              <small class="text-muted">Sell baking supplies</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                          <div class="form-check card p-3">
                                <input type="checkbox" name="businesses[]" id="academy" value="academy" 
                                    class="form-check-input" {{ in_array('academy', old('businesses', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="academy">
                              <h6>Leymax Academy</h6>
                              <small class="text-muted">Cake-making courses</small>
                                </label>
                          </div>
                            </div>
                        </div>
                        @error('businesses')
                          <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                        <input class="form-check-input @error('terms') is-invalid @enderror" name="terms" 
                               type="checkbox" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">
                          I agree and accept the <a href="#">terms and conditions</a>
                            </label>
                        @error('terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Create Account</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
