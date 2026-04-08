<?php
/**
 * Клас Doctor — модель лікаря
 *
 * Використовується у всіх завданнях ЛР3.
 */

class Doctor
{
    public string $name;
    public string $specialization;
    public string $licenseNumber;

    /**
     * Завдання 3: Конструктор — задає початкові значення властивостей
     */
    public function __construct(string $name = '', string $specialization = '', string $licenseNumber = '')
    {
        $this->name = $name;
        $this->specialization = $specialization;
        $this->licenseNumber = $licenseNumber;
    }

    /**
     * Завдання 2: Метод getInfo()
     * Повертає форматований рядок з даними лікаря
     */
    public function getInfo(): string
    {
        return "Лікар: {$this->name}, Спеціалізація: {$this->specialization}, Ліцензія: {$this->licenseNumber}";
    }

    /**
     * Завдання 4: Магічний метод __clone()
     * Викликається автоматично при клонуванні об'єкта
     */
    public function __clone(): void
    {
        $this->name = 'Лікар';
        $this->specialization = '';
        $this->licenseNumber = '';
    }
}