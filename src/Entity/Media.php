<?php

declare(strict_types=1);

namespace MyApp\Entity;


class Media
{
    private ?int $id;
    private string $file_name;
    private string $file_path;
    private string $file_type;
    private ?int $file_size;
    private string $created_at;

    public function __construct(?int $id, string $file_name, string $file_path, string $file_type, ?int $file_size, string $created_at)
    {
        $this->id = $id;
        $this->file_name = $file_name;
        $this->file_path = $file_path;
        $this->file_type = $file_type;
        $this->file_size = $file_size;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getFileName(): string { return $this->file_name; }
    public function setFileName(string $file_name): void { $this->file_name = $file_name; }
    public function getFilePath(): string { return $this->file_path; }
    public function setFilePath(string $file_path): void { $this->file_path = $file_path; }
    public function getFileType(): string { return $this->file_type; }
    public function setFileType(string $file_type): void { $this->file_type = $file_type; }
    public function getFileSize(): ?int { return $this->file_size; }
    public function setFileSize(?int $file_size): void { $this->file_size = $file_size; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }
}
