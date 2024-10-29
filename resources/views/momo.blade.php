<div class="card">
    <div class="card-header">{{ __('Payment Momo') }}</div>

    <div class="card-body">
        <form action="/payment-momo" method="POST" enctype="application/x-www-form-urlencoded">
            @csrf
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" required value="55000">
            </div>
            <div class="form-group mt-3 text-end">
                <button type="submit" class="btn btn-primary" id="btn-momo">Pay with MoMo</button>
            </div>
        </form>
    </div>
</div>
