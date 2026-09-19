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
        $search = trim((string)($this->request->get('search', '')));
        $genre = trim((string)($this->request->get('genre', '')));
        $sort = trim((string)($this->request->get('sort', 'newest')));

        $sql = 'SELECT id, title, director, genre, year, duration_min, poster_url FROM movies WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (title LIKE :search OR director LIKE :search OR genre LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($genre !== '') {
            $sql .= ' AND genre = :genre';
            $params[':genre'] = $genre;
        }

        switch ($sort) {
            case 'oldest':
                $sql .= ' ORDER BY year ASC, id ASC';
                break;
            case 'title':
                $sql .= ' ORDER BY title ASC';
                break;
            case 'duration':
                $sql .= ' ORDER BY duration_min DESC';
                break;
            case 'newest':
            default:
                $sql .= ' ORDER BY year DESC, id DESC';
                break;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $movies = $stmt->fetchAll();

        $genreStmt = $this->db->query('SELECT DISTINCT genre FROM movies WHERE genre <> "" ORDER BY genre ASC');
        $genres = $genreStmt->fetchAll(PDO::FETCH_COLUMN);

        $this->render('movie/list', [
            'movies' => $movies,
            'genres' => $genres,
            'search' => $search,
            'genre' => $genre,
            'sort' => $sort,
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

        if (empty($_SESSION['is_admin']) || empty($_SESSION['admin_mode'])) {
            $_SESSION['flash_error'] = 'Видалення фільмів доступне лише в режимі адміністратора.';
            $this->redirect('movie/list');
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
