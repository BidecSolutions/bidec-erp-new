<?php
namespace App\Repositories\Interfaces;

Interface PaymentRepositoryInterface{

    public function allPayments($data);
    public function storePayment($data);
    public function findPayment($id);
    public function updatePayment($data, $id);
    public function changePaymentStatus($id,$status);
}
