<?php

class AdminController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_login(): void
    {
        if (!empty($_SESSION['is_admin'])) {
            $this->redirect('admin/dashboard');
            return;
        }

        $error = '';

        if ($this->request->isPost()) {
            $login = trim((string)$this->request->post('login', ''));
            $password = (string)$this->request->post('password', '');

            if ($login === '' || $password === '') {
                $error = 'Введіть логін і пароль адміністратора.';
            } else {
                $stmt = $this->db->prepare('SELECT * FROM users WHERE login = :login AND role = :role LIMIT 1');
                $stmt->execute([':login' => $login, ':role' => 'admin']);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['user_login'] = $user['login'];
                    $_SESSION['is_admin'] = true;
                    $_SESSION['admin_mode'] = true;
                    $this->redirect('admin/dashboard');
                    return;
                }

                $error = 'Невірний логін або пароль адміністратора.';
            }
        }

        $this->render('admin/login', ['error' => $error], 'Адмін-вхід');
    }

    public function action_dashboard(): void
    {
        if (empty($_SESSION['is_admin']) || empty($_SESSION['admin_mode'])) {
            $this->redirect('index/main');
            return;
        }

        $stats = [
            'movies' => (int)$this->db->query('SELECT COUNT(*) FROM movies')->fetchColumn(),
            'users' => (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'bookings' => (int)$this->db->query('SELECT COUNT(*) FROM reservations')->fetchColumn(),
            'shows' => (int)$this->db->query('SELECT COUNT(*) FROM shows')->fetchColumn(),
            'uploads' => count(array_filter(glob(ROOT_DIR . '/data/uploads/*'), 'is_file')),
        ];

        $modules = [
            ['title' => 'Користувачі', 'route' => 'auth/profile', 'description' => 'Перегляд і керування профілями користувачів.'],
            ['title' => 'Фільми', 'route' => 'movie/list', 'description' => 'CRUD для кіноафіші та жанрів.'],
            ['title' => 'Квитки', 'route' => 'ticket/booking', 'description' => 'Контроль бронювання місць у залі.'],
            ['title' => 'Гостьова книга', 'route' => 'guestbook/index', 'description' => 'Відповіді від відвідувачів сайту.'],
            ['title' => 'Файли', 'route' => 'upload/index', 'description' => 'Завантаження та керування медіафайлами.'],
            ['title' => 'Адмінські параметри', 'route' => 'settings/color', 'description' => 'Передбачені налаштування дизайну системи.'],
        ];

        $this->render('admin/dashboard', [
            'stats' => $stats,
            'modules' => $modules,
        ], 'Адмін-панель');
    }

    public function action_toggle_mode(): void
    {
        if (empty($_SESSION['is_admin'])) {
            $this->redirect('admin/login');
            return;
        }

        $_SESSION['admin_mode'] = empty($_SESSION['admin_mode']);

        if (!empty($_SESSION['admin_mode'])) {
            $this->redirect('admin/dashboard');
            return;
        }

        $this->redirect('index/main');
    }

    public function action_logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_login'], $_SESSION['is_admin'], $_SESSION['admin_mode']);
        session_regenerate_id(true);
        $this->redirect('index/main');
    }

    public function action_json(): void
    {
        if (empty($_SESSION['is_admin']) || empty($_SESSION['admin_mode'])) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['error' => 'Неавторизовано'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $module = (string)$this->request->get('module', 'overview');
        $response = ['module' => $module];

        switch ($module) {
            case 'movies':
                $response['items'] = $this->db->query('SELECT id, title, director, genre, year, duration_min FROM movies ORDER BY year DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'users':
                $response['items'] = $this->db->query('SELECT id, login, email, first_name, last_name, role FROM users ORDER BY id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'bookings':
                $response['items'] = $this->db->query('SELECT r.id, m.title AS movie_title, r.seat, u.login, r.reserved_at FROM reservations r LEFT JOIN users u ON u.id = r.user_id LEFT JOIN shows s ON s.id = r.show_id LEFT JOIN movies m ON m.id = s.movie_id ORDER BY r.id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'overview':
            default:
                $response['items'] = [
                    'movies' => (int)$this->db->query('SELECT COUNT(*) FROM movies')->fetchColumn(),
                    'users' => (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
                    'bookings' => (int)$this->db->query('SELECT COUNT(*) FROM reservations')->fetchColumn(),
                    'shows' => (int)$this->db->query('SELECT COUNT(*) FROM shows')->fetchColumn(),
                ];
                break;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
