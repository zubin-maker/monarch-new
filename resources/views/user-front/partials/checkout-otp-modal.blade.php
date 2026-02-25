{{-- Shopflo-style checkout: Proceed to Pay → Phone → OTP → (optional) Address → checkout --}}
<div id="checkout-otp-modal" class="modal fade" tabindex="-1" aria-labelledby="checkoutOtpModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" id="checkoutOtpModalLabel">{{ $keywords['Proceed to Pay'] ?? __('Proceed To Pay') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0">
        {{-- Step 1: Phone --}}
        <div id="checkout-otp-step-phone" class="checkout-otp-step">
          <p class="text-muted small mb-3">{{ $keywords['Enter your phone number to continue'] ?? __('Enter your phone number to continue') }}</p>
          <div class="mb-3">
            <label for="checkout-otp-phone" class="form-label">{{ $keywords['Phone Number'] ?? __('Phone Number') }} *</label>
            <input type="tel" class="form-control" id="checkout-otp-phone" placeholder="e.g. +919876543210" autocomplete="tel">
            <div id="checkout-otp-phone-error" class="invalid-feedback"></div>
          </div>
          <button type="button" class="btn btn-primary w-100" id="checkout-otp-send-btn">{{ $keywords['Send OTP'] ?? __('Send OTP') }}</button>
        </div>

        {{-- Step 2: OTP --}}
        <div id="checkout-otp-step-otp" class="checkout-otp-step d-none">
          <p class="mb-2">{{ $keywords['Enter OTP sent to'] ?? __('Enter OTP sent to') }} <span id="checkout-otp-display-phone" class="fw-medium"></span> <button type="button" class="btn btn-link btn-sm p-0 ms-1" id="checkout-otp-edit-phone">{{ $keywords['Edit'] ?? __('Edit') }}</button></p>
          <p class="text-muted small mb-3">{{ $keywords['Please enter the correct OTP to continue'] ?? __('Please enter the correct OTP to continue') }}</p>
          <div class="d-flex gap-2 justify-content-center mb-3 flex-wrap" id="checkout-otp-digits">
            <input type="text" class="form-control text-center" maxlength="1" style="width: 2.5rem;" id="otp-1" data-idx="0" inputmode="numeric" autocomplete="one-time-code">
            <input type="text" class="form-control text-center" maxlength="1" style="width: 2.5rem;" id="otp-2" data-idx="1" inputmode="numeric">
            <input type="text" class="form-control text-center" maxlength="1" style="width: 2.5rem;" id="otp-3" data-idx="2" inputmode="numeric">
            <input type="text" class="form-control text-center" maxlength="1" style="width: 2.5rem;" id="otp-4" data-idx="3" inputmode="numeric">
            <input type="text" class="form-control text-center" maxlength="1" style="width: 2.5rem;" id="otp-5" data-idx="4" inputmode="numeric">
            <input type="text" class="form-control text-center" maxlength="1" style="width: 2.5rem;" id="otp-6" data-idx="5" inputmode="numeric">
          </div>
          <div id="checkout-otp-verify-error" class="text-danger small mb-2"></div>
          <button type="button" class="btn btn-primary w-100 mb-2" id="checkout-otp-verify-btn">{{ $keywords['Verify OTP'] ?? __('Verify OTP') }}</button>
          <p class="small text-muted mb-0">{{ $keywords['Resend OTP in'] ?? __('Resend OTP in') }} <span id="checkout-otp-resend-countdown">60</span>s</p>
        </div>

        {{-- Step 3: Address (new user) --}}
        <div id="checkout-otp-step-address" class="checkout-otp-step d-none">
          <p class="text-muted small mb-3">{{ $keywords['Add your details to create an account and continue'] ?? __('Add your details to create an account and continue') }}</p>
          <div class="row g-2">
            <div class="col-6">
              <label for="checkout-otp-fname" class="form-label small">{{ $keywords['First_Name'] ?? __('First Name') }} *</label>
              <input type="text" class="form-control form-control-sm" id="checkout-otp-fname" name="first_name">
            </div>
            <div class="col-6">
              <label for="checkout-otp-lname" class="form-label small">{{ $keywords['Last_Name'] ?? __('Last Name') }} *</label>
              <input type="text" class="form-control form-control-sm" id="checkout-otp-lname" name="last_name">
            </div>
            <div class="col-12">
              <label for="checkout-otp-email" class="form-label small">{{ $keywords['Email_Address'] ?? __('Email Address') }} *</label>
              <input type="email" class="form-control form-control-sm" id="checkout-otp-email" name="email">
            </div>
            <div class="col-12">
              <label for="checkout-otp-address" class="form-label small">{{ $keywords['Address'] ?? __('Address') }} *</label>
              <textarea class="form-control form-control-sm" id="checkout-otp-address" name="billing_address" rows="2"></textarea>
            </div>
            <div class="col-6">
              <label for="checkout-otp-city" class="form-label small">{{ $keywords['City'] ?? __('City') }} *</label>
              <input type="text" class="form-control form-control-sm" id="checkout-otp-city" name="billing_city">
            </div>
            <div class="col-6">
              <label for="checkout-otp-state" class="form-label small">{{ $keywords['State'] ?? __('State') }}</label>
              <input type="text" class="form-control form-control-sm" id="checkout-otp-state" name="billing_state">
            </div>
            <div class="col-12">
              <label for="checkout-otp-country" class="form-label small">{{ $keywords['Country'] ?? __('Country') }} / {{ $keywords['Pincode'] ?? __('Pincode') }} *</label>
              <input type="text" class="form-control form-control-sm" id="checkout-otp-country" name="billing_country" placeholder="{{ $keywords['Country or Pincode'] ?? __('Country or Pincode') }}">
            </div>
          </div>
          <div id="checkout-otp-address-errors" class="text-danger small mt-2"></div>
          <button type="button" class="btn btn-primary w-100 mt-3" id="checkout-otp-submit-address">{{ $keywords['Continue to Checkout'] ?? __('Continue to Checkout') }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
