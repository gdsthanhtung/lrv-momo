<div class="card">
    <div class="card-header">{{ __('Payment ZaloPay') }}</div>

    <div class="card-body">
        <form action="/payment-zalopay" method="POST">
            @csrf
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" required value="99000">
            </div>
            <div class="form-group mt-3 text-end">
                <button type="submit" class="btn btn-primary" id="btn-zalopay">Pay with ZaloPay</button>
            </div>
        </form>
    </div>
</div>
