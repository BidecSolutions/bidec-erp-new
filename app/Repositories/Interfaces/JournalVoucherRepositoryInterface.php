<?php
namespace App\Repositories\Interfaces;

Interface JournalVoucherRepositoryInterface{

    public function allJournalVouchers($data);
    public function storeJournalVoucher($data);
    public function findJournalVoucher($id);
    public function updateJournalVoucher($id, $data);
    //public function changeJournalVoucherStatus($id,$status);
}
