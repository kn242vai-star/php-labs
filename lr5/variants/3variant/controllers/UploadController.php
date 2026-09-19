<?php

class UploadController extends PageController
{
    private string $uploadDir;
    private array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private int $maxSize = 5 * 1024 * 1024; // 5 MB
    private bool $canCheckMime;

    public function __construct()
    {
        parent::__construct();
        $this->uploadDir = DATA_DIR . '/uploads';
        $this->canCheckMime = class_exists('finfo') || function_exists('mime_content_type') || function_exists('getimagesize');

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function action_index(): void
    {
        if (!$this->canAccessAdminTools()) {
            $_SESSION['flash_error'] = 'Доступ до завантаження файлів доступний лише в режимі адміністратора.';
            $this->redirect('index/main');
            return;
        }

        $message = '';
        $error = '';
        $movieOptions = $this->getMovieOptions();

        if ($this->request->isPost() && isset($_FILES['image'])) {
            $file = $_FILES['image'];
            $selectedMovieId = (int)($this->request->post('movie_id', 0));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Помилка завантаження файлу (код: ' . $file['error'] . ').';
            } elseif ($file['size'] > $this->maxSize) {
                $error = 'Максимальний розмір файлу: 5 МБ.';
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $realType = null;

                if (!in_array($ext, $this->allowedExtensions, true)) {
                    $error = 'Дозволені формати: JPEG, PNG, GIF, WebP.';
                } else {
                    if ($this->canCheckMime) {
                        if (class_exists('finfo')) {
                            $finfo = new finfo(FILEINFO_MIME_TYPE);
                            $realType = $finfo->file($file['tmp_name']);
                        } elseif (function_exists('mime_content_type')) {
                            $realType = mime_content_type($file['tmp_name']);
                        } elseif (function_exists('getimagesize')) {
                            $info = getimagesize($file['tmp_name']);
                            $realType = $info[2] ? image_type_to_mime_type($info[2]) : null;
                        }
                    }

                    if ($realType !== null && !in_array($realType, $this->allowedMimeTypes, true)) {
                        $error = 'Дозволені формати: JPEG, PNG, GIF, WebP.';
                    }
                }
            }
            if ($error === '' && isset($ext)) {
                $safeName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = $this->uploadDir . '/' . $safeName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $posterPath = 'data/uploads/' . $safeName;
                    if ($selectedMovieId > 0) {
                        $this->assignPosterToMovie($selectedMovieId, $posterPath);
                    }
                    $message = 'Зображення "' . htmlspecialchars($file['name']) . '" завантажено!';
                } else {
                    $error = 'Не вдалося зберегти файл.';
                }
            }
        }

        $images = $this->getImages();

