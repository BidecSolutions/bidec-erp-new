<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use PayPal\Api\Amount;
    use PayPal\Api\Payer;
    use PayPal\Api\Payment;
    use PayPal\Api\RedirectUrls;
    use PayPal\Api\Transaction;
    use PayPal\Auth\OAuthTokenCredential;
    use PayPal\Rest\ApiContext;

    class PayPalController extends Controller
    {
        private $apiContext;

        public function __construct()
        {
            $this->apiContext = new ApiContext(
                new OAuthTokenCredential(env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET'))
            );
        }

        public function showPaymentForm()
        {
            return view('payment_testing.payment');
        }

        public function processPayment(Request $request)
        {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $amount = new Amount();
            $amount->setTotal($request->amount);
            $amount->setCurrency('USD');

            $transaction = new Transaction();
            $transaction->setAmount($amount);

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(url('/paypal/status'))
                         ->setCancelUrl(url('/'));

            $payment = new Payment();
            $payment->setIntent('sale')
                    ->setPayer($payer)
                    ->setTransactions([$transaction])
                    ->setRedirectUrls($redirectUrls);

            try {
                $payment->create($this->apiContext);
                return redirect()->away($payment->getApprovalLink());
            } catch (\Exception $e) {
                return redirect('/')->with('error', 'Payment failed: ' . $e->getMessage());
            }
        }

        public function paymentStatus()
        {
            // Handle payment status here
        }
    }

?>
