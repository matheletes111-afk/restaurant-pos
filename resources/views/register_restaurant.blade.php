<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Restaurant - RestoPOS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2E7D32;
            --primary-dark: #1B5E20;
            --secondary: #FF9800;
            --light: #F5F5F5;
            --dark: #212121;
            --gray: #757575;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .registration-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }
        
        .registration-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin: 0 auto;
        }
        
        .registration-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .registration-header h1 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .registration-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .registration-body {
            padding: 40px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }
        
        .required::after {
            content: ' *';
            color: #dc3545;
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
        }
        
        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #757575;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        
        .step.active .step-circle {
            background: var(--primary);
            color: white;
        }
        
        .step-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray);
        }
        
        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }
        
        .step-line {
            position: absolute;
            top: 20px;
            left: 50px;
            right: 50px;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .step-line-fill {
            position: absolute;
            top: 20px;
            left: 50px;
            width: 0;
            height: 2px;
            background: var(--primary);
            z-index: 0;
            transition: width 0.5s;
        }
        
        @media (max-width: 768px) {
            .registration-container {
                padding: 20px;
            }
            
            .registration-body {
                padding: 25px;
            }
            
            .step-line {
                display: none;
            }
            
            .step-line-fill {
                display: none;
            }
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: var(--gray);
        }
        
        .form-group {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="registration-card">
                        <div class="registration-header">
                            <h1><i class="fas fa-utensils me-2"></i> Register Your Restaurant</h1>
                            <p>Join RestoPOS and transform your restaurant operations</p>
                        </div>
                        
                        <div class="registration-body">
                            <!-- Step Indicator -->
                            <div class="step-indicator">
                                <div class="step active" id="step1">
                                    <div class="step-circle">1</div>
                                    <div class="step-label">Restaurant Info</div>
                                </div>
                                <div class="step" id="step2">
                                    <div class="step-circle">2</div>
                                    <div class="step-label">Owner Details</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step-line-fill" id="stepLineFill"></div>
                            </div>
                            
                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            <!-- Registration Form -->
                            <form method="POST" action="{{ route('restaurant.register') }}">
                                @csrf
                                
                                <!-- Restaurant Information -->
                                <div id="section1">
                                    <h4 class="mb-4"><i class="fas fa-store me-2"></i> Restaurant Information</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required">Restaurant Name</label>
                                                <input type="text" name="restaurant_name" class="form-control" 
                                                       value="{{ old('restaurant_name') }}" required 
                                                       placeholder="Enter restaurant name">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required">Pincode</label>
                                                <input type="text" name="pincode" class="form-control" 
                                                       value="{{ old('pincode') }}" required 
                                                       placeholder="Enter pincode">
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">Address</label>
                                                <input type="text" name="address" class="form-control" 
                                                       value="{{ old('address') }}" required 
                                                       placeholder="Enter full address">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <div></div>
                                        <button type="button" class="btn btn-register" onclick="showSection(2)">
                                            Next <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Owner Information -->
                                <div id="section2" style="display: none;">
                                    <h4 class="mb-4"><i class="fas fa-user me-2"></i> Owner Information</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required">Owner Name</label>
                                                <input type="text" name="name" class="form-control" 
                                                       value="{{ old('name') }}" required 
                                                       placeholder="Enter owner's full name">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required">Email</label>
                                                <input type="email" name="email" class="form-control" 
                                                       value="{{ old('email') }}" required 
                                                       placeholder="Enter email address">
                                                @if($errors->has('email'))
                                                    <small class="text-danger">{{ $errors->first('email') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required">Phone</label>
                                                <input type="text" name="phone" class="form-control" 
                                                       value="{{ old('phone') }}" required 
                                                       placeholder="Enter phone number">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3 form-group">
                                                <label class="form-label required">Password</label>
                                                <input type="password" name="password" id="password" 
                                                       class="form-control" required 
                                                       placeholder="Create a password">
                                                <span class="password-toggle" onclick="togglePassword()">
                                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a> 
                                            and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                                        </label>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary" onclick="showSection(1)">
                                            <i class="fas fa-arrow-left me-2"></i> Back
                                        </button>
                                        <button type="submit" class="btn btn-register">
                                            <i class="fas fa-user-plus me-2"></i> Register Restaurant
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="login-link">
                                Already have an account? <a href="{{ route('login') }}">Login here</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms of Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>By registering with RestoPOS, you agree to our terms of service...</p>
                    <!-- Add full terms here -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Privacy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>We value your privacy and are committed to protecting your personal information...</p>
                    <!-- Add full privacy policy here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showSection(sectionNumber) {
            // Hide all sections
            document.getElementById('section1').style.display = 'none';
            document.getElementById('section2').style.display = 'none';
            
            // Show selected section
            document.getElementById('section' + sectionNumber).style.display = 'block';
            
            // Update step indicator
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.remove('active');
            
            if(sectionNumber === 1) {
                document.getElementById('step1').classList.add('active');
                document.getElementById('stepLineFill').style.width = '0%';
            } else {
                document.getElementById('step1').classList.add('active');
                document.getElementById('step2').classList.add('active');
                document.getElementById('stepLineFill').style.width = '100%';
            }
        }
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const terms = document.getElementById('terms');
            
            // Password validation
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                document.getElementById('password').focus();
                return false;
            }
            
            // Terms agreement validation
            if (!terms.checked) {
                e.preventDefault();
                alert('Please agree to the Terms of Service and Privacy Policy.');
                return false;
            }
        });
        
        // Initialize step indicator
        document.addEventListener('DOMContentLoaded', function() {
            showSection(1);
        });
    </script>
</body>
</html>