        $this->render('upload/index', [
            'images' => $images,
            'movieOptions' => $movieOptions,
            'message' => $message,
            'error' => $error,
        ], 'Завантаження зображень');
    }

    public function action_delete(): void
    {
        if (!$this->canAccessAdminTools()) {
            $_SESSION['flash_error'] = 'Доступ до завантаження файлів доступний лише в режимі адміністратора.';
            $this->redirect('index/main');
            return;
        }

        if ($this->request->isPost()) {
            $image = basename($this->request->post('image') ?? '');
            $target = $this->uploadDir . '/' . $image;

            if ($image === '' || !in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), $this->allowedExtensions, true)) {
                $_SESSION['flash_error'] = 'Некоректна назва зображення.';
            } elseif (!is_file($target)) {
                $_SESSION['flash_error'] = 'Файл не знайдено.';
            } elseif (!unlink($target)) {
                $_SESSION['flash_error'] = 'Не вдалося видалити зображення.';
            } else {
                $this->clearPosterForImage($image);
                $_SESSION['flash_success'] = 'Зображення видалено.';
            }
        }

        $this->redirect('upload/index');
    }

    public function action_rename(): void
    {
        if (!$this->canAccessAdminTools()) {
            $_SESSION['flash_error'] = 'Доступ до завантаження файлів доступний лише в режимі адміністратора.';
            $this->redirect('index/main');
            return;
        }

        if ($this->request->isPost()) {
            $currentName = basename($this->request->post('current_name') ?? '');
            $newNameRaw = trim($this->request->post('new_name') ?? '');
            $currentPath = $this->uploadDir . '/' . $currentName;
            $oldExt = strtolower(pathinfo($currentName, PATHINFO_EXTENSION));

            if ($currentName === '' || !is_file($currentPath)) {
                $_SESSION['flash_error'] = 'Файл не знайдено.';
            } elseif ($newNameRaw === '') {
                $_SESSION['flash_error'] = 'Вкажіть нову назву файлу.';
            } else {
                $newBase = basename($newNameRaw);
                $newExt = strtolower(pathinfo($newBase, PATHINFO_EXTENSION));

                if ($newExt === '') {
                    $newBase .= '.' . $oldExt;
                    $newExt = $oldExt;
                }

                if (!in_array($newExt, $this->allowedExtensions, true)) {
                    $_SESSION['flash_error'] = 'Розширення файлу має бути JPEG, PNG, GIF або WebP.';
                } elseif ($newExt !== $oldExt) {
                    $_SESSION['flash_error'] = 'Змінювати розширення файлу не можна.';
                } else {
                    $newBase = preg_replace('/[^\p{L}\p{N}_\-\. ]/u', '_', $newBase);
                    $newBase = preg_replace('/\s+/', ' ', $newBase);
                    if ($newBase === $currentName) {
                        $_SESSION['flash_error'] = 'Нова назва така сама, як існуюча.';
                    } else {
                        $newPath = $this->uploadDir . '/' . $newBase;
                        if (is_file($newPath)) {
                            $_SESSION['flash_error'] = 'Файл з такою назвою вже існує.';
                        } elseif (!rename($currentPath, $newPath)) {
                            $_SESSION['flash_error'] = 'Не вдалося перейменувати файл.';
                        } else {
                            $this->syncPosterFilename($currentName, $newBase);
                            $_SESSION['flash_success'] = 'Зображення перейменовано.';
                        }
                    }
                }
            }
        }

        $this->redirect('upload/index');
    }

    private function canAccessAdminTools(): bool
    {
        return !empty($_SESSION['is_admin']) && !empty($_SESSION['admin_mode']);
    }

    private function getMovieOptions(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT id, title FROM movies ORDER BY title ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function assignPosterToMovie(int $movieId, string $posterPath): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE movies SET poster_url = :poster_url WHERE id = :id');
        $stmt->execute([
            ':poster_url' => $posterPath,
            ':id' => $movieId,
        ]);
    }

    private function clearPosterForImage(string $imageName): void
    {
        $db = Database::getInstance();
        $posterPath = 'data/uploads/' . basename($imageName);
        $stmt = $db->prepare('UPDATE movies SET poster_url = :empty WHERE poster_url = :poster_url');
        $stmt->execute([
            ':empty' => '',
            ':poster_url' => $posterPath,
        ]);
    }

    private function syncPosterFilename(string $oldName, string $newName): void
    {
        $db = Database::getInstance();
        $oldPath = 'data/uploads/' . $oldName;
        $newPath = 'data/uploads/' . $newName;
        $stmt = $db->prepare('UPDATE movies SET poster_url = :new_path WHERE poster_url = :old_path');
        $stmt->execute([
            ':new_path' => $newPath,
            ':old_path' => $oldPath,
        ]);
    }

    private function getImages(): array
    {
        $images = [];
        $files = glob($this->uploadDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $movieMap = [];

        $db = Database::getInstance();
        $movies = $db->query('SELECT id, title, poster_url FROM movies ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($movies as $movie) {
            $movieMap[trim((string)($movie['poster_url'] ?? ''))] = $movie['title'];
        }

        if ($files) {
            rsort($files);
            foreach ($files as $file) {
                $baseName = basename($file);
                $url = 'data/uploads/' . $baseName;
                $images[] = [
                    'name' => $baseName,
                    'display_name' => pathinfo($baseName, PATHINFO_FILENAME),
                    'extension' => strtolower(pathinfo($baseName, PATHINFO_EXTENSION)),
                    'url' => $url,
                    'size' => filesize($file),
                    'date' => date('Y-m-d H:i', filemtime($file)),
                    'movie_title' => $movieMap[$url] ?? null,
                ];
            }
        }

        return $images;
    }
}
