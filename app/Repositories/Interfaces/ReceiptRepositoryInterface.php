<?php
namespace App\Repositories\Interfaces;

Interface ReceiptRepositoryInterface{

    public function allReceipts($data);
    public function storeReceipt($data);
    public function findReceipt($id);
    public function updateReceipt($data, $id);
    public function changeReceiptStatus($id,$status);
}
