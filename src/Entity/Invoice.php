<?php

declare(strict_types=1);

namespace MyApp\Entity;
use MyApp\Entity\Reservation;


class Invoice
{
    private ?int $id;
    private Reservation $reservation;
    private string $invoice_number;
    private float $amount;
    private float $tax_amount;
    private float $total_amount;
    private string $invoice_date;
    private string $due_date;
    private string $status;
    private ?string $pdf_path;

    public function __construct(
        ?int $id,
        Reservation $reservation,
        string $invoice_number,
        float $amount,
        float $tax_amount,
        float $total_amount,
        string $invoice_date,
        string $due_date,
        string $status,
        ?string $pdf_path
    ) {
        $this->id = $id;
        $this->reservation = $reservation;
        $this->invoice_number = $invoice_number;
        $this->amount = $amount;
        $this->tax_amount = $tax_amount;
        $this->total_amount = $total_amount;
        $this->invoice_date = $invoice_date;
        $this->due_date = $due_date;
        $this->status = $status;
        $this->pdf_path = $pdf_path;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getReservation(): Reservation { return $this->reservation; }
    public function setReservation(Reservation $reservation): void { $this->reservation = $reservation; }

    public function getInvoiceNumber(): string { return $this->invoice_number; }
    public function setInvoiceNumber(string $invoice_number): void { $this->invoice_number = $invoice_number; }

    public function getAmount(): float { return $this->amount; }
    public function setAmount(float $amount): void { $this->amount = $amount; }

    public function getTaxAmount(): float { return $this->tax_amount; }
    public function setTaxAmount(float $tax_amount): void { $this->tax_amount = $tax_amount; }

    public function getTotalAmount(): float { return $this->total_amount; }
    public function setTotalAmount(float $total_amount): void { $this->total_amount = $total_amount; }

    public function getInvoiceDate(): string { return $this->invoice_date; }
    public function setInvoiceDate(string $invoice_date): void { $this->invoice_date = $invoice_date; }

    public function getDueDate(): string { return $this->due_date; }
    public function setDueDate(string $due_date): void { $this->due_date = $due_date; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getPdfPath(): ?string { return $this->pdf_path; }
    public function setPdfPath(?string $pdf_path): void { $this->pdf_path = $pdf_path; }
}