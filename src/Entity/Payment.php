<?php

declare(strict_types=1);

namespace MyApp\Entity;
use MyApp\Entity\Invoice;

class Payment
{
    private ?int $id;
    private Invoice $invoice;
    private float $amount;
    private string $payment_method;
    private ?string $transaction_id;
    private string $status;
    private string $payment_date;
    private ?string $last_four_digits;

    public function __construct(
        ?int $id,
        Invoice $invoice,
        float $amount,
        string $payment_method,
        ?string $transaction_id,
        string $status,
        string $payment_date,
        ?string $last_four_digits
    ) {
        $this->id = $id;
        $this->invoice = $invoice;
        $this->amount = $amount;
        $this->payment_method = $payment_method;
        $this->transaction_id = $transaction_id;
        $this->status = $status;
        $this->payment_date = $payment_date;
        $this->last_four_digits = $last_four_digits;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getInvoice(): Invoice { return $this->invoice; }
    public function setInvoice(Invoice $invoice): void { $this->invoice = $invoice; }

    public function getAmount(): float { return $this->amount; }
    public function setAmount(float $amount): void { $this->amount = $amount; }

    public function getPaymentMethod(): string { return $this->payment_method; }
    public function setPaymentMethod(string $payment_method): void { $this->payment_method = $payment_method; }

    public function getTransactionId(): ?string { return $this->transaction_id; }
    public function setTransactionId(?string $transaction_id): void { $this->transaction_id = $transaction_id; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getPaymentDate(): string { return $this->payment_date; }
    public function setPaymentDate(string $payment_date): void { $this->payment_date = $payment_date; }

    public function getLastFourDigits(): ?string { return $this->last_four_digits; }
    public function setLastFourDigits(?string $last_four_digits): void { $this->last_four_digits = $last_four_digits; }
}
