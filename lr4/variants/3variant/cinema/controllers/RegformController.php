<?php

class RegformController extends PageController
{
    public function action_form(): void
    {
        $errors = [];
        $old = [];

        if ($this->request->isPost()) {
            $old = $this->request->allPost();
            $errors = $this->validate($old);

            if (empty($errors)) {
                $_SESSION['reg_success'] = true;
                $_SESSION['reg_data'] = [
                    'first_name' => $old['first_name'],
                    'last_name' => $old['last_name'],
                    'email' => $old['email'],
                ];
                $this->redirect('regform/done');
                return;
            }
        }

        $this->render('regform/form', [
            'errors' => $errors,
            'old' => $old,
        ], 'Реєстрація');
    }

    public function action_done(): void
    {
        if (empty($_SESSION['reg_success'])) {
            $this->redirect('regform/form');
            return;
        }

        $data = $_SESSION['reg_data'] ?? [];
        unset($_SESSION['reg_success'], $_SESSION['reg_data']);

        $this->render('regform/done', ['regData' => $data], 'Реєстрація успішна');
    }

    private function validate(array $data): array
    {
        $errors = [];

        $firstName = trim($data['first_name'] ?? '');
        if ($firstName === '') {
            $errors['first_name'] = "Ім'я є обов'язковим.";
        } elseif (!preg_match('/^[A-Za-z]+$/u', $firstName)) {
            $errors['first_name'] = "Ім'я має містити лише англійські літери.";
        }

        $lastName = trim($data['last_name'] ?? '');
        if ($lastName === '') {
            $errors['last_name'] = "Прізвище є обов'язковим.";
        } elseif (!preg_match('/^[A-Za-z]+$/u', $lastName)) {
            $errors['last_name'] = 'Прізвище має містити лише англійські літери.';
        }

        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $errors['email'] = "E-mail є обов'язковим.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Невірний формат E-mail.';
        }

        $password = $data['password'] ?? '';
        if ($password === '') {
            $errors['password'] = "Пароль є обов'язковим.";
        } elseif ((function_exists('mb_strlen') ? mb_strlen($password) : strlen($password)) < 6) {
            $errors['password'] = 'Пароль має бути не менше 6 символів.';
        }

        $passwordConfirm = $data['password_confirm'] ?? '';
        if ($passwordConfirm === '') {
            $errors['password_confirm'] = "Підтвердження пароля є обов'язковим.";
        } elseif ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Паролі не збігаються.';
        }

        $gender = $data['gender'] ?? '';
        if ($gender === '') {
            $errors['gender'] = "Стать є обов'язковою.";
        }

        $dobDay = trim($data['dob_day'] ?? '');
        $dobMonth = trim($data['dob_month'] ?? '');
        $dobYear = trim($data['dob_year'] ?? '');

        if ($dobDay === '' || $dobMonth === '' || $dobYear === '') {
            $errors['dob_day'] = 'Дата народження є обов\'язковою.';
        } elseif (!is_numeric($dobDay) || $dobDay < 1 || $dobDay > 31) {
            $errors['dob_day'] = 'День має бути від 1 до 31.';
        } elseif (!is_numeric($dobMonth) || $dobMonth < 1 || $dobMonth > 12) {
            $errors['dob_month'] = 'Місяць має бути від 1 до 12.';
        } elseif (!is_numeric($dobYear) || strlen($dobYear) !== 4) {
            $errors['dob_year'] = 'Рік має бути 4 цифри.';
        } else {
            // Перевірка коректності дати
            if (!checkdate((int)$dobMonth, (int)$dobDay, (int)$dobYear)) {
                $errors['dob_day'] = 'Некоректна дата.';
            } else {
                $dob = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);
                $age = $this->calculateAge($dob);
                if ($age < 0) {
                    $errors['dob_day'] = 'Дата народження не може бути в майбутньому.';
                } elseif (($gender === 'male' && $age < 21) || ($gender === 'female' && $age < 18)) {
                    $errors['dob_day'] = 'Вік не відповідає вимогам (чоловіки від 21, жінки від 18).';
                }
            }
        }

        $city = $data['city'] ?? '';
        if ($city === '') {
            $errors['city'] = "Місто є обов'язковим.";
        }

        $about = trim($data['about'] ?? '');
        if ($about !== '' && (function_exists('mb_strlen') ? mb_strlen($about) : strlen($about)) > 500) {
            $errors['about'] = 'Максимум 500 символів.';
        }

        return $errors;
    }

    private function calculateAge(string $dob): int
    {
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        return $age;
    }
}
