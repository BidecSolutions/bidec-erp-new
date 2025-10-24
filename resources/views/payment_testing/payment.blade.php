
<form action="http://localhost/practice/ummah-madrasa/paypal/process" method="POST">
    @csrf
    <input type="hidden" name="amount" value="10.00">
    <input type="submit" value="Pay with PayPal">
</form>
