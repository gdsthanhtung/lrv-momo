<div class="card">
    <div class="card-header">{{ __('Payment VNPay') }}</div>

    <div class="card-body">
        <form action="/payment-vnpay" method="POST">
            @csrf
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" required value="77000">
            </div>
            <div class="form-group mt-3 text-end">
                <button type="submit" class="btn btn-primary" name="payUrl" id="btn-vnpay">Pay with VNPay</button>
            </div>
        </form>
    </div>
</div>
