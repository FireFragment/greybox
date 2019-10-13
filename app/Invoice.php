<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use rikudou\CzQrPayment\QrPayment;

class Invoice extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fakturoid_id', 'number', 'client', 'status', 'issued_on', 'taxable_fulfillment_due', 'due_on', 'currency', 'language', 'total', 'paid_amount', 'qr_url'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'fakturoid_id'
    ];

    public $lines = [];
    public $due;

    public function __construct($dueDate)
    {
        $this->setDue($dueDate);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client', 'id');
    }

    public function generateQr()
    {
        $payment = new QrPayment(getenv('ACCOUNT_CZ_NUMBER'),getenv('ACCOUNT_CZ_BANK'));

        $payment->setVariableSymbol($this->number);
        $payment->setAmount($this->total);
        $payment->setCurrency($this->currency);

        if (file_exists("qrs/$this->qr_url.png")) {
            $qrFileName = $this->qr_url;
        } else {
            $qrFileName = uniqid($this->number, true);
        }
        $qrImage = $payment->getQrImage()->writeString();

        $qrFile = fopen("qrs/$qrFileName.png", 'w');
        fwrite($qrFile, $qrImage);
        fclose($qrFile);

        $this->qr_url = $qrFileName;
        return $this->qr_url;
    }

    public function getPdf(\Fakturoid\Client $fc)
    {
        $invoiceFileName = $this->qr_url;
        $invoicePdf = $fc->getInvoicePdf($this->fakturoid_id);
        $count = 1;

        while ($invoicePdf->getStatusCode() !== 200) {
            sleep(2);
            $invoicePdf = $fc->getInvoicePdf($this->fakturoid_id);
            $count++;

            if ($count >= 5 && $invoicePdf->getStatusCode() !== 200) {
                return false;
            }
        }

        $invoiceFile = fopen("invoices/$invoiceFileName.pdf", 'w');
        fwrite($invoiceFile, $invoicePdf->getBody());
        fclose($invoiceFile);

        return true;
    }

    public function setDue($dueDate)
    {
        $difference = strtotime($dueDate) - time();
        if (0 >= $difference){
            $this->due = 0;
        } else {
            $this->due = round($difference / (24 * 60 * 60));
        }
    }

    public function setLine($description, $quantity, $unit, $price)
    {
        $this->lines[] = [
            'name' => $description,
            'quantity' => $quantity,
            'unit_name' => $unit,
            'unit_price' => $price
        ];
    }

    public function setFakturoidData($fakturoidInvoice)
    {
        $this->fakturoid_id = $fakturoidInvoice->id;
        $this->number = $fakturoidInvoice->number;
        $this->status = $fakturoidInvoice->status;
        $this->issued_on = $fakturoidInvoice->issued_on;
        $this->due_on = $fakturoidInvoice->due_on;
        $this->currency = $fakturoidInvoice->currency;
        $this->language = $fakturoidInvoice->language;
        $this->total = $fakturoidInvoice->total;
        $this->paid_amount = $fakturoidInvoice->paid_amount;
    }

    public function setFullUrls(\Fakturoid\Client $fc) {

        $this->qr_full_url = "https://debate-greybox.herokuapp.com/qrs/$this->qr_url.png"; // TODO: nastavovat adresu dynamicky
        if ($this->getPdf($fc)) {
            $this->pdf_full_url = "https://debate-greybox.herokuapp.com/invoices/$this->pdf_url.pdf";
        }
    }
}
