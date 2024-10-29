<!-- resources/views/payment.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>VNPay Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>VNPay Payment</h2>
    <form action="/payment-vnpay" method="POST">
        @csrf
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="currency">Currency</label>
            <input type="text" class="form-control" id="currency" name="currency" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
        </div>
        {{-- <button type="submit" class="btn btn-primary" name="redirect">Pay with VNPay</button> --}}
        <button type="submit" class="btn btn-primary">Pay with VNPay</button>
    </form>
</div>
</body>
</html>
