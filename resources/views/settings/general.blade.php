@extends('layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="pagetitle">
    <h1>General Settings</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">System</li>
            <li class="breadcrumb-item active">General Settings</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">System Configuration</h5>
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    <form action="{{ route('settings.general.update') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Company Information</h5>
                                        
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $settings['company_name'] ?? 'Leymax Baker' }}" required>
                                            <div class="invalid-feedback">Please enter the company name.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="app_logo" class="form-label">Company Logo</label>
                                            <div class="d-flex align-items-center">
                                                @if(isset($settings['app_logo']))
                                                <div class="me-3">
                                                    <img src="{{ $settings['app_logo'] }}" alt="Company Logo" height="50">
                                                </div>
                                                @endif
                                                <input type="file" class="form-control" id="app_logo" name="app_logo" accept="image/*">
                                            </div>
                                            <small class="text-muted">Recommended size: 200x50 pixels. Leave empty to keep current logo.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="company_address" class="form-label">Company Address</label>
                                            <textarea class="form-control" id="company_address" name="company_address" rows="2" required>{{ $settings['company_address'] ?? '' }}</textarea>
                                            <div class="invalid-feedback">Please enter the company address.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Contact Information</h5>
                                        
                                        <div class="mb-3">
                                            <label for="company_phone" class="form-label">Company Phone</label>
                                            <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}" required>
                                            <div class="invalid-feedback">Please enter the company phone number.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="company_email" class="form-label">Company Email</label>
                                            <input type="email" class="form-control" id="company_email" name="company_email" value="{{ $settings['company_email'] ?? '' }}" required>
                                            <div class="invalid-feedback">Please enter a valid email.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="registration_number" class="form-label">Registration Number</label>
                                            <input type="text" class="form-control" id="registration_number" name="registration_number" value="{{ $settings['registration_number'] ?? '' }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="tax_number" class="form-label">Tax Number</label>
                                            <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ $settings['tax_number'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">License & Payment Status</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">License Status</label>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $licenseExpiry = isset($settings['license_expiry']) ? \Carbon\Carbon::parse($settings['license_expiry']) : null;
                                                    $daysRemaining = $licenseExpiry ? $licenseExpiry->diffInDays(now(), false) : null;
                                                    $isExpired = $daysRemaining !== null && $daysRemaining <= 0;
                                                @endphp
                                                
                                                @if($isExpired)
                                                    <span class="badge bg-danger me-2">Expired</span>
                                                @elseif($daysRemaining !== null && $daysRemaining <= 30)
                                                    <span class="badge bg-warning me-2">Expiring Soon</span>
                                                @else
                                                    <span class="badge bg-success me-2">Active</span>
                                                @endif
                                                
                                                <span>
                                                    @if($licenseExpiry)
                                                        {{ $isExpired ? 'Expired on' : 'Valid until' }} {{ $licenseExpiry->format('M d, Y') }}
                                                        ({{ abs($daysRemaining) }} {{ Str::plural('day', abs($daysRemaining)) }} {{ $isExpired ? 'ago' : 'remaining' }})
                                                    @else
                                                        No expiration date set
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Payment Status</label>
                                            <div class="d-flex align-items-center">
                                                @if(isset($settings['payment_status']) && $settings['payment_status'] == 'paid')
                                                    <span class="badge bg-success me-2">Paid</span>
                                                @else
                                                    <span class="badge bg-danger me-2">Unpaid</span>
                                                @endif
                                                
                                                <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                                    <i class="bi bi-credit-card me-1"></i> Make Payment
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Regional Settings</h5>
                                        
                                        <div class="mb-3">
                                            <label for="default_currency" class="form-label">Default Currency</label>
                                            <div class="input-group">
                                                <select class="form-select" id="default_currency" name="default_currency" required>
                                                    <option value="USD" {{ ($settings['default_currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                    <option value="EUR" {{ ($settings['default_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                    <option value="GBP" {{ ($settings['default_currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                                    <option value="JPY" {{ ($settings['default_currency'] ?? '') == 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                                                    <option value="CNY" {{ ($settings['default_currency'] ?? '') == 'CNY' ? 'selected' : '' }}>CNY - Chinese Yuan</option>
                                                    <option value="KES" {{ ($settings['default_currency'] ?? '') == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                                    @if(isset($settings['custom_currencies']))
                                                        @foreach(json_decode($settings['custom_currencies'], true) as $code => $name)
                                                            <option value="{{ $code }}" {{ ($settings['default_currency'] ?? '') == $code ? 'selected' : '' }}>{{ $code }} - {{ $name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addCurrencyModal">
                                                    <i class="bi bi-plus-circle"></i> Add Currency
                                                </button>
                                            </div>
                                            <div class="invalid-feedback">Please select a currency.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="default_language" class="form-label">Default Language</label>
                                            <select class="form-select" id="default_language" name="default_language" required>
                                                <option value="en" {{ ($settings['default_language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                                <option value="fr" {{ ($settings['default_language'] ?? '') == 'fr' ? 'selected' : '' }}>French</option>
                                                <option value="es" {{ ($settings['default_language'] ?? '') == 'es' ? 'selected' : '' }}>Spanish</option>
                                                <option value="de" {{ ($settings['default_language'] ?? '') == 'de' ? 'selected' : '' }}>German</option>
                                                <option value="sw" {{ ($settings['default_language'] ?? '') == 'sw' ? 'selected' : '' }}>Swahili</option>
                                            </select>
                                            <div class="invalid-feedback">Please select a language.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="tax_rate" class="form-label">Default Tax Rate (%)</label>
                                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" min="0" max="100" step="0.01" value="{{ $settings['tax_rate'] ?? '16' }}" required>
                                            <div class="invalid-feedback">Please enter a valid tax rate (0-100).</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">System Status</h5>
                                        
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title mb-0">PHP Version</h6>
                                                        <p class="card-text fs-5">{{ phpversion() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title mb-0">Laravel Version</h6>
                                                        <p class="card-text fs-5">{{ app()->version() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title mb-0">Server</h6>
                                                        <p class="card-text fs-5">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title mb-0">Environment</h6>
                                                        <p class="card-text fs-5">{{ ucfirst(config('app.env')) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Currency Modal -->
<div class="modal fade" id="addCurrencyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Currency</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCurrencyForm" action="{{ route('settings.currency.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="currency_code" class="form-label">Currency Code</label>
                        <input type="text" class="form-control" id="currency_code" name="currency_code" required maxlength="3" placeholder="e.g. USD">
                        <div class="form-text">Enter a 3-letter ISO currency code (e.g., USD, EUR, GBP).</div>
                    </div>
                    <div class="mb-3">
                        <label for="currency_name" class="form-label">Currency Name</label>
                        <input type="text" class="form-control" id="currency_name" name="currency_name" required placeholder="e.g. US Dollar">
                        <div class="form-text">Enter the full name of the currency.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Currency</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">License Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" action="{{ route('settings.payment.process') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_plan" class="form-label">Select Plan</label>
                                <select class="form-select" id="payment_plan" name="payment_plan" required>
                                    <option value="">Select a Plan</option>
                                    <option value="monthly">Monthly ($29/month)</option>
                                    <option value="yearly">Yearly ($299/year - Save $49)</option>
                                    <option value="lifetime">Lifetime ($999 - One time payment)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mpesa">M-Pesa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Plan Benefits</h5>
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Full system access</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Unlimited products</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Priority support</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Regular updates</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Backup service</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Credit Card Payment Form -->
                    <div id="creditCardForm" style="display: none;">
                        <h6>Credit Card Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="col-md-6">
                                <label for="card_name" class="form-label">Cardholder Name</label>
                                <input type="text" class="form-control" id="card_name" name="card_name" placeholder="John Doe">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="expiry_month" class="form-label">Expiry Month</label>
                                <select class="form-select" id="expiry_month" name="expiry_month">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="expiry_year" class="form-label">Expiry Year</label>
                                <select class="form-select" id="expiry_year" name="expiry_year">
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                            </div>
                        </div>
                    </div>
                    
                    <!-- M-Pesa Payment Form -->
                    <div id="mpesaForm" style="display: none;">
                        <h6>M-Pesa Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+254</span>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="712345678">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bank Transfer Information -->
                    <div id="bankTransferInfo" style="display: none;">
                        <div class="alert alert-info">
                            <h6>Bank Transfer Information</h6>
                            <p>Please transfer the payment to the following bank account:</p>
                            <p><strong>Bank Name:</strong> Example Bank<br>
                            <strong>Account Name:</strong> Leymax Bakery<br>
                            <strong>Account Number:</strong> 1234567890<br>
                            <strong>Branch:</strong> Main Branch<br>
                            <strong>Reference:</strong> Your company name</p>
                            <p>After making the payment, please email the transaction details to <a href="mailto:payments@leymaxbakery.com">payments@leymaxbakery.com</a></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="paymentSubmitBtn">Process Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('.needs-validation');
        
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
        
        // Payment method change handler
        document.getElementById('payment_method').addEventListener('change', function() {
            const method = this.value;
            document.getElementById('creditCardForm').style.display = 'none';
            document.getElementById('mpesaForm').style.display = 'none';
            document.getElementById('bankTransferInfo').style.display = 'none';
            
            if (method === 'credit_card') {
                document.getElementById('creditCardForm').style.display = 'block';
                document.getElementById('paymentSubmitBtn').textContent = 'Process Payment';
            } else if (method === 'mpesa') {
                document.getElementById('mpesaForm').style.display = 'block';
                document.getElementById('paymentSubmitBtn').textContent = 'Request M-Pesa STK Push';
            } else if (method === 'bank_transfer') {
                document.getElementById('bankTransferInfo').style.display = 'block';
                document.getElementById('paymentSubmitBtn').textContent = 'Confirm Bank Transfer';
            } else if (method === 'paypal') {
                document.getElementById('paymentSubmitBtn').textContent = 'Pay with PayPal';
            }
        });
    });
</script>
@endsection 