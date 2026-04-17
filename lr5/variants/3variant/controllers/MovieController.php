<?php

class MovieController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_list(): void
    {
        $stmt = $this->db->query('SELECT * FROM movies ORDER BY id DESC');
        $movies = $stmt->fetchAll();

        $this->render('movie/list', [
            'movies' => $movies,
        ], 'Фільми');
    }

    public function action_create(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        $errors = [];
        $old = [];

        if ($this->request->isPost()) {
            $old = $this->request->allPost();
            $errors = $this->validate($old);

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'INSERT INTO movies (title, director, genre, year, duration_min)
                     VALUES (:title, :director, :genre, :year, :duration_min)'
                );
                $stmt->execute([
                    ':title' => trim($old['title']),
                    ':director' => trim($old['director']),
                    ':genre' => trim($old['genre'] ?? ''),
                    ':year' => (int)($old['year']),
                    ':duration_min' => (int)($old['duration_min']),
                ]);

                $_SESSION['flash_success'] = 'Фільм "' . trim($old['title']) . '" додано!';
                $this->redirect('movie/list');
                return;
            }
        }

        $this->render('movie/create', [
            'errors' => $errors,
            'old' => $old,
        ], 'Додати фільм');
    }

    public function action_edit(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        $id = (int)$this->request->get('id', 0);

        if ($id <= 0) {
            $this->redirect('movie/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM movies WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $movie = $stmt->fetch();

        if (!$movie) {
            $this->redirect('movie/list');
            return;
        }

        $errors = [];

        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            $errors = $this->validate($data);

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'UPDATE movies SET title = :title, director = :director, genre = :genre,
                     year = :year, duration_min = :duration_min WHERE id = :id'
                );
                $stmt->execute([
                    ':title' => trim($data['title']),
                    ':director' => trim($data['director']),
                    ':genre' => trim($data['genre'] ?? ''),
                    ':year' => (int)($data['year']),
                    ':duration_min' => (int)($data['duration_min']),
                    ':id' => $id,
                ]);

                $_SESSION['flash_success'] = 'Фільм оновлено!';
                $this->redirect('movie/list');
                return;
            }

            $movie = array_merge($movie, $data);
        }

        $this->render('movie/edit', [
            'movie' => $movie,
            'errors' => $errors,
        ], 'Редагувати фільм');
    }

    public function action_delete(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        if ($this->request->isPost()) {
            $id = (int)$this->request->post('id', 0);

            if ($id > 0) {
                $stmt = $this->db->prepare('DELETE FROM movies WHERE id = :id');
                $stmt->execute([':id' => $id]);
                $_SESSION['flash_success'] = 'Фільм видалено!';
            }
        }

        $this->redirect('movie/list');
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (trim($data['title'] ?? '') === '') {
            $errors['title'] = 'Назва фільму є обов\'язковою.';
        }

        if (trim($data['director'] ?? '') === '') {
            $errors['director'] = 'Режисер є обов\'язковим.';
        }

        $year = $data['year'] ?? '';
        if ($year !== '' && (!is_numeric($year) || (int)$year < 1888 || (int)$year > date('Y') + 1)) {
            $errors['year'] = 'Рік має бути дійсним роком випуску.';
        }

        $duration = $data['duration_min'] ?? '';
        if ($duration !== '' && (!is_numeric($duration) || (int)$duration < 1)) {
            $errors['duration_min'] = 'Тривалість має бути додатнім числом хвилин.';
        }

        return $errors;
    }
}